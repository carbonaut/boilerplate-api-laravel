<h1 style="display: flex;align-items:center;">
<img src="https://user-images.githubusercontent.com/20388082/89651773-cb900d00-d89a-11ea-99bb-d5e97b1609d0.png" width="50" alt="Carbonaut Logo" style="margin-right:5px;">
Carbonaut's Laravel API Boilerplate
</h1>

![integration](https://github.com/carbonaut/boilerplate-api-laravel/workflows/integration/badge.svg)

Nothing better than getting to action without worrying about the repetitive and boring 
parts of bootstrapping a project, right?

That's why we've built this awesome boilerplate so you can focus on your project's ideas and get the boring, albeit important, parts out of the way.

- Language: PHP 8.2
- Framework: Laravel 10
- Database: PostgreSQL 15, SQLite 3 (tests only)

## Goodies
- [Larastan](https://github.com/nunomaduro/larastan) passing on the highest level available;
- [Sanctum](https://laravel.com/docs/10.x/sanctum) for Authentication;
  - Using UUIDs for the PersonalAccessTokens and for the Authenticable models;
  - Custom routes for authentication;
  - Tokens expire in [one year](config/sanctum.php);
- [Rollbar](https://docs.rollbar.com/docs/laravel) integration for error tracking
- [Swagger](https://swagger.io) (OpenAPI 3.0) for API documentation
  - The documentation is available on the `api.` subdomain of your application;
  - The documentation is disabled when the application is running in production (see `routes\api.php`);
  - Keep the documentation up-to-date on `resources\api\documentation.yaml`
- [Github Actions Workflow](.github/workflows/integration.yml) to ensure the new code is linted, tested and [larastan](https://github.com/nunomaduro/larastan)-ed before merging into a root branch;
  - Make sure you enable branch protection on your repository (pull request before merging, approvals, up-to-date branches, conversation resolution) and require the following status checks to pass before merging: `lint`, `test` and `static-analysis`;
- [Laravel Translatable](https://github.com/spatie/laravel-translatable) for multi-language support;
- [Laravel Translations Loader](https://github.com/spatie/laravel-translation-loader) helps you provide translations from the database to your application using the API;
- [Laravel-Lang](https://github.com/Laravel-Lang/lang) and [Publisher](https://github.com/Laravel-Lang/publisher) to manage your laravel translations. Check [the documentation](https://publisher.laravel-lang.com/using/) on how to add languages or update existing;
- [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) configuration file for code styling (PhpCsFixer rule-set with minor customizations);
- [Clockwork](https://github.com/itsgoingd/clockwork) gives you an insight into your application runtime - including request data, performance metrics, log entries, database queries, cache queries, redis commands, dispatched events, queued jobs, rendered views and more - for HTTP requests, commands, queue jobs and tests;
- [Secure Headers](https://github.com/bepsvpt/secure-headers) for adding security related headers to HTTP response. The following configs were changed from default: `x-frame-options`, `connect-src`, `default-src`, `font-src`, `img-src`, `script-src` (`self`, `unsafe-inline` and `unsafe-eval`), `style-src`
  - ⚠️ Once you set up SSL/TLS, remember to enable `hsts`;
- [Flysystem AWS S3 Adapter](https://github.com/thephpleague/flysystem-aws-s3-v3) is a sub-split of Flysystem library that provides an iteraction interface with AWS S3.
- [Slack Notifications](https://laravel.com/docs/10.x/notifications#slack-notifications) for sending notifications via Slack;
- Several endpoints that will help you quickly bootstrap your application. See `routes\api.php`;
- Route groups are attached to application subdomains (see `app\Providers\RouteServiceProvider.php`);
- [Prevents Lazy Loading](https://laravel.com/docs/10.x/eloquent-relationships#preventing-lazy-loading) helps you prevent shipping code that does not perform well;
- [Login Throttling](app/Http/Middleware/ThrottleLogin.php) based on [Laravel UI](https://github.com/laravel/ui/blob/master/auth-backend/ThrottlesLogins.php);
- [Application Localization](app/Http/Middleware/Localize.php) based on user preferences and `Accept-Language` header;
- [Custom error handling](app/Exceptions/Handler.php) to have standardized errors returned on the API endpoints. `message` is a localized field that can be displayed to the user, as `error` is a more meaningful message for the developers;
- [Queue](https://laravel.com/docs/10.x/queues) setup to use the database driver;
- [Emails](https://laravel.com/docs/10.x/mail) already set up for email verification and password reset. It will take into account the [user preferred language](https://laravel.com/docs/10.x/mail#user-preferred-locales) when sending the email;
- [Pull-request template](.github/pull_request_template.md) so you don't forget about important things when merging code;
- [Example Tests](tests) for some methods and endpoints. We opted to use the Unit namespace for testing methods and internal code, and the Feature namespace to test the application from the "outside", by calling routes and accessing pages;

## Package Recommendations

We did not add the following packages in order to keep this boilerplate as slim as possible.
But they are excellent assets on our projects.

- [Eloquent Sortable](https://github.com/spatie/eloquent-sortable) that adds sortable behaviour to an Eloquent model.
- [Boosted Enums](https://github.com/archtechx/enums) for [native PHP 8.1+ Enums](https://php.watch/versions/8.1/enums);
- [Belongs-to-through](https://github.com/staudenmeir/belongs-to-through) adds the inverse version of `HasManyThrough`, allowing `BelongsToThrough` relationships with unlimited intermediate models;
- [Laravel Nova](https://nova.laravel.com/) we strongly recommend it if you are looking for a beautifully-designed administration panel for your Laravel application;

## Getting started


### Existing environment
Make sure you have PHP and PostgreSQL installed and running locally. Rename `.env.example.local` to `.env` and change the default values. You're good to go! For running tests make sure you have SQLite available.

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
The manual dockerization was removed in favor of [Laravel Sail](https://laravel.com/docs/10.x/sail), which is yet to be added to this project. Feel free to open a PR!

## Deployment
We recommend using [Laravel Forge](https://forge.laravel.com/) or [Laravel Vapor](https://vapor.laravel.com/) to deploy your Laravel application. More information can be found on their respective documentations. 

If you want to deploy on AWS ElasticBeanstalk using Github Actions, check the [files that were removed](https://github.com/carbonaut/boilerplate-api-laravel/commit/a6edcc336d9cfb0bdedd5ec209b0d66f18bf410d) from this boilerplate.

### Deployment Checklist
- [ ] I have used the correct environment variables, especially `APP_ENV=production` and `APP_DEBUG=false`;
- [ ] I have enabled HSTS on `config\secure-headers.php`;
- [ ] I have updated `config\cors.php` to only allow calls from trusted sources;
- [ ] I am running the queue worker on my server;
- [ ] I have added [Laravel Scheduled](https://laravel.com/docs/10.x/scheduling#running-the-scheduler) to my server cron;

## Contributing

If you find any problems, please report them with [Issues](https://github.com/carbonaut/boilerplate-api-laravel/issues).

Also, PRs are always welcome :)

## Known Issues
- Not all methods and endpoints are tested. This should be addressed in the future;

## Acknowledgements
- OAuth2 (implemented by Passport) does not recommend the use of Password Grants anymore and suggests using [Authorization Code Grant](https://oauth2.thephpleague.com/authorization-server/which-grant/) instead. Since we'll not be authenticating third-party applications, we changed from [Passport](https://laravel.com/docs/10.x/passport) to [Sanctum](https://laravel.com/docs/10.x/sanctum);
- Even though a way of storing user devices is available, there's no scaffolding or examples on how to send a message. This should be implemented in future versions using [Notifications](https://laravel.com/docs/10.x/notifications) and [Notification Channels](https://laravel-notification-channels.com/). See [previous implementation](https://github.com/carbonaut/boilerplate-api-laravel/commit/3db896a57091e13c83cb2f134539870da44ef10c);
- Our [custom user profile](https://github.com/carbonaut/boilerplate-api-laravel/commit/4489b533fe24f0a6148c82d8cdb92cb42ba5d5c8) solution was removed. You should either use [spatie/laravel-permission](https://github.com/spatie/laravel-permission) or wait until we release our custom package;
- Currently the emails are only being dispatched into the queue and sent to the user. Ideally we'd like to keep track of the emails that were sent, their send status, recipient and even a route to keep track if the user has opened the email. The [previous code](https://github.com/carbonaut/boilerplate-api-laravel/commit/3422f84f49bac6212edf3ce968aa3e90c4e66a64) used by this boilerplate was removed in favor of a proper implementation that should be done in the future;