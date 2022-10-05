<?php

namespace Tests\Feature\Api\Resources;

use App\Models\LanguageLine;
use Tests\TestCase;

/**
 * @internal
 *
 * @group Api\Resources
 *
 * @coversNothing
 */
class GetLanguageLinesByGroupTest extends TestCase
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
    private string $endpoint = '/resources/language-lines/{group}';

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
     * Asserts the route returns an error when accesing an invalid group.
     *
     * @covers \App\Http\Controllers\Api\ResourcesController::getLanguageLinesByGroup
     *
     * @return void
     */
    public function testReturnsErrorOnInvalidGroup(): void
    {
        $response = $this->json($this->method, strtr($this->endpoint, ['{group}' => 'invalid-group']));

        $response->assertNotFound();
    }

    /**
     * Asserts the route returns an empty array when passing an empty group.
     *
     * @covers \App\Http\Controllers\Api\ResourcesController::getLanguageLinesByGroup
     *
     * @return void
     */
    public function testReturnsEmptyArrayOnEmptyGroup(): void
    {
        // Assert that no API line exists
        $this->assertDatabaseMissing('language_lines', ['group' => 'api']);

        $response = $this->json($this->method, strtr($this->endpoint, ['{group}' => 'api']));

        $response
            ->assertOk()
            ->assertExactJson([]);
    }

    /**
     * Asserts the route returns a valid array for a given group.
     *
     * @covers \App\Http\Controllers\Api\ResourcesController::getLanguageLinesByGroup
     * @covers \App\Http\Resources\Models\LanguageLineResource::toArray
     *
     * @return void
     */
    public function testReturnsSuccessOnValidGroup(): void
    {
        // Create a API language line;
        LanguageLine::factory()->create([
            'group' => 'api',
            'key'   => 'EXEMPLE_KEY',
            'text'  => [
                'en' => 'Example Text',
            ],
        ]);

        // Assert the API line exists;
        $this->assertDatabaseCount('language_lines', 1);
        $this->assertDatabaseHas('language_lines', ['group' => 'api']);

        $response = $this->json($this->method, strtr($this->endpoint, ['{group}' => 'api']));

        $response
            ->assertOk()
            ->assertExactJson([
                [
                    'key'  => 'EXEMPLE_KEY',
                    'text' => 'Example Text',
                ],
            ]);
    }

    /**
     * Asserts the route respects the Accept-Language header.
     *
     * @covers \App\Http\Controllers\Api\ResourcesController::getLanguageLinesByGroup
     * @covers \App\Http\Resources\Models\LanguageLineResource::toArray
     *
     * @dataProvider localizedLanguageLine
     *
     * @param string $language
     * @param string $language_line
     *
     * @return void
     */
    public function testRespectsAcceptLanguageHeader(string $language, string $language_line): void
    {
        // Create a API language line;
        LanguageLine::factory()->create([
            'group' => 'api',
            'key'   => 'EXEMPLE_KEY',
            'text'  => [
                'en'    => 'Example Text',
                'pt_BR' => 'Texto de Exemplo',
            ],
        ]);

        $response = $this->json(
            $this->method,
            strtr($this->endpoint, [
                '{group}' => 'api',
            ]),
            [],
            ['Accept-Language' => $language]
        );

        $response
            ->assertOk()
            ->assertExactJson([
                [
                    'key'  => 'EXEMPLE_KEY',
                    'text' => $language_line,
                ],
            ]);
    }
}
