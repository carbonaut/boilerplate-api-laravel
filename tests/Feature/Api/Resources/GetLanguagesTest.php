<?php

namespace Tests\Feature\Api\Resources;

use App\Enums\Language;
use App\Http\Controllers\Api\ResourcesController;
use App\Http\Resources\Models\LanguageResource;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Resources')]
#[CoversMethod(Language::class, 'getLabel')]
#[CoversMethod(ResourcesController::class, 'getLanguages')]
#[CoversMethod(LanguageResource::class, 'toArray')]
class GetLanguagesTest extends TestCase
{
    /**
     * The route endpoint.
     *
     * @var string
     */
    private const Endpoint = '/resources/languages';

    /**
     * Setup the test environment.
     *
     * WARNING: Be careful when adding code here, as this setUp() method
     * is called before each test for each data set on this class.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Asserts the GET /resources/languages route returns the expected languages.
     *
     * @return void
     */
    public function testReturnsExpectedLanguages(): void
    {
        $response = $this->getJson(self::Endpoint);

        $response
            ->assertOk()
            ->assertExactJson([
                [
                    'label' => Language::English->label(),
                    'value' => Language::English->value,
                ],
                [
                    'label' => Language::BrazilianPortuguese->label(),
                    'value' => Language::BrazilianPortuguese->value,
                ],
            ])
        ;
    }
}
