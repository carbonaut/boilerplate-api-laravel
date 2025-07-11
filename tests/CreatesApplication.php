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
        $app = require Application::inferBasePath() . '/bootstrap/app.php';
        assert($app instanceof Application);

        // Load the conf from the env file;
        $app->loadEnvironmentFrom($this->currentEnvFile);

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
