<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Uri;

class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;

    /**
     * The route subdomain.
     *
     * @var null|string
     */
    protected $subdomain;

    /**
     * The route path.
     *
     * @var string
     */
    protected $path;

    /**
     * Builds a URI for the given subdomain and path.
     *
     * @param array<string, string> $replacements
     * @param null|string           $path
     *
     * @return Uri
     */
    protected function uri(array $replacements = [], $path = null): Uri
    {
        return (new Uri())
            ->withHost(
                ($this->subdomain ? "{$this->subdomain}." : '') . Uri::of(Config::string('app.url'))->host()
            )
            ->withPath(
                strtr($path ?? $this->path, $replacements)
            )
            ->withScheme('http')
        ;
    }
}
