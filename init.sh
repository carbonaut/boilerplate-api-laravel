#!/bin/sh

# Install dependencies
if [ ! -d './vendor' ]; then
    composer install
fi

# Copy .env examples
if [ ! -f './.env' ]; then 
    cp '.env.example.local' '.env'
fi
if [ ! -f './.env.test' ]; then 
    cp '.env.example.test' '.env.test'
fi

# Run migrations
php artisan migrate 

# Install OAuth keys
if [ ! -f 'storage/oauth-public.key' ] || [ ! -f 'storage/oauth-private.key' ]; then
    php artisan passport:keys
    php artisan passport:install
    php artisan key:generate 
fi

# Serve
php artisan serve --host 0.0.0.0
