<?php

namespace App\Services;

use App\Models\LanguageLine;

class LanguageLineService
{
    /**
     * Create language lines based on a pre-determined array type. The structure is as follow.
     *
     *     private $groups = [
     *         'api' => [
     *             'LANGUAGE_LINE_KEY' => [
     *                 'en'    => 'English translation.',
     *                 'pt_BR' => 'Portuguese translation.',
     *             ],
     *         ],
     *     ];
     *
     * @param array<string, array<string, array<string, string>>> $groups
     * @param bool                                                $updateExisting update if a matching language line already exists
     *
     * @return void
     */
    public static function createLanguageLines(array $groups, bool $updateExisting = true): void
    {
        foreach ($groups as $group => $keys) {
            foreach ($keys as $key => $translations) {
                // Find existing language lines by group and key
                $languageLine = LanguageLine::firstOrNew([
                    'group' => $group,
                    'key'   => $key,
                ]);

                // If the language line already exists and we should not update existing ones, skip the iteration
                if ($languageLine->exists && !$updateExisting) {
                    continue;
                }

                // Set the values and save
                $languageLine->text = $translations;
                $languageLine->save();
            }
        }
    }

    /**
     * Delete language lines based on a pre-determined array type. The structure is as follow.
     *
     *     private $languageLines = [
     *         'api' => [
     *             'LANGUAGE_LINE_KEY' => [
     *                 'en'    => 'English translation.',
     *                 'pt_BR' => 'Portuguese translation.',
     *             ],
     *         ],
     *     ];
     *
     * @param array<string, array<string, array<string, string>>> $groups
     *
     * @return void
     */
    public static function deleteLanguageLines(array $groups): void
    {
        foreach ($groups as $group => $keys) {
            foreach ($keys as $key => $translations) {
                // Delete language line by key and type
                LanguageLine::where('group', $group)->where('key', $key)->delete();
            }
        }
    }
}
