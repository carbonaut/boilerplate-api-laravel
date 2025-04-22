<?php

namespace Tests\Feature\Api\Resources;

use App\Enums\LanguageLineGroup;
use App\Http\Controllers\Api\ResourcesController;
use App\Http\Resources\Models\LanguageLineResource;
use App\Models\LanguageLine;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Resources')]
#[CoversMethod(ResourcesController::class, 'getLanguageLinesByGroup')]
#[CoversMethod(LanguageLineResource::class, 'toArray')]
class GetLanguageLinesByGroupTest extends TestCase
{
    /**
     * The route subdomain.
     *
     * @var null|string
     */
    protected $subdomain = 'api';

    /**
     * The route path.
     *
     * @var string
     */
    protected $path = '/resources/language-lines/{group}';

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
        $response = $this->getJson($this->uri(['{group}' => 'invalid-group']));

        $response->assertNotFound();
    }

    /**
     * Asserts the route returns an empty array when passing an empty group.
     */
    public function testReturnsEmptyArrayOnEmptyGroup(): void
    {
        $response = $this->getJson($this->uri([
            '{group}' => LanguageLineGroup::randomCase()->value,
        ]));

        $response
            ->assertOk()
            ->assertExactJson([])
        ;
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
            ->create()
        ;

        $response = $this
            ->getJson($this->uri(['{group}' => $languageLine->group->value]))
        ;

        $response
            ->assertOk()
            ->assertExactJson([
                [
                    'key'  => $languageLine->key,
                    'text' => $languageLine->text[$locale],
                ],
            ])
        ;
    }

    /**
     * Asserts the route respects the Accept-Language header.
     *
     * @param string $language
     */
    #[DataProviderExternal(\App\Enums\Language::class, 'asDataProvider')]
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
                $this->uri(['{group}' => $languageLine->group->value]),
            )
        ;

        $response
            ->assertOk()
            ->assertExactJson([
                [
                    'key'  => $languageLine->key,
                    'text' => $languageLine->text[$language],
                ],
            ])
        ;
    }
}
