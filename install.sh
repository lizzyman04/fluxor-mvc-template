#!/usr/bin/env bash
# install.sh — Fluxor MVC Template installer
# Usage: curl -fsSL https://raw.githubusercontent.com/lizzyman04/fluxor-mvc-template/main/install.sh | bash

set -euo pipefail

REPO="lizzyman04/fluxor-mvc-template"
BRANCH="main"
ARCHIVE_URL="https://github.com/${REPO}/archive/refs/heads/${BRANCH}.tar.gz"
EXTRACTED_DIR="fluxor-mvc-template-${BRANCH}"
TMPDIR_BASE="$(mktemp -d)"
BACKUP_TS="$(date +%Y%m%d_%H%M%S)"

# ── Colors ────────────────────────────────────────────────────────────────────
if [[ -t 1 ]]; then
    RED='\033[0;31m' GREEN='\033[0;32m' YELLOW='\033[1;33m'
    BLUE='\033[0;34m' BOLD='\033[1m' NC='\033[0m'
else
    RED='' GREEN='' YELLOW='' BLUE='' BOLD='' NC=''
fi

info()    { echo -e "  ${BLUE}→${NC}  $*"; }
ok()      { echo -e "  ${GREEN}✓${NC}  $*"; }
warn()    { echo -e "  ${YELLOW}!${NC}  $*"; }
die()     { echo -e "  ${RED}✗${NC}  $*" >&2; exit 1; }

# ── Cleanup ───────────────────────────────────────────────────────────────────
cleanup() { rm -rf "$TMPDIR_BASE"; }
trap cleanup EXIT

# ── Helpers ───────────────────────────────────────────────────────────────────

require_cmd() {
    command -v "$1" >/dev/null 2>&1 || die "'$1' is required but not installed. Aborting."
}

# Prompt via /dev/tty so it works when stdin is consumed by the pipe
prompt_yn() {
    local answer
    if [[ -t 0 ]]; then
        read -r -p "  $* [y/N]: " answer
    else
        read -r -p "  $* [y/N]: " answer </dev/tty
    fi
    [[ "$answer" =~ ^[Yy]$ ]]
}

safe_backup() {
    local target="$1"
    if [[ -e "$target" ]]; then
        local dest="${target}.bak.${BACKUP_TS}"
        cp -r "$target" "$dest"
        warn "Backed up  '$target'  →  '$dest'"
    fi
}

# Add keys from .env.example to .env only when the key is absent
merge_env() {
    local example="$1"
    [[ -f ".env" ]] || cp "$example" .env

    while IFS= read -r line; do
        [[ "$line" =~ ^[[:space:]]*# ]] && continue
        [[ -z "${line// }" ]] && continue

        local key
        key="$(printf '%s' "$line" | cut -d'=' -f1 | xargs)"
        [[ -z "$key" ]] && continue

        if ! grep -qE "^${key}[[:space:]]*=" .env 2>/dev/null; then
            printf '\n%s' "$line" >> .env
        fi
    done < "$example"
}

# Write a temp PHP script that merges two composer.json files
# argv[1] = current project composer.json  (written in-place)
# argv[2] = template composer.json
write_composer_merger() {
    cat > "$TMPDIR_BASE/merge_composer.php" << 'PHP'
<?php
[$_, $projectFile, $templateFile] = $argv;

$project  = json_decode(file_get_contents($projectFile),  true) ?: [];
$template = json_decode(file_get_contents($templateFile), true);

$merged = $template;

// Preserve the user's identity fields
foreach (['name', 'description', 'authors', 'keywords', 'homepage'] as $field) {
    if (!empty($project[$field])) {
        $merged[$field] = $project[$field];
    }
}

// Merge dependency sections (template packages take priority for shared keys)
$merged['require']     = array_merge($project['require']     ?? [], $template['require']);
$merged['require-dev'] = array_merge($project['require-dev'] ?? [], $template['require-dev'] ?? []);

// Merge scripts (template additions augment rather than replace user scripts)
$merged['scripts'] = array_merge($project['scripts'] ?? [], $template['scripts']);

// Template autoload is authoritative
$merged['autoload'] = $template['autoload'];
if (!empty($project['autoload-dev'])) {
    $merged['autoload-dev'] = $project['autoload-dev'];
}

file_put_contents(
    $projectFile,
    json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL
);
echo "composer.json merged successfully.\n";
PHP
}

merge_composer() {
    local tpl_json="$1"
    if [[ ! -f "composer.json" ]]; then
        cp "$tpl_json" composer.json
        return
    fi
    write_composer_merger
    php "$TMPDIR_BASE/merge_composer.php" composer.json "$tpl_json"
}

# Copy a directory with no-clobber for files that already exist
copy_no_clobber() {
    local src="$1" dst="$2"
    mkdir -p "$dst"
    cp -rn "${src}/." "$dst/"
}

# ── Main ──────────────────────────────────────────────────────────────────────

main() {
    echo ""
    echo -e "  ${BOLD}Fluxor MVC Template — Installer${NC}"
    echo    "  ════════════════════════════════"
    echo ""

    require_cmd php
    require_cmd composer

    # ── Detect existing Fluxor project ────────────────────────────────────────
    local is_update=false
    if [[ -f "composer.json" ]] && \
       { grep -q '"lizzyman04/fluxor"' composer.json 2>/dev/null || [[ -d "app/router" ]]; }; then
        warn "Existing Fluxor project detected in: $(pwd)"
        if prompt_yn "Update this project? (replaced dirs will be backed up)"; then
            is_update=true
        else
            echo "  Aborted."; exit 0
        fi
    fi

    # ── Download template ─────────────────────────────────────────────────────
    info "Downloading template (${REPO}@${BRANCH})..."
    local archive="${TMPDIR_BASE}/template.tar.gz"

    if command -v curl >/dev/null 2>&1; then
        curl -fsSL "$ARCHIVE_URL" -o "$archive"
    elif command -v wget >/dev/null 2>&1; then
        wget -qO "$archive" "$ARCHIVE_URL"
    else
        die "curl or wget is required to download the template."
    fi

    tar -xzf "$archive" -C "$TMPDIR_BASE"
    local tpl="${TMPDIR_BASE}/${EXTRACTED_DIR}"
    [[ -d "$tpl" ]] || die "Could not locate extracted template at '${tpl}'."
    ok "Template downloaded and extracted."

    # ── Replace core directories ──────────────────────────────────────────────
    for dir in app src db; do
        if [[ ! -d "$tpl/$dir" ]]; then
            warn "Template is missing '$dir/' — skipping."
            continue
        fi
        if $is_update && [[ -d "$dir" ]]; then
            safe_backup "$dir"
        fi
        info "Installing ${dir}/..."
        rm -rf "$dir"
        cp -r "$tpl/$dir" .
        ok "Installed ${dir}/"
    done

    # ── Merge public/ (preserve existing user files) ──────────────────────────
    if [[ -d "$tpl/public" ]]; then
        info "Merging public/ (existing files are preserved)..."
        mkdir -p public

        for file in index.php .htaccess; do
            local src_file="${tpl}/public/${file}"
            [[ -f "$src_file" && ! -f "public/$file" ]] && cp "$src_file" "public/$file"
        done

        [[ -d "$tpl/public/assets"  ]] && copy_no_clobber "$tpl/public/assets"  "public/assets"
        [[ -d "$tpl/public/uploads" ]] && copy_no_clobber "$tpl/public/uploads" "public/uploads"

        ok "public/ merged."
    fi

    # ── Merge composer.json ───────────────────────────────────────────────────
    info "Merging composer.json..."
    merge_composer "$tpl/composer.json"
    ok "composer.json updated."

    # ── Update .env ───────────────────────────────────────────────────────────
    info "Updating .env..."
    local env_example="${tpl}/.env.example"
    if [[ -f "$env_example" ]]; then
        cp "$env_example" .env.example
        merge_env "$env_example"
        ok ".env updated (existing values preserved)."
    else
        warn ".env.example not found in template — skipping."
    fi

    # ── Install Composer dependencies ─────────────────────────────────────────
    info "Installing Composer dependencies..."
    composer update --no-interaction --no-progress
    ok "Dependencies installed."

    # ── Run migrations ────────────────────────────────────────────────────────
    info "Running database migrations..."
    if composer migrate; then
        ok "Migrations complete."
    else
        warn "Migration failed. Configure DB credentials in .env then run:  composer migrate"
    fi

    # ── Run seeders ───────────────────────────────────────────────────────────
    info "Running database seeders..."
    if composer seed; then
        ok "Seeders complete."
    else
        warn "Seeder failed. Run manually:  composer seed"
    fi

    # ── Done ──────────────────────────────────────────────────────────────────
    echo ""
    echo -e "  ${GREEN}${BOLD}Installation complete!${NC}"
    echo ""
    echo "  Next steps:"
    echo "    1. Edit .env with your database credentials"
    echo "    2. Start the dev server:  composer dev"
    echo "    3. Open in browser:       http://localhost:8000"
    echo ""
}

main "$@"
