#Requires -Version 5.1
# install.ps1 — Fluxor MVC Template installer (Windows)
# Usage: irm https://raw.githubusercontent.com/lizzyman04/fluxor-mvc-template/main/install.ps1 | iex

[CmdletBinding()]
param()

$ErrorActionPreference = "Stop"
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

$Repo        = "lizzyman04/fluxor-mvc-template"
$Branch      = "main"
$ArchiveUrl  = "https://github.com/$Repo/archive/refs/heads/$Branch.zip"
$ExtractName = "fluxor-mvc-template-$Branch"
$TmpDir      = Join-Path ([System.IO.Path]::GetTempPath()) ([System.IO.Path]::GetRandomFileName())
$BackupTs    = Get-Date -Format "yyyyMMdd_HHmmss"

# ── Output helpers ────────────────────────────────────────────────────────────

function Write-Step { param([string]$msg) Write-Host "  -> $msg" -ForegroundColor Cyan }
function Write-Ok   { param([string]$msg) Write-Host "  v  $msg" -ForegroundColor Green }
function Write-Warn { param([string]$msg) Write-Host "  !  $msg" -ForegroundColor Yellow }
function Write-Fail { param([string]$msg) Write-Host "  x  $msg" -ForegroundColor Red; exit 1 }

# ── Helpers ───────────────────────────────────────────────────────────────────

function Test-Command {
    param([string]$Name)
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        Write-Fail "'$Name' is required but not installed. Aborting."
    }
}

function Invoke-Backup {
    param([string]$Path)
    if (Test-Path $Path) {
        $dest = "${Path}.bak.${BackupTs}"
        Copy-Item -Recurse -Force $Path $dest
        Write-Warn "Backed up  '$Path'  ->  '$dest'"
    }
}

# Merge .env.example keys into .env without overwriting existing values
function Merge-Env {
    param([string]$ExamplePath)

    if (-not (Test-Path ".env")) {
        Copy-Item $ExamplePath ".env"
        return
    }

    $envContent = Get-Content ".env" -Raw -ErrorAction SilentlyContinue

    foreach ($line in (Get-Content $ExamplePath)) {
        $trimmed = $line.Trim()
        if ($trimmed -match "^#" -or [string]::IsNullOrWhiteSpace($trimmed)) { continue }

        $key = ($trimmed -split "=", 2)[0].Trim()
        if ([string]::IsNullOrEmpty($key)) { continue }

        if ($envContent -notmatch "(?m)^${key}\s*=") {
            Add-Content ".env" "`n$line"
        }
    }
}

# Write a temp PHP merger script and run it
function Merge-ComposerJson {
    param([string]$TemplatePath)

    if (-not (Test-Path "composer.json")) {
        Copy-Item $TemplatePath "composer.json"
        return
    }

    $mergerScript = Join-Path $TmpDir "merge_composer.php"

    $phpCode = @'
<?php
[$_, $projectFile, $templateFile] = $argv;

$project  = json_decode(file_get_contents($projectFile),  true) ?: [];
$template = json_decode(file_get_contents($templateFile), true);

$merged = $template;

foreach (['name', 'description', 'authors', 'keywords', 'homepage'] as $field) {
    if (!empty($project[$field])) {
        $merged[$field] = $project[$field];
    }
}

$merged['require']     = array_merge($project['require']     ?? [], $template['require']);
$merged['require-dev'] = array_merge($project['require-dev'] ?? [], $template['require-dev'] ?? []);
$merged['scripts']     = array_merge($project['scripts']     ?? [], $template['scripts']);
$merged['autoload']    = $template['autoload'];
if (!empty($project['autoload-dev'])) {
    $merged['autoload-dev'] = $project['autoload-dev'];
}

file_put_contents(
    $projectFile,
    json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL
);
echo "composer.json merged successfully.\n";
'@

    Set-Content -Path $mergerScript -Value $phpCode -Encoding UTF8

    # Use absolute paths so PHP can find both files from any working directory
    $absProject  = (Resolve-Path "composer.json").Path
    $absTemplate = (Resolve-Path $TemplatePath).Path
    & php $mergerScript $absProject $absTemplate
    if ($LASTEXITCODE -ne 0) { Write-Fail "Failed to merge composer.json." }
}

# Copy all files from $Src into $Dst without clobbering existing files
function Copy-NoClobber {
    param([string]$Src, [string]$Dst)
    New-Item -ItemType Directory -Force -Path $Dst | Out-Null
    Get-ChildItem -Recurse -File $Src | ForEach-Object {
        $rel = $_.FullName.Substring($Src.Length).TrimStart('\', '/')
        $target = Join-Path $Dst $rel
        if (-not (Test-Path $target)) {
            $targetDir = Split-Path $target -Parent
            New-Item -ItemType Directory -Force -Path $targetDir | Out-Null
            Copy-Item $_.FullName $target
        }
    }
}

# ── Main ──────────────────────────────────────────────────────────────────────

try {
    Write-Host ""
    Write-Host "  Fluxor MVC Template -- Installer" -ForegroundColor White -NoNewline
    Write-Host ""
    Write-Host "  ================================"
    Write-Host ""

    Test-Command "php"
    Test-Command "composer"

    $cwd = (Get-Location).Path

    # ── Detect existing Fluxor project ────────────────────────────────────────
    $isUpdate = $false
    $composerContent = if (Test-Path "composer.json") { Get-Content "composer.json" -Raw -ErrorAction SilentlyContinue } else { "" }
    $isFluxor = ($composerContent -match '"lizzyman04/fluxor"') -or (Test-Path "app\router")

    if ($isFluxor) {
        Write-Warn "Existing Fluxor project detected in: $cwd"
        $confirm = Read-Host "  Update this project? (replaced dirs will be backed up) [y/N]"
        if ($confirm -notmatch "^[Yy]$") { Write-Host "  Aborted."; exit 0 }
        $isUpdate = $true
    }

    # ── Download template ─────────────────────────────────────────────────────
    Write-Step "Downloading template ($Repo @ $Branch)..."
    New-Item -ItemType Directory -Force -Path $TmpDir | Out-Null
    $archive = Join-Path $TmpDir "template.zip"

    Invoke-WebRequest -Uri $ArchiveUrl -OutFile $archive -UseBasicParsing
    Expand-Archive -Path $archive -DestinationPath $TmpDir -Force

    $tpl = Join-Path $TmpDir $ExtractName
    if (-not (Test-Path $tpl)) {
        Write-Fail "Could not locate extracted template at '$tpl'."
    }
    Write-Ok "Template downloaded and extracted."

    # ── Replace core directories ──────────────────────────────────────────────
    foreach ($dir in @("app", "src", "db")) {
        $tplSub = Join-Path $tpl $dir
        if (-not (Test-Path $tplSub)) {
            Write-Warn "Template is missing '$dir\' -- skipping."
            continue
        }
        if ($isUpdate -and (Test-Path $dir)) {
            Invoke-Backup $dir
        }
        Write-Step "Installing $dir\..."
        if (Test-Path $dir) { Remove-Item -Recurse -Force $dir }
        Copy-Item -Recurse $tplSub $dir
        Write-Ok "Installed $dir\"
    }

    # ── Merge public\ (preserve existing user files) ──────────────────────────
    $tplPublic = Join-Path $tpl "public"
    if (Test-Path $tplPublic) {
        Write-Step "Merging public\ (existing files are preserved)..."
        New-Item -ItemType Directory -Force -Path "public" | Out-Null

        foreach ($file in @("index.php", ".htaccess")) {
            $src = Join-Path $tplPublic $file
            $dst = Join-Path "public" $file
            if ((Test-Path $src) -and (-not (Test-Path $dst))) {
                Copy-Item $src $dst
            }
        }

        $tplAssets  = Join-Path $tplPublic "assets"
        $tplUploads = Join-Path $tplPublic "uploads"
        if (Test-Path $tplAssets)  { Copy-NoClobber $tplAssets  "public\assets" }
        if (Test-Path $tplUploads) { Copy-NoClobber $tplUploads "public\uploads" }

        Write-Ok "public\ merged."
    }

    # ── Merge composer.json ───────────────────────────────────────────────────
    Write-Step "Merging composer.json..."
    Merge-ComposerJson (Join-Path $tpl "composer.json")
    Write-Ok "composer.json updated."

    # ── Update .env ───────────────────────────────────────────────────────────
    Write-Step "Updating .env..."
    $envExample = Join-Path $tpl ".env.example"
    if (Test-Path $envExample) {
        Copy-Item $envExample ".env.example" -Force
        Merge-Env $envExample
        Write-Ok ".env updated (existing values preserved)."
    } else {
        Write-Warn ".env.example not found in template -- skipping."
    }

    # ── Install Composer dependencies ─────────────────────────────────────────
    Write-Step "Installing Composer dependencies..."
    & composer update --no-interaction --no-progress
    if ($LASTEXITCODE -ne 0) { Write-Fail "composer update failed." }
    Write-Ok "Dependencies installed."

    # ── Run migrations ────────────────────────────────────────────────────────
    Write-Step "Running database migrations..."
    & composer migrate
    if ($LASTEXITCODE -eq 0) {
        Write-Ok "Migrations complete."
    } else {
        Write-Warn "Migration failed. Edit .env with DB credentials then run:  composer migrate"
    }

    # ── Run seeders ───────────────────────────────────────────────────────────
    Write-Step "Running database seeders..."
    & composer seed
    if ($LASTEXITCODE -eq 0) {
        Write-Ok "Seeders complete."
    } else {
        Write-Warn "Seeder failed. Run manually:  composer seed"
    }

    # ── Done ──────────────────────────────────────────────────────────────────
    Write-Host ""
    Write-Host "  Installation complete!" -ForegroundColor Green
    Write-Host ""
    Write-Host "  Next steps:"
    Write-Host "    1. Edit .env with your database credentials"
    Write-Host "    2. Start the dev server:  composer dev"
    Write-Host "    3. Open in browser:       http://localhost:8000"
    Write-Host ""

} finally {
    if (Test-Path $TmpDir) {
        Remove-Item -Recurse -Force $TmpDir -ErrorAction SilentlyContinue
    }
}
