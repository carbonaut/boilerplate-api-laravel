<?php

namespace Tests\Feature\Api\Resources;

use Tests\TestCase;

/**
 * @internal
 *
 * @group Api\Resources
 *
 * @coversNothing
 */
class GetLanguagesTest extends TestCase
{
    use DataProvider;

    /**
     * The method for the route endpoint.
     *
     * @var string
     */
    private string $method = 'GET';

    /**
     * The route endpoint.
     *
     * @var string
     */
    private string $endpoint = '/resources/languages';

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
     * @covers \App\Enums\Language::getLabel
     * @covers \App\Http\Controllers\Api\ResourcesController::getLanguages
     * @covers \App\Http\Resources\Models\LanguageResource::toArray
     *
     * @dataProvider availableLanguages
     *
     * @param array<string, array<int, array<int, array<string, string>>>> $languages
     *
     * @return void
     */
    public function testReturnsExpectedLanguages(array $languages): void
    {
        $response = $this->json($this->method, $this->endpoint);

        $response
            ->assertOk()
            ->assertExactJson($languages);
    }
}
