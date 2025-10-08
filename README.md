# Composer MVC Template

This project is a lightweight, modern MVC template built with **PHP** and **Composer**, following the Model-View-Controller (MVC) architecture. It provides a clean, organized structure for developing PHP applications, with secure authentication system, image uploading, email handling, environment configuration and modern development workflow.

## 🚀 Features

- **Modern MVC Architecture** - Clean separation of concerns
- **Cycle ORM** - Fast and efficient database abstraction
- **Secure Authentication** - Token-based sessions with CSRF protection
- **Environment Configuration** - Easy configuration via `.env` files
- **Routing System** - Flexible and intuitive route management
- **View Templating** - Simple and powerful template engine
- **Security First** - Built-in protection against common vulnerabilities
- **Lightweight** - Minimal dependencies, maximum performance

This README guides you through setting up the project, using its features, and understanding its structure.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation](#installation)
3. [Project Structure](#project-structure)
4. [Configuration](#configuration)
5. [Database & Models](#database--models)
6. [Authentication](#authentication)
7. [Routing](#routing)
8. [Views & Templates](#views--templates)
9. [API Reference](#api-reference)
10. [Deployment](#deployment)
11. [Troubleshooting](#troubleshooting)
12. [License](#license)

---

## Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.0
- **Composer**: [Download Composer](https://getcomposer.org/)
- **Database**: MySQL or PostgreSQL (configured in `.env`)
- **Internet Connection**: Required for loading **Tailwind CSS** via the Play CDN and **jQuery** CDN.

---

## Installation

1. Clone the repository:

```bash
git clone https://github.com/lizzyman04/composer-mvc-template.git my-composer-app
```

2. Navigate to the project directory:

```bash
cd my-composer-app
```

3. Install the required Composer dependencies:

```bash
composer install
```

4. Set up your environment file by copying the default `.env.example` file to `.env`:

```bash
cp .env.example .env
```

5. Update your `.env` file with your database and email configurations.

6. Set up your database.

```bash
composer schema
```

7. Start the local server:

```bash
composer serve
```

Visit [http://localhost:8000](http://localhost:8000) in your browser.

---

## Project Structure

Here's an overview of the main project directories and files:

```
/composer-mvc-template
├── /public
│   ├── /assets
│   │   ├── /css
│   │   ├── /img
│   │   └── /js
│   ├── index.php
│   ├── /uploads
├── /src
│   ├── /Controllers
│   ├── /Models
│   ├── /Views
│   │   ├── /layouts
│   │   │   ├── main.php
│   ├── └──...
├── /core
│   ├── Helpers/
│   ├── Authenticator.php
│   ├── Mailer.php
│   ├── Router.php
│   ├── Uploader.php
│   └── View.php
├── /database
│   ├── connection.php
│   ├── orm.php
│   └── schema.php
├── /config
│   ├── config.php
│   ├── env.php
│   ├── schema.sql
│   └── router/
├── .env
├── .env.example
├── .gitignore
├── composer.json
├── LICENSE
└── README.md
```

---

## Configuration

### Environment Variables (.env)

```env
# Application
BASE_URL=http://localhost:8000
APP_ENV=development

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Authentication Security
AUTH_SECRET_KEY=your-super-secret-key-change-this
AUTH_TOKEN_ALGORITHM=sha256
AUTH_TOKEN_EXPIRY=86400
AUTH_SESSION_EXPIRY=1800
AUTH_SESSION_REGENERATE=1800
AUTH_REMEMBER_EXPIRY=2592000
```

### Available Commands

```bash
# Development
composer dev              # Start development server
composer schema           # Run database schema

# Code quality
composer lint            # Check code style
composer fix             # Fix code style issues
```

---

## 🗄️ Database & Models

### Schema

The template includes ready-to-use tables:

```sql
-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Posts table  
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Creating Models

```php
<?php
// src/Models/Product.php
namespace Source\Models;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;

#[Entity(table: 'products')]
class Product
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    public string $name;

    #[Column(type: 'decimal')]
    public float $price;

    #[Column(type: 'datetime', name: 'created_at')]
    public \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
```

### Using Models

```php
use Core\Helpers\ORMHelper;
use Source\Models\Product;

// Create
$product = new Product();
$product->name = "New Product";
$product->price = 29.99;

$manager = ORMHelper::getManager();
$manager->persist($product);
$manager->run();

// Read
$products = ORMHelper::select(Product::class)
    ->where('price', '>', 20)
    ->fetchAll();

// Update
$product->price = 24.99;
$manager->persist($product);
$manager->run();

// Delete
$manager->delete($product);
$manager->run();
```

---

## 🔐 Authentication

### Setup

The template includes a complete authentication system:

```php
use Core\Helpers\AuthHelper;

// Check if user is logged in
$credentials = AuthHelper::check();
if ($credentials) {
    echo "Welcome, " . $credentials['name'];
}

// Require authentication
$user = AuthHelper::requireAuth();

// Logout
AuthHelper::logout();
```

### Protecting Routes

```php
// In your controller
public function dashboard()
{
    $credentials = AuthHelper::check();
    
    if (!$credentials) {
        header('Location: /login');
        exit;
    }

    // User is authenticated
    return View::render('dashboard', ['user' => $credentials]);
}
```

### CSRF Protection

```php
// In your view
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= AuthHelper::csrfToken() ?>">
    <!-- form fields -->
</form>

// In your controller
if (!AuthHelper::validateCsrf($_POST['csrf_token'] ?? '')) {
    // Handle invalid CSRF token
}
```

---

## 🛣️ Routing

### Route Configuration

Routes are defined in `config/router/`:

```php
// config/router/web.php
return [
    '/' => 'HomeController@index',
    '/about' => 'HomeController@about',
    
    '/posts' => 'PostController@index',
    '/posts/{id}' => [
        'methods' => ['GET'],
        'controller' => 'PostController@show'
    ],
    '/posts/create' => [
        'methods' => ['GET', 'POST'], 
        'controller' => 'PostController@create'
    ]
];
```

### Creating Controllers

```php
<?php
// src/Controllers/PostController.php
namespace Source\Controllers;

use Core\Helpers\AuthHelper;
use Core\Helpers\ResponseHelper;
use Core\View;

class PostController
{
    public function index()
    {
        $credentials = AuthHelper::check();
        
        View::render('posts/index', [
            'title' => 'All Posts',
            'user' => $credentials
        ]);
    }

    public function show($id)
    {
        // $id is automatically injected from route parameter
        
        View::render('posts/show', [
            'title' => 'Post Details',
            'post_id' => $id
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle form submission
            ResponseHelper::success(['message' => 'Post created!']);
        }

        // Show form
        View::render('posts/create', ['title' => 'Create Post']);
    }
}
```

---

## 👁️ Views & Templates

### Basic View

```php
// In controller
View::render('home', [
    'title' => 'Welcome Home',
    'user' => $credentials,
    'posts' => $posts
]);

// src/Views/home.php
<h1><?= $title ?></h1>
<?php if ($user): ?>
    <p>Welcome back, <?= htmlspecialchars($user['name']) ?>!</p>
<?php endif; ?>
```

### Using Layouts

```php
<?php
// src/Views/posts/show.php
$title = $post->title;
ob_start();
?>

<article class="post">
    <h1><?= htmlspecialchars($post->title) ?></h1>
    <div class="content">
        <?= nl2br(htmlspecialchars($post->content)) ?>
    </div>
</article>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>
```

### JSON Responses

```php
use Core\Helpers\ResponseHelper;

// Success response
ResponseHelper::success($data);
ResponseHelper::success($data, 201); // With custom status code

// Error response  
ResponseHelper::error('Something went wrong');
ResponseHelper::error('Not found', 404);

// Pagination
ResponseHelper::pagination($data, $total, $perPage, $currentPage);

// Redirect
ResponseHelper::redirect('/new-location');
```

---

## 📚 API Reference

### Core Helpers

#### ORMHelper
```php
ORMHelper::select(Model::class)->where(...)->fetchAll();
ORMHelper::getRepository(Model::class)->findByPK($id);
ORMHelper::getManager()->persist($entity)->run();
```

#### AuthHelper
```php
AuthHelper::check();
AuthHelper::setup($credentials);
AuthHelper::logout();
AuthHelper::csrfToken();
AuthHelper::validateCsrf($token);
```

#### ResponseHelper
```php
ResponseHelper::success($data, $statusCode);
ResponseHelper::error($message, $statusCode);
ResponseHelper::redirect($url);
ResponseHelper::json_response($data);
```

### Router Features

- **Route parameters**: `/users/{id}`
- **Multiple HTTP methods**: `['GET', 'POST', 'PUT']`
- **Automatic dependency injection**
- **404 handling**

---

## 🚀 Deployment

### Production Checklist

1. **Update .env for production**:
```env
APP_ENV=production
BASE_URL=https://yourdomain.com
AUTH_SECRET_KEY=generate-a-secure-random-key
```

2. **Configure web server**:
```apache
# Apache .htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

```nginx
# Nginx configuration
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

3. **Set proper permissions**:
```bash
chmod -R 755 public/
chmod -R 775 storage/ # if using file storage
```

4. **Optimize Composer**:
```bash
composer install --no-dev --optimize-autoloader
```

---

## 🐛 Troubleshooting

### Common Issues

1. **Cycle ORM Schema Errors**
   - Run `composer schema` to reset database
   - Check model annotations match database schema

2. **Authentication Issues**
   - Verify `AUTH_SECRET_KEY` in .env
   - Check session configuration

3. **Route Not Found**
   - Verify route definition in `config/router/`
   - Check controller namespace

4. **Database Connection**
   - Verify .env database credentials
   - Check database server is running

### Getting Help

- Check the logs in your web server error log
- Verify all environment variables are set
- Ensure all Composer dependencies are installed

---

## 🤝 Contributing

We welcome contributions! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

### Development Setup

```bash
git clone your-fork-url
cd composer-mvc-template
composer install
cp .env.example .env
composer schema
composer dev
```

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## 🎯 Conclusion

This MVC template provides a solid foundation for building modern PHP applications with best practices in security, architecture, and development workflow. It's designed to be both beginner-friendly and scalable for complex applications.

Happy coding! 🚀