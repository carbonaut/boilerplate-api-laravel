<h1 style="display: flex;align-items:center;">
<img src="https://user-images.githubusercontent.com/20388082/89651773-cb900d00-d89a-11ea-99bb-d5e97b1609d0.png" width="50" alt="Carbonaut Logo" style="margin-right:5px;">
Carbonaut's Laravel API Boilerplate
</h1>

![integration](https://github.com/carbonaut/boilerplate-api-laravel/workflows/integration/badge.svg)

Nothing better than getting to action without worrying about the repetitive and boring 
parts of bootstrapping a project, right?

That's why we've built this awesome boilerplate so you can focus on your project's ideas and get the boring, albeit important, parts out of the way.

- Language: PHP 8.0
- Framework: Laravel 8
- Database: PostgreSQL 13

## Goodies
- OAuth2 Authentication with [Passport](https://laravel.com/docs/7.x/passport)
- [Rollbar](https://rollbar.com) integration for error tracking
- [Swagger](https://swagger.io) (OpenAPI 3.0) for API documentation
- Multi language support with [astrotomic/laravel-translatable](https://github.com/astrotomic/laravel-translatable)
- Password Reset and Email Verification flows built in
- Powered by Docker and Docker Compose for easily building your development environment
- CI and CD flows for GitHub Actions
- Email and Push Notifications already set up
- Configuration files for deploying into AWS Elastic Beanstalk
- [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) configuration file for code styling (PhpCsFixer rule-set with minor customizations)

## Getting started


### Existing environment
Make sure you have PHP and PostgreSQL installed and running locally. Rename `.env.example.local` to `.env` and change the default falues. You're good to go!

```sh
$ git clone https://github.com/carbonaut/boilerplate-api-laravel
$ cd boilerplate-api-laravel
$ composer install
$ cp .env.example.local .env
$ cp .env.example.test .env.test
# Edit .env files...
$ php artisan migrate
$ php artisan db:seed
$ php artisan serve
# API docs at http://api.localhost:8000
```

### Laravel Sail
The manual dockerization was removed in favor of [Laravel Sail](https://laravel.com/docs/9.x/sail), which is yet to be added to this project. Feel free to open a PR!



## Contributing

If you find any problems, please report them with [Issues](https://github.com/carbonaut/boilerplate-api-laravel/issues).

Also, PRs are always welcome :)
