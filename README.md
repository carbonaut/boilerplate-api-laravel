<h1 style="display: flex;align-items:center;">
<img src="https://user-images.githubusercontent.com/20388082/89651773-cb900d00-d89a-11ea-99bb-d5e97b1609d0.png" width="50" alt="Carbonaut Logo" style="margin-right:5px;">
Carbonaut's Laravel API Boilerplate
</h1>

![integration](https://github.com/carbonaut/boilerplate-api-laravel/workflows/integration/badge.svg)

Nothing better than getting to action without worrying about the repetitive and boring 
parts of bootstrapping a project, right?

That's why we've built this awesome boilerplate so you can focus on your project's ideas and get the boring, albeit important, parts out of the way.

- Language: PHP 7.3
- Framework: Laravel 7
- Database: PostgreSQL 12

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
- [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) configuration file for code styling

## Getting started

### With Docker
First, make sure to have **Docker** and **Docker Compose** correctly set up.

Then, you can run the following:
```sh  
$ git clone https://github.com/carbonaut/boilerplate-api-laravel
$ cd boilerplate-api-laravel
$ docker-compose up
# Some time later...
$ docker-compose down
```
With just that, you should be up and running and have the documentation available at `http://api.localhost:8000`

No need to install PHP and its extensions or PostgreSQL.

If you ever need extra extensions or native dependencies, 
you can refer to the provided `Dockerfile` and customize whatever the built container has.

Additionally, you can customize the `docker_postgres_init.sql` file for initial bootstrapping of the PostgreSQL database. 
It's important to note that this file is only sourced when creating the `postgres` Docker volume.

### Existing environment
You also have the option to use your existing local environment for PHP and PostgreSQL.

Just ignore everything related to Docker and Docker Compose and change the defaults from the 
`.env.example` files and you're good to go.

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
# Docs at http://api.localhost:8000
```

## Additional Info and Troubleshooting 
- **Important**: If you run into any problem related to permissions, it may be necessary to rebuild the API image with your user and group ID.
  This is because Docker runs as root by default which leaves us with permission issues for any    files created inside the container. This is why we want to run the container as the current user.
  
  You can see your User ID and Group ID as follows:
  ```sh
  $ echo $UID
  1000
  $ echo $GID
  998
  ```

  You can then edit the `docker-compose.yml` file with your IDs:
  ```yaml
  [...]
  api:
  build:
      [...]
      args:
      UID: 1000 # <- Change the UID here
      GID: 998 # <- Change the GID here
  [...]
  ```
  And rebuild the image:
  ```sh
  $ docker-compose up --build
  ```

- You can run the containers in the background with the `-d` flag:
  ```
  docker-compose up -d 
  ```
  If later you want to see the logs for a container, you can use the `logs` command
  For example, you can attach to the logs of the `api` container like so:
  ```
  docker-compose logs --follow api 
  ```

- To run the commands inside the containers, you can run the following pattern:

  `docker-compose exec $service_name $command`

  For example, running `composer` and `artisan` inside the API container is simple: 
  ```sh 
  $ docker-compose exec api composer require abc/def
  $ docker-compose exec api composer dump-autload
  $ docker-compose exec api php artisan migrate
  $ docker-compose exec api php artisan make:model Astrounauts
  ```
  Another example is if you want to connect to the database:
  ```sh 
  $ docker-compose exec database psql -U carbonaut -d api
  ```

  You could also startup a shell, like Bash, inside the container and send commands from it:
  ```sh
  $ docker-compose exec api bash 
  # Now, you're inside the container
  $ php artisan migrate
  ```

## Contributing

If you find any problems, please report them with [Issues](https://github.com/carbonaut/boilerplate-api-laravel/issues).

Also, PRs are always welcome :)
