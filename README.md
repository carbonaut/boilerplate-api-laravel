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
- [Larastan](https://github.com/nunomaduro/larastan) passing on the highest level available;
- [Sanctum](https://laravel.com/docs/9.x/sanctum) for Authentication;
  - Using UUIDs for the PersonalAccessTokens and for the Authenticable models;
  - Custom routes for authentication;
  - Tokens expire in [one year](config/sanctum.php);
- [Rollbar](https://docs.rollbar.com/docs/laravel) integration for error tracking
- [Swagger](https://swagger.io) (OpenAPI 3.0) for API documentation
  - The documentation is available on the `api.` subdomain of your application;
  - The documentation is disabled when the application is running in production (see `routes\api.php`);
  - Keep the documentation up-to-date on `resources\api\documentation.yaml`
- [Laravel Translatable](https://github.com/spatie/laravel-translatable) for multi-language support;
- [Laravel Translations Loader](https://github.com/spatie/laravel-translation-loader) helps you provide translations from the database to your application using the API;
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
- [Custom error handling](app/Exceptions/Handler.php) to have standardized errors returned on the API endpoints. `message` is a localized field that can be displayed to the user, as `error` is a more meaningful message for the developers;
- [Queue](https://laravel.com/docs/9.x/queues) setup to use the database driver;
- [Emails](https://laravel.com/docs/9.x/mail) already set up for email verification and password reset. It will take into account the [user preferred language](https://laravel.com/docs/9.x/mail#user-preferred-locales) when sending the email;
- [Pull-request template](.github/pull_request_template.md) so you don't forget about important things when merging code;
- [Route Binding Trait](app/Traits/ResolveRouteBinding.php) to prevent QueryException when passing an invalid parameter type to a route with [Model Binding](https://laravel.com/docs/9.x/routing#route-model-binding);
- [Example Tests](tests) for some methods and endpoints. We opted to use the Unit namespace for testing methods and internal code, and the Feature namespace to test the application from the "outside", by calling routes and accessing pages;

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

## Deployment
We recommend using [Laravel Forge](https://forge.laravel.com/) or [Laravel Vapor](https://vapor.laravel.com/) to deploy your Laravel application. More information can be found on their respective documentations. 

If you want to deploy on AWS ElasticBeanstalk using Github Actions, check the [files that were removed](https://github.com/carbonaut/boilerplate-api-laravel/commit/a6edcc336d9cfb0bdedd5ec209b0d66f18bf410d) from this boilerplate.

### Deployment Checklist
- [ ] I have used the correct environment variables, especially `APP_ENV=production` and `APP_DEBUG=false`;
- [ ] I have enabled HSTS on `config\secure-headers.php`;
- [ ] I have updated `config\cors.php` to only allow calls from trusted sources;
- [ ] I am running the queue worker on my server;
- [ ] I have added [Laravel Scheduled](https://laravel.com/docs/9.x/scheduling#running-the-scheduler) to my server cron;

## Contributing

If you find any problems, please report them with [Issues](https://github.com/carbonaut/boilerplate-api-laravel/issues).

Also, PRs are always welcome :)

## Known Issues
- `psr/log` is locked to `v2.0.0` due to `rollbar/rollbar-laravel` not being compatible with `v3.0.0`. This is a minor issue since `v3.0.0` is [only adding return types](https://github.com/php-fig/log/compare/2.0.0...3.0.0). More on this: [rollbar/rollbar-php-laravel#138](https://github.com/rollbar/rollbar-php-laravel/issues/138) and [rollbar/rollbar-php#570](https://github.com/rollbar/rollbar-php/issues/570)
- [preventSilentlyDiscardingAttributes](https://laravel.com/docs/9.x/eloquent#mass-assignment-exceptions) is not working on the current Laravel version. This should be addressed as soon as a fix is available so we know when fields are being discarded. [1](https://devscope.io/code/laravel/framework/issues/44094) [2](https://github.com/laravel/framework/commit/eff2275d1fae7a15ba91685b8e94e730108be9f4) [3](https://github.com/laravel/framework/pull/43893)
- Not all methods and endpoints are tested. This should be addressed in the future;

## Acknowledgements
- OAuth2 (implemented by Passport) does not recommend the use of Password Grants anymore and suggests using [Authorization Code Grant](https://oauth2.thephpleague.com/authorization-server/which-grant/) instead. Since we'll not be authenticating third-party applications, we changed from [Passport](https://laravel.com/docs/9.x/passport) to [Sanctum](https://laravel.com/docs/9.x/sanctum);
- Even though a way of storing user devices is available, there's no scaffolding or examples on how to send a message. This should be implemented in future versions using [Notifications](https://laravel.com/docs/9.x/notifications) and [Notification Channels](https://laravel-notification-channels.com/). See [previous implementation](https://github.com/carbonaut/boilerplate-api-laravel/commit/3db896a57091e13c83cb2f134539870da44ef10c);
- Our [custom user profile](https://github.com/carbonaut/boilerplate-api-laravel/commit/4489b533fe24f0a6148c82d8cdb92cb42ba5d5c8) solution was removed. You should either use [spatie/laravel-permission](https://github.com/spatie/laravel-permission) or wait until we release our custom package;
- Currently the emails are only being dispatched into the queue and sent to the user. Ideally we'd like to keep track of the emails that were sent, their send status, recipient and even a route to keep track if the user has opened the email. The [previous code](https://github.com/carbonaut/boilerplate-api-laravel/commit/3422f84f49bac6212edf3ce968aa3e90c4e66a64) used by this boilerplate was removed in favor of a proper implementation that should be done in the future;