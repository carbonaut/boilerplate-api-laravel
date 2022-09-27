<?php

namespace Tests\Unit\Services;

use App\Models\LanguageLine;
use App\Services\LanguageLineService;
use Tests\TestCase;
use ValueError;

/**
 * @internal
 *
 * @coversNothing
 */
class LanguageLineServiceTest extends TestCase
{
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
     * Asserts the service fails when creating phrases from invalid structure.
     *
     * @group App\Services
     *
     * @covers \App\Services\LanguageLineService::createLanguageLines
     *
     * @return void
     */
    public function testFailsOnCreatingFromInvalidStructure(): void
    {
        $this->expectException(ValueError::class);

        LanguageLineService::createLanguageLines([
            'LANGUAGE_LINE_KEY' => [
                'en'    => 'English translation.',
                'pt_BR' => 'Portuguese translation.',
            ],
        ]);
    }

    /**
     * Asserts the service creates phrase from a valid structure.
     *
     * @group App\Services
     *
     * @covers \App\Services\LanguageLineService::createLanguageLines
     *
     * @return void
     */
    public function testSuccessOnCreatingFromValidStructure(): void
    {
        // Starting with an empty table
        $this->assertDatabaseCount('language_lines', 0);

        LanguageLineService::createLanguageLines([
            'api' => [
                'LANGUAGE_LINE_KEY' => [
                    'en'    => 'English translation.',
                    'pt_BR' => 'Portuguese translation.',
                ],
            ],
        ]);

        // Assert the API line exists;
        $this->assertDatabaseHas('language_lines', [
            'group' => 'api',
            'key'   => 'LANGUAGE_LINE_KEY',
            'text'  => json_encode([
                'en'    => 'English translation.',
                'pt_BR' => 'Portuguese translation.',
            ]),
        ]);
    }

    /**
     * Asserts the service respects the updateExisting flag.
     *
     * @group App\Services
     *
     * @covers \App\Services\LanguageLineService::createLanguageLines
     *
     * @return void
     */
    public function testServiceRespectsTheUpdateExistingFlag(): void
    {
        // Create a base line
        LanguageLine::factory()->create([
            'group' => 'api',
            'key'   => 'LANGUAGE_LINE_KEY',
            'text'  => [
                'en'    => 'English translation.',
                'pt_BR' => 'Portuguese translation.',
            ],
        ]);

        // Re-create the phrase with a different translation, but without updating.
        LanguageLineService::createLanguageLines([
            'api' => [
                'LANGUAGE_LINE_KEY' => [
                    'en'    => 'New English translation.',
                    'pt_BR' => 'New Portuguese translation.',
                ],
            ],
        ], false);

        // Assert the text didn't change.
        $this->assertDatabaseHas('language_lines', [
            'group' => 'api',
            'key'   => 'LANGUAGE_LINE_KEY',
            'text'  => json_encode([
                'en'    => 'English translation.',
                'pt_BR' => 'Portuguese translation.',
            ]),
        ]);

        // Re-create the phrase with a different translation, forcing update if exists.
        LanguageLineService::createLanguageLines([
            'api' => [
                'LANGUAGE_LINE_KEY' => [
                    'en'    => 'New English translation.',
                    'pt_BR' => 'New Portuguese translation.',
                ],
            ],
        ], true);

        // Assert the text has changed.
        $this->assertDatabaseHas('language_lines', [
            'group' => 'api',
            'key'   => 'LANGUAGE_LINE_KEY',
            'text'  => json_encode([
                'en'    => 'New English translation.',
                'pt_BR' => 'New Portuguese translation.',
            ]),
        ]);
    }

    /**
     * Asserts the service deletes phrases from a valid structure.
     *
     * @group App\Services
     *
     * @covers \App\Services\LanguageLineService::deleteLanguageLines
     *
     * @return void
     */
    public function testSuccessOnDeletingFromValidStructure(): void
    {
        $languageLines = [
            'api' => [
                'LANGUAGE_LINE_KEY' => [
                    'en'    => 'English translation.',
                    'pt_BR' => 'Portuguese translation.',
                ],
            ],
        ];

        // Starting with an empty table
        $this->assertDatabaseCount('language_lines', 0);

        // Create the language lines
        LanguageLineService::createLanguageLines($languageLines);

        // Assert the lines were created
        $this->assertDatabaseCount('language_lines', 1);

        // Delete the language lines
        LanguageLineService::deleteLanguageLines($languageLines);

        // Assert the lines were deleted
        $this->assertDatabaseCount('language_lines', 0);
    }
}
