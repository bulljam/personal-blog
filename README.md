# Personal Blog

A modern blog application built with Laravel 12, Livewire 3, and Volt. This application provides a complete blogging platform with user authentication, role-based access control, and interactive features for content creators and readers.

## Features

- User authentication with email verification
- Role-based access control (Author and Reader)
- Blog post management with CRUD operations
- Post interactions: likes, dislikes, comments, and nested replies
- Favorites system for saving posts
- User dashboard with profile management
- Post search and filtering capabilities
- Responsive design with dark mode support
- Real-time updates using Livewire

## Technology Stack

- PHP 8.2+
- Laravel 12
- Livewire 3
- Livewire Volt
- Tailwind CSS 4
- Pest 4 (Testing)
- Larastan (Static Analysis)

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and Yarn
- MySQL, PostgreSQL, or SQLite
- Laravel Herd (recommended) or PHP development server

## Installation

1. Clone the repository:
```bash
git clone https://github.com/bulljam/personal-blog.git
cd personal-blog
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
yarn install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=personal_blog
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations:
```bash
php artisan migrate
```

8. Build frontend assets:
```bash
yarn run build
```

## Development

Start the development server:
```bash
composer run dev
```

This command runs the Laravel server, queue worker, and Vite development server concurrently.

For development with SSR:
```bash
composer run dev:ssr
```

## Testing

Run all tests:
```bash
php artisan test
```

Run tests for a specific file:
```bash
php artisan test tests/Feature/ExampleTest.php
```

Filter tests by name:
```bash
php artisan test --filter=testName
```

## Code Quality

Format code with Laravel Pint:
```bash
composer run pint
```

Run static analysis with Larastan:
```bash
composer run phpstan
```

## Project Structure

- `app/Models/` - Eloquent models (User, Post, Comment, Like, Favourite)
- `app/Enums/` - Application enums (Role)
- `app/Policies/` - Authorization policies
- `app/Http/Middleware/` - Custom middleware
- `resources/views/livewire/pages/` - Volt component pages
- `routes/web.php` - Application routes
- `database/migrations/` - Database migrations
- `tests/` - Test suite

## User Roles

- **Author**: Can create, edit, and manage blog posts
- **Reader**: Can view posts, interact with content, and manage favorites

