<h1 style="display: flex;align-items:center;">
<img src="https://user-images.githubusercontent.com/20388082/89651773-cb900d00-d89a-11ea-99bb-d5e97b1609d0.png" width="50" alt="Carbonaut Logo" style="margin-right:5px;">
Carbonaut's Laravel API Boilerplate
</h1>

![integration](https://github.com/carbonaut/boilerplate-api-laravel/workflows/integration/badge.svg)

Nothing better than getting to action without worrying about the repetitive and boring 
parts of bootstrapping a project, right?

That's why we've built this awesome boilerplate so you can focus on your project's ideas and get the boring, albeit important, parts out of the way.

- Language: PHP 8.1
- Framework: Laravel 9
- Database: PostgreSQL 13

## Goodies
- [Sanctum](https://laravel.com/docs/9.x/sanctum) for Authentication;
  - Using UUIDs for the PersonalAccessTokens and for the Authenticable models;
- [Rollbar](https://docs.rollbar.com/docs/laravel) integration for error tracking
- [Swagger](https://swagger.io) (OpenAPI 3.0) for API documentation
  - The documentation is available on the `api.` subdomain of your application;
  - The documentation is disabled when the application is running in production (see `routes\api.php`);
  - Keep the documentation up-to-date on `resources\api\documentation.yaml`
- [Laravel Translatable](https://github.com/spatie/laravel-translatable) for multi-language support;
- Password Reset and Email Verification flows built in
- CI and CD flows for GitHub Actions
- Email and Push Notifications already set up
- Configuration files for deploying into AWS Elastic Beanstalk
- [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) configuration file for code styling (PhpCsFixer rule-set with minor customizations);
- [Clockwork](https://github.com/itsgoingd/clockwork) gives you an insight into your application runtime - including request data, performance metrics, log entries, database queries, cache queries, redis commands, dispatched events, queued jobs, rendered views and more - for HTTP requests, commands, queue jobs and tests;
- [Eloquent Sortable](https://github.com/spatie/eloquent-sortable) that adds sortable behaviour to an Eloquent model.
- [Secure Headers](https://github.com/bepsvpt/secure-headers) for adding security related headers to HTTP response. The following configs were changed from default: `x-frame-options`, `default-src`, `font-src`, `img-src`, `script-src` (`self`, `unsafe-inline` and `unsafe-eval`), `style-src`
  - ⚠️ Once you set up SSL/TLS, remember to enable `hsts`; 
- [Belongs-to-through](https://github.com/staudenmeir/belongs-to-through) adds the inverse version of `HasManyThrough`, allowing `BelongsToThrough` relationships with unlimited intermediate models;
- [Validation rules](https://github.com/mattkingshott/axiom) to augment the existing set provided by Laravel itself;
- [Slack Notifications](https://laravel.com/docs/9.x/notifications#slack-notifications) for sending notifications via Slack;
- [Boosted Enums](https://github.com/archtechx/enums) for [native PHP 8.1 Enums](https://php.watch/versions/8.1/enums);
- Route groups are attached to application subdomains (see `app\Providers\RouteServiceProvider.php`);

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
$ php artisan key:generate
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

## Known Issues
- `psr/log` is locked to `v2.0.0` due to `rollbar/rollbar-laravel` not being compatible with `v3.0.0`. This is a minor issue since `v3.0.0` is [only adding return types](https://github.com/php-fig/log/compare/2.0.0...3.0.0). More on this: [rollbar/rollbar-php-laravel#138](https://github.com/rollbar/rollbar-php-laravel/issues/138) and [rollbar/rollbar-php#570](https://github.com/rollbar/rollbar-php/issues/570)