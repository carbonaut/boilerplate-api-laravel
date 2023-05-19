<?php

namespace Tests\Unit\App\Services\LanguageLine;

use App\Models\LanguageLine;
use App\Services\LanguageLineService;
use Tests\TestCase;
use ValueError;

/**
 * @internal
 *
 * @group App\Services
 *
 * @covers \App\Services\LanguageLineService::createLanguageLines
 */
class CreateLanguageLinesTest extends TestCase
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
     * Asserts the service fails when creating phrases from invalid structure.
     *
     * @return void
     */
    public function testFailsOnCreatingFromInvalidStructure(): void
    {
        $this->expectException(ValueError::class);

        LanguageLineService::createLanguageLines([
            'invalid-key' => [
                'LANGUAGE_LINE_KEY' => [
                    'en'    => 'English translation.',
                    'pt_BR' => 'Portuguese translation.',
                ],
            ],
        ]);
    }

    /**
     * Asserts the service creates phrase from a valid structure.
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
}
