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
  - Added routes for authentication
- [Rollbar](https://docs.rollbar.com/docs/laravel) integration for error tracking
- [Swagger](https://swagger.io) (OpenAPI 3.0) for API documentation
  - The documentation is available on the `api.` subdomain of your application;
  - The documentation is disabled when the application is running in production (see `routes\api.php`);
  - Keep the documentation up-to-date on `resources\api\documentation.yaml`
- [Laravel Translatable](https://github.com/spatie/laravel-translatable) for multi-language support;
- [Laravel Translations Loader](https://github.com/spatie/laravel-translation-loader) helps you provide translations from the database to your application using the API;
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
- Several endpoints that will help you quickly bootstrap your application. See `routes\api.php`;
- Route groups are attached to application subdomains (see `app\Providers\RouteServiceProvider.php`);
- [Prevents Lazy Loading](https://laravel.com/docs/9.x/eloquent-relationships#preventing-lazy-loading) helps you prevent shipping code that does not perform well;
- [Login Throttling](app/Http/Middleware/ThrottleLogin.php) based on [Laravel UI](https://github.com/laravel/ui/blob/master/auth-backend/ThrottlesLogins.php);
- [Application Localization](app/Http/Middleware/Localize.php) based on user preferences and `Accept-Language` header;

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
$ php artisan migrate:fresh --seed
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
- [preventSilentlyDiscardingAttributes](https://laravel.com/docs/9.x/eloquent#mass-assignment-exceptions) is not working on the current Laravel version. This should be addressed as soon as a fix is available so we know when fields are being discarded. [1](https://devscope.io/code/laravel/framework/issues/44094) [2](https://github.com/laravel/framework/commit/eff2275d1fae7a15ba91685b8e94e730108be9f4) [3]](https://github.com/laravel/framework/pull/43893)

## Acknowledgements
- OAuth2 (implemented by Passport) does not recommend the use of Password Grants anymore and suggests using [Authorization Code Grant](https://oauth2.thephpleague.com/authorization-server/which-grant/) instead. Since we'll not be authenticating third-party applications, we changed from [Passport](https://laravel.com/docs/9.x/passport) to [Sanctum](https://laravel.com/docs/9.x/sanctum);
- Even though a way of storing user devices is available, there's no scaffolding or examples on how to send a message. This should be implemented in future versions using [Notifications](https://laravel.com/docs/9.x/notifications) and [Notification Channels](https://laravel-notification-channels.com/). See [previous implementation](https://github.com/carbonaut/boilerplate-api-laravel/commit/3db896a57091e13c83cb2f134539870da44ef10c);
- Our [custom user profile](https://github.com/carbonaut/boilerplate-api-laravel/commit/4489b533fe24f0a6148c82d8cdb92cb42ba5d5c8) solution was removed. You should either use [spatie/laravel-permission](https://github.com/spatie/laravel-permission) or wait until we release our custom package;