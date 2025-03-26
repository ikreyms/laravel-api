# Laravel API Setup

This is a custom Laravel API setup that includes essential features for building robust APIs. This setup leverages the latest Laravel version (12.0), integrates the **Spatie Laravel Data** package, provides an easy-to-use ID hashing mechanism via **Hashids**, and more.

## Features

- **No starter kit** – A clean Laravel 12.0 setup with no additional boilerplate code.
- **PHP 8.2** – Compatible with the latest version of PHP.
- **Laravel 12.0** – The most recent version of Laravel.
- **Laravel Tinker 2.10** – Interactive REPL for testing and debugging.
- **Spatie Laravel Data 4.14** – Provides easy handling of Data Transfer Objects (DTOs) for organizing data in your API.
- **Custom Artisan Commands** – Commands for generating DTOs and actions:
  - `php artisan make:data`
  - `php artisan make:action`
- **SQLite Database** – Default database setup for easy development (can be switched to other databases as needed).
- **Password Validation** – Supports `Password::defaults()` from `Illuminate\Validation\Rules` for password validation, customizable in `AppServiceProvider`.
- **Hashids for IDs** – Hashids are used when communicating with clients instead of regular database IDs, enhancing security.
- **HasHashid Trait** – Automatically includes the `HasHashid` trait in models when generated using artisan commands. Migration files will also include a `hashid` field.

STEPS TO FOLLOW:

- Run composer install
- Set .env
	first copy .env.example to .env
	Set APP_NAME, APP_URL, DB_*
- Generate APP_KEY using artisan key:generate
- Update user model
	fillable array, migrations according to requirements
- Run artisan migrate:fresh to reflect the changes in db

