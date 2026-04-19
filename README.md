# Fluxor MVC Template 🚀

**A complete, production-ready MVC template built on the Fluxor PHP Framework** - File-based routing, Cycle ORM, authentication, mailer, uploader, and beautiful views.

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net)
[![Fluxor Version](https://img.shields.io/badge/fluxor-%5E1.0-4f46e5.svg)](https://github.com/lizzyman04/fluxor)

## 📋 Table of Contents

1. [Overview](#-overview)
2. [Features](#-features)
3. [Requirements](#-requirements)
4. [Installation](#-installation)
5. [Project Structure](#-project-structure)
6. [Configuration](#-configuration)
7. [Database Setup](#-database-setup)
8. [File-Based Routing](#-file-based-routing)
9. [Controllers](#-controllers)
10. [Models & Cycle ORM](#-models--cycle-orm)
11. [Authentication](#-authentication)
12. [Views & Layouts](#-views--layouts)
13. [Mailer](#-mailer)
14. [Uploader](#-uploader)
15. [Core Helpers](#-core-helpers)
16. [API Reference](#-api-reference)
17. [Deployment](#-deployment)
18. [Troubleshooting](#-troubleshooting)
19. [License](#-license)

---

## 📖 Overview

**Fluxor MVC Template** is a complete, production-ready web application template built on top of the [Fluxor PHP Framework](https://github.com/lizzyman04/fluxor). It combines the elegance of file-based routing with the power of Cycle ORM, providing a solid foundation for building modern PHP applications.

Unlike traditional MVC templates, Fluxor MVC Template uses **file-based routing** inspired by Next.js, where your route structure is defined by your folder structure. This makes routes intuitive, maintainable, and self-documenting.

---

## ✨ Features

| Category | Features |
|----------|----------|
| **Architecture** | MVC pattern, PSR-4 autoloading, Service Container |
| **Routing** | File-based routing (Next.js style), dynamic parameters, route groups |
| **Database** | Cycle ORM, database migrations, MySQL/PostgreSQL/SQLite support |
| **Authentication** | Secure sessions, CSRF protection, "Remember Me", role-based access |
| **Views** | Layout system, sections, partials, asset management, XSS protection |
| **Email** | SMTP mailer with HTML templates |
| **File Upload** | Secure upload with hash naming, MIME validation, duplicate prevention |
| **Security** | CSRF tokens, XSS escaping, secure session handling, password hashing |
| **Development** | Hot reload, detailed error pages, debug mode |

---

## 📦 Requirements

- **PHP** >= 8.1
- **Composer** >= 2.0
- **Database**: MySQL 5.7+, PostgreSQL 10+, or SQLite 3+
- **Extensions**: PDO, JSON, Session, Fileinfo, OpenSSL

---

## 🚀 Installation

### Quick Start

```bash
# Clone the repository
git clone https://github.com/lizzyman04/fluxor-mvc-template.git my-app
cd my-app

# Install dependencies
composer install

# Copy environment configuration
cp .env.example .env

# Generate application key and configure database
# Edit .env with your database credentials

# Run database migrations
composer migrate

# Start the development server
composer dev
```

Visit `http://localhost:8000` in your browser.

### Development Commands

```bash
composer dev                 # Start development server
composer prod                # Start production server
composer migrate             # Run pending migrations
composer migrate:rollback    # Rollback last migration batch
composer migrate:rollback:all # Rollback all migrations
composer migrate:status      # Show migration status
composer seed                # Run all seeders
composer migration:create    # Scaffold a new migration class
composer test                # Run PHPUnit tests
```

---

## 📁 Project Structure

```
fluxor-mvc-template/
├── app/
│   ├── Core/                    # Core helpers
│   │   ├── Auth.php             # Authentication helper
│   │   ├── Mailer.php           # Email helper
│   │   ├── ORMHelper.php        # Cycle ORM helper
│   │   └── Uploader.php         # File upload helper
│   └── router/                  # File-based routes (Next.js style)
│       ├── index.php            # GET /
│       ├── about.php            # GET /about
│       ├── auth/                # Authentication routes
│       │   ├── login.php        # GET/POST /auth/login
│       │   ├── register.php     # GET/POST /auth/register
│       │   └── logout.php       # GET/POST /auth/logout
│       └── posts/               # Post management routes
│           ├── index.php        # GET /posts
│           ├── create.php       # GET/POST /posts/create
│           ├── [id]/            # Dynamic route
│           │   ├── index.php    # GET /posts/{id}
│           │   ├── edit.php     # GET/POST /posts/{id}/edit
│           │   └── delete.php   # POST /posts/{id}/delete
├── db/
│   ├── core/                    # Database core files
│   │   ├── bootstrap.php        # Database bootstrap
│   │   ├── connection.php       # Database connection config
│   │   ├── orm.php              # Cycle ORM factory
│   │   └── phinx.php            # Phinx migration configuration
│   ├── migrations/              # Phinx PHP migration classes
│   │   ├── 20240101000001_create_users_table.php
│   │   └── 20240101000002_create_posts_table.php
│   └── seeders/                 # Phinx seeder classes
│       └── DefaultUsersSeeder.php
├── public/
│   ├── index.php                # Front controller
│   ├── .htaccess                # Apache rewrite rules
│   ├── assets/                  # Static assets
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   └── uploads/                 # Uploaded files directory
├── src/
│   ├── Controllers/             # Application controllers
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   └── PostController.php
│   ├── Models/                  # Database models
│   │   ├── User.php
│   │   └── Post.php
│   └── Views/                   # View templates
│       ├── layouts/
│       │   └── main.php         # Main layout
│       ├── auth/                # Authentication views
│       │   ├── login.php
│       │   └── register.php
│       ├── posts/               # Post views
│       │   ├── index.php
│       │   ├── show.php
│       │   ├── create.php
│       │   └── edit.php
│       ├── errors/
│       │   └── 404.php          # 404 error page
│       └── home.php             # Homepage
├── .env.example                 # Environment configuration example
├── .gitignore                   # Git ignore rules
├── composer.json                # Composer dependencies
├── LICENSE                      # MIT License
└── README.md                    # This file
```

---

## ⚙️ Configuration

### Environment Variables (.env)

```env
# Application Configuration
APP_NAME="Fluxor App"
APP_ENV=development              # development, production, testing
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_KEY=base64:...

# Database Configuration
DB_CONNECTION=mysql              # mysql, pgsql, sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fluxor_db
DB_USERNAME=root
DB_PASSWORD=your_password

# Authentication Security
AUTH_SECRET_KEY=your-super-secret-key
AUTH_SESSION_EXPIRY=1800
AUTH_REMEMBER_EXPIRY=2592000

# Mail Configuration (optional)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Fluxor App"

# Upload Configuration (optional)
UPLOAD_MAX_SIZE=5242880          # 5MB
UPLOAD_ALLOWED_TYPES=jpg,jpeg,png,gif,webp,pdf
```

### Application Configuration

```php
// public/index.php
$app = new Fluxor\App();

$app->setConfig([
    'router_path' => __DIR__ . '/../app/router',
    'views_path' => __DIR__ . '/../src/Views',
]);

$app->run();
```

---

## 🗄️ Database Setup

Fluxor uses **[Phinx](https://phinx.org/)** for database migrations — a framework-agnostic migration tool with rollback support, migration tracking, and PHP-based migration classes.

### Running Migrations

```bash
# Run all pending migrations
composer migrate

# Check migration status
composer migrate:status

# Rollback last batch
composer migrate:rollback

# Rollback everything (reset)
composer migrate:rollback:all

# Seed default data
composer seed

# Scaffold a new migration
composer migration:create MyMigrationName
```

### Creating a Migration

```php
// db/migrations/20240101000003_add_avatar_to_users.php
use Phinx\Migration\AbstractMigration;

final class AddAvatarToUsers extends AbstractMigration
{
    public function change(): void
    {
        $this->table('users')
            ->addColumn('avatar', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->update();
    }
}
```

Phinx automatically tracks which migrations have run in the `phinx_migrations` table and generates `up()`/`down()` rollback logic from `change()`.

### Default Users

After running `composer migrate && composer seed`, two users are created:

| Name | Email | Password | Role |
|------|-------|----------|------|
| Admin User | `admin@example.com` | `admin123` | admin |
| Demo User | `demo@example.com` | `demo123` | user |

> **Security**: Change these credentials immediately in production.

---

## 🛣️ File-Based Routing

Fluxor uses **file-based routing** inspired by Next.js. Your route structure is defined by your folder structure.

### Basic Routes

| File | URL |
|------|-----|
| `app/router/index.php` | `/` |
| `app/router/about.php` | `/about` |
| `app/router/contact.php` | `/contact` |

### Dynamic Routes

Use `[param]` syntax for dynamic segments:

```
app/router/users/[id].php          # /users/123
app/router/posts/[slug]/edit.php   # /posts/my-post/edit
```

```php
// app/router/users/[id].php
use Fluxor\Flow;
use Fluxor\Response;

Flow::GET()->do(function($req) {
    $userId = $req->param('id');
    return Response::json(['user' => $userId]);
});
```

### Route Groups (Prefixes)

Use `(group)` for logical grouping without affecting URLs:

```
app/router/(admin)/dashboard.php    # /dashboard
app/router/(admin)/users.php         # /users
app/router/(api)/v1/users.php        # /v1/users
```

### Route with Multiple HTTP Methods

```php
// app/router/auth/login.php
use Fluxor\Flow;
use Source\Controllers\AuthController;

Flow::GET()->to(AuthController::class, 'showLogin');
Flow::POST()->to(AuthController::class, 'login');
```

---

## 🎮 Controllers

Controllers extend `Fluxor\Controller` and have access to the request object and response helpers.

```php
<?php
// src/Controllers/PostController.php
namespace Source\Controllers;

use Fluxor\Controller;
use Fluxor\Response;
use App\Core\Auth;
use App\Core\ORMHelper;
use Source\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $user = Auth::requireAuth();
        
        $posts = ORMHelper::findAllBy(Post::class, 'userId', $user['user_id']);
        
        return Response::view('posts/index', [
            'title' => 'My Posts',
            'posts' => $posts,
            'user' => $user
        ]);
    }
    
    public function show($id)
    {
        $user = Auth::requireAuth();
        
        $post = ORMHelper::findOneBy(Post::class, 'id', $id);
        
        if (!$post || $post->getUserId() !== $user['user_id']) {
            return Response::view('errors/404', [
                'message' => 'Post not found'
            ], 404);
        }
        
        return Response::view('posts/show', [
            'title' => $post->getTitle(),
            'post' => $post
        ]);
    }
}
```

### Controller Response Methods

```php
// JSON responses
return Response::json($data);
return Response::success($data, 'Message');
return Response::error('Error message', 400);

// HTML responses
return Response::view('view-name', $data);
return Response::html('<h1>Hello</h1>');

// Redirects
return Response::redirect('/dashboard');

// File download
return Response::download('/path/to/file.pdf', 'custom-name.pdf');
```

---

## 📊 Models & Cycle ORM

### Defining Models

```php
<?php
// src/Models/Post.php
namespace Source\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use DateTime;

#[Entity(table: "posts")]
class Post
{
    #[Column(type: "bigPrimary")]
    private int $id;
    
    #[Column(type: "string", length: 255)]
    private string $title;
    
    #[Column(type: "text")]
    private string $content;
    
    #[Column(type: "bigInteger", name: "user_id")]
    private int $userId;
    
    #[BelongsTo(target: User::class, innerKey: 'user_id', outerKey: 'id')]
    private $user;
    
    #[Column(type: "datetime", name: "created_at")]
    private DateTime $createdAt;
    
    // Getters and setters...
}
```

### Using ORMHelper

```php
use App\Core\ORMHelper;
use Source\Models\User;
use Source\Models\Post;

// Find by primary key
$user = ORMHelper::findByPK(User::class, 1);

// Find one by field
$user = ORMHelper::findOneBy(User::class, 'email', 'user@example.com');

// Find all by field
$posts = ORMHelper::findAllBy(Post::class, 'userId', 1);

// Custom select
$users = ORMHelper::select(User::class)
    ->where('role', 'admin')
    ->fetchAll();

// Create entity
$post = new Post();
$post->setTitle('My Post');
$post->setContent('Content...');

$manager = ORMHelper::getManager();
$manager->persist($post);
$manager->run();

// Update entity
$post->setTitle('Updated Title');
$manager->persist($post);
$manager->run();

// Delete entity
$manager->delete($post);
$manager->run();
```

---

## 🔐 Authentication

### Basic Usage

```php
use App\Core\Auth;

// Check if user is logged in
if (Auth::check()) {
    $user = Auth::user();
    echo "Welcome, " . $user['name'];
}

// Require authentication (redirects if not logged in)
$user = Auth::requireAuth();

// Get current user (returns null if not logged in)
$user = Auth::user();

// Logout
Auth::logout();
```

### Login

```php
$credentials = [
    'user_id' => $user->getId(),
    'email' => $user->getEmail(),
    'name' => $user->getName(),
    'role' => $user->getRole()
];

Auth::login($credentials, $remember = true);
```

### CSRF Protection

```php
// In your view
<input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

// In your controller
if (!Auth::validateCsrf($this->request->input('csrf_token'))) {
    return Response::error('Invalid CSRF token', 419);
}
```

### Protecting Routes

```php
// In your controller
public function dashboard()
{
    $user = Auth::requireAuth(); // Redirects to /auth/login if not logged in
    
    // User is authenticated
    return Response::view('dashboard', ['user' => $user]);
}
```

---

## 👁️ Views & Layouts

### Layout System

```php
// src/Views/layouts/main.php
<?php use Fluxor\View; ?>
<!DOCTYPE html>
<html>
<head>
    <title><?= View::yield('title', 'Default Title') ?></title>
    <link href="<?= View::asset('css/app.css') ?>" rel="stylesheet">
    <?= View::yield('styles') ?>
</head>
<body>
    <?= View::yield('content') ?>
    <?= View::yield('scripts') ?>
</body>
</html>
```

### Using Layouts

```php
// src/Views/posts/index.php
<?php use Fluxor\View; ?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title) ?>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="container">
    <h1><?= View::e($title) ?></h1>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <h2><?= View::e($post->getTitle()) ?></h2>
            <p><?= nl2br(View::e($post->getExcerpt())) ?></p>
        </div>
    <?php endforeach; ?>
</div>
<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script src="<?= View::asset('js/posts.js') ?>"></script>
<?php View::endSection(); ?>
```

### Helper Methods

```php
// Escaping (XSS protection)
View::e($unsafeString);

// Raw output (use carefully!)
View::raw($safeHtml);

// Include partial view
View::include('components/header', ['title' => 'My Page']);

// Assets
View::asset('css/app.css');  // /assets/css/app.css
View::url('dashboard');       // /dashboard

// CSRF field
View::csrfField();  // <input type="hidden" name="csrf_token" value="...">

// Method spoofing
View::method('PUT');  // <input type="hidden" name="_method" value="PUT">
```

---

## 📧 Mailer

```php
use App\Core\Mailer;

$mailer = new Mailer();

// Send plain email
$mailer->send(
    'recipient@example.com',
    'Subject',
    '<h1>HTML Body</h1>',
    'Plain text body'  // Optional
);

// Send email with template
$mailer->sendTemplate('welcome', 'user@example.com', [
    'subject' => 'Welcome!',
    'name' => 'John Doe'
]);
```

Template location: `src/Views/emails/welcome.php`

---

## 📁 Uploader

```php
use App\Core\Uploader;

// Single file upload
$file = $_FILES['avatar'];
$url = Uploader::upload($file, ['jpg', 'png'], 5 * 1024 * 1024);

// Get file info
$info = Uploader::getFileInfo('path/to/file.jpg');

// Delete file
Uploader::delete('path/to/file.jpg');

// Get all uploaded files
$files = Uploader::getAll(['sort' => 'created', 'direction' => 'desc']);

// Clean old files (older than 24 hours)
$deleted = Uploader::cleanOldFiles(86400);
```

---

## 🛠️ Core Helpers

### ORMHelper Methods

| Method | Description |
|--------|-------------|
| `getORM()` | Get Cycle ORM instance |
| `getManager()` | Get EntityManager for persist/delete |
| `getRepository($entityClass)` | Get repository for queries |
| `select($entityClass)` | Create select query |
| `findByPK($entityClass, $id)` | Find by primary key |
| `findOneBy($entityClass, $field, $value)` | Find one by field |
| `findAllBy($entityClass, $field, $value)` | Find all by field |
| `findAll($entityClass)` | Get all entities |

### Auth Methods

| Method | Description |
|--------|-------------|
| `check()` | Check if user is logged in |
| `user()` | Get current user or null |
| `requireAuth($redirectUrl)` | Require authentication or redirect |
| `login($credentials, $remember)` | Login user |
| `logout()` | Logout user |
| `csrfToken()` | Generate CSRF token |
| `validateCsrf($token)` | Validate CSRF token |

### Mailer Methods

| Method | Description |
|--------|-------------|
| `send($to, $subject, $htmlBody, $textBody)` | Send email |
| `sendTemplate($to, $template, $data)` | Send email using template |

### Uploader Methods

| Method | Description |
|--------|-------------|
| `upload($file, $allowedTypes, $maxSize)` | Upload file |
| `delete($filePath)` | Delete file |
| `getUrl($filePath)` | Get public URL |
| `getFileInfo($filePath)` | Get file information |
| `getAll($options)` | Get all uploaded files |
| `cleanOldFiles($olderThan)` | Delete old files |

---

## 🌐 Deployment

### Production Checklist

1. **Update .env for production**:
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-secure-key
```

2. **Configure web server**:

**Apache (.htaccess)**:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

**Nginx**:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

3. **Set proper permissions**:
```bash
chmod -R 755 public/
chmod -R 775 storage/
```

4. **Optimize Composer**:
```bash
composer install --no-dev --optimize-autoloader
```

---

## 🐛 Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| **Database connection failed** | Verify `.env` database credentials and ensure database server is running |
| **Migration errors** | Run `composer migrate` to recreate tables |
| **404 errors** | Check `.htaccess` or nginx configuration |
| **Authentication not working** | Verify `AUTH_SECRET_KEY` in `.env` |
| **Views not found** | Check `views_path` configuration in `public/index.php` |
| **CSRF token invalid** | Clear browser cookies and session |

### Logs

```bash
# Check PHP error log
tail -f storage/logs/php-error.log

# Check web server logs
tail -f /var/log/apache2/error.log
```

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- Built on [Fluxor PHP Framework](https://github.com/lizzyman04/fluxor)
- ORM by [Cycle ORM](https://cycle-orm.dev/)
- Styling by [Tailwind CSS](https://tailwindcss.com/)

---

**Fluxor MVC Template** - Build modern PHP applications with joy! 🚀