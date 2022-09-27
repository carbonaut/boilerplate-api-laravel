<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * The default test env file.
     *
     * @var string
     */
    public string $currentEnvFile = '.env.test';

    /**
     * Creates the application.
     *
     * WARNING: Avoid adding code here, as this method
     * is called before each test for each data set.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication(): Application
    {
        // Fetch application;
        $app = require __DIR__ . '/../bootstrap/app.php';

        // Load the conf from the env file;
        $app->loadEnvironmentFrom($this->currentEnvFile);

        // Bootstraps the app;
        $app->make(Kernel::class)
            ->bootstrap();

        return $app;
    }
}
