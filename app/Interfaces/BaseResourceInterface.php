<?php

namespace App\Interfaces;

interface BaseResourceInterface
{
    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     *
     * @return void
     */
    public function __construct($resource);
}
