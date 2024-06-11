<?php

namespace Tests\Unit\App\Services\LanguageLine;

use App\Enums\Language;
use App\Models\LanguageLine;
use App\Services\LanguageLineService;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('App\Services')]
#[CoversMethod(LanguageLineService::class, 'deleteLanguageLines')]
class DeleteLanguageLinesTest extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Asserts the service deletes phrases from a valid structure.
     *
     * @return void
     */
    public function testSuccessOnDeletingFromValidStructure(): void
    {
        // Starting with an empty table
        $this->assertDatabaseCount('language_lines', 0);

        $languageLine = LanguageLine::factory()->create([
            'text' => [
                'en'    => fake()->sentence(),
                'pt_BR' => fake()->sentence(),
            ],
        ]);

        // Assert the lines were created
        $this->assertDatabaseCount('language_lines', 1);

        // Delete the language lines
        LanguageLineService::deleteLanguageLines([
            $languageLine->group->value => [
                $languageLine->key => [
                    'en'    => $languageLine->text['en'],
                    'pt_BR' => $languageLine->text['pt_BR'],
                ],
            ],
        ]);

        // Assert the lines were deleted
        $this->assertDatabaseCount('language_lines', 0);
    }

    /**
     * Asserts the service deletes phrases from a valid structure.
     *
     * @return void
     */
    public function testSuccessOnDeletingFromShortStructure(): void
    {
        // Starting with an empty table
        $this->assertDatabaseCount('language_lines', 0);

        $languageLine = LanguageLine::factory()->create();

        // Assert the lines were created
        $this->assertDatabaseCount('language_lines', 1);

        // Delete the language lines
        LanguageLineService::deleteLanguageLines([
            $languageLine->group->value => [
                $languageLine->key => [
                ],
            ],
        ]);

        // Assert the lines were deleted
        $this->assertDatabaseCount('language_lines', 0);
    }
}
