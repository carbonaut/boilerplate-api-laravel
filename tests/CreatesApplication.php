<?php

namespace Tests;

use Exception;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    public $currentEnvFile = '.env.test';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        if (is_file($this->currentEnvFile)) {
            $app->loadEnvironmentFrom($this->currentEnvFile);
        } else {
            throw new Exception("Configuration file \"{$this->currentEnvFile}\" not found.");
            exit();
        }

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
