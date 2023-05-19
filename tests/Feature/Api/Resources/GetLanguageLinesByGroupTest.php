<?php

namespace Tests\Feature\Api\Resources;

use App\Enums\LanguageLineGroup;
use App\Models\LanguageLine;
use Tests\TestCase;

/**
 * @internal
 *
 * @group Api\Resources
 *
 * @covers \App\Http\Controllers\Api\ResourcesController::getLanguageLinesByGroup
 * @covers \App\Http\Resources\Models\LanguageLineResource::toArray
 */
class GetLanguageLinesByGroupTest extends TestCase
{
    /**
     * The route endpoint.
     *
     * @var string
     */
    private const Endpoint = '/resources/language-lines/{group}';

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
     */
    public function testReturnsErrorOnInvalidGroup(): void
    {
        $response = $this->getJson(strtr(self::Endpoint, ['{group}' => 'invalid-group']));

        $response->assertNotFound();
    }

    /**
     * Asserts the route returns an empty array when passing an empty group.
     */
    public function testReturnsEmptyArrayOnEmptyGroup(): void
    {
        $response = $this->getJson(strtr(self::Endpoint, [
            '{group}' => LanguageLineGroup::randomCase()->value,
        ]));

        $response
            ->assertOk()
            ->assertExactJson([]);
    }

    /**
     * Asserts the route returns a valid array for a given group.
     */
    public function testReturnsSuccessOnValidGroup(): void
    {
        $locale = config('app.locale');
        assert(is_string($locale));

        $languageLine = LanguageLine::factory()
            ->withLocale($locale)
            ->create();

        $response = $this
            ->getJson(strtr(self::Endpoint, ['{group}' => $languageLine->group->value]));

        $response
            ->assertOk()
            ->assertExactJson([
                [
                    'key'  => $languageLine->key,
                    'text' => $languageLine->text[$locale],
                ],
            ]);
    }

    /**
     * Asserts the route respects the Accept-Language header.
     *
     * @dataProvider App\Enums\Language::asDataProvider
     *
     * @param string $language
     */
    public function testRespectsAcceptLanguageHeader(string $language): void
    {
        // Create a API language line;
        $languageLine = LanguageLine::factory()->create([
            'text' => [
                'en'    => fake()->sentence(),
                'pt_BR' => fake()->sentence(),
            ],
        ]);

        $response = $this
            ->withHeader('Accept-Language', $language)
            ->get(
                strtr(self::Endpoint, ['{group}' => $languageLine->group->value]),
            );

        $response
            ->assertOk()
            ->assertExactJson([
                [
                    'key'  => $languageLine->key,
                    'text' => $languageLine->text[$language],
                ],
            ]);
    }
}
