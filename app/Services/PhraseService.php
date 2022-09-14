<?php

namespace App\Services;

use App\Models\Phrase;

class PhraseService
{
    /**
     * Create phrases based on a pre-determined array type. The structure is as follow.
     *
     *     private $phrases = [
     *         'api' => [
     *             'PHRASE_KEY' => [
     *                 'en'    => 'English translation.',
     *                 'pt-BR' => 'Portuguese translation.',
     *             ],
     *         ],
     *     ];
     *
     * @param array $data
     * @param bool  $updateExisting update if a matching phrase already exists
     *
     * @return void
     */
    public static function createPhrases(array $data, bool $updateExisting = true): void
    {
        foreach ($data as $type => $phrases) {
            foreach ($phrases as $key => $translations) {
                // Find existing phrase by key and type
                $phrase = Phrase::firstOrNew([
                    'type' => $type,
                    'key'  => $key,
                ]);

                // If the phrase already exists and we should not update existing ones, skip the iteration
                if ($phrase->exists && !$updateExisting) {
                    continue;
                }

                // Set the values and save
                $phrase->setTranslations('value', $translations);
                $phrase->save();
            }
        }
    }

    /**
     * Delete phrases based on a pre-determined array type. The structure is as follow.
     *
     *     private $phrases = [
     *         'api' => [
     *             'PHRASE_KEY' => [
     *                 'en'    => 'English translation.',
     *                 'pt-BR' => 'Portuguese translation.',
     *             ],
     *         ],
     *     ];
     *
     * @param array $data
     *
     * @return void
     */
    public static function deletePhrases(array $data): void
    {
        foreach ($data as $type => $phrases) {
            foreach ($phrases as $key => $translations) {
                // Delete phrase by key and type
                Phrase::where('type', $type)->where('key', $key)->delete();
            }
        }
    }
}
