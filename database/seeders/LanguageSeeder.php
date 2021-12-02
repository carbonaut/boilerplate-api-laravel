<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $languages = collect([
            [
                'locale' => 'en',
                'name'   => 'English (United States)',
            ],
            [
                'locale' => 'de-at',
                'name'   => 'Deutsch (Österreich)',
            ],
        ]);

        $languages->each(function ($language) {
            Language::factory()->create([
                'name'   => $language['name'],
                'locale' => $language['locale'],
            ]);
        });
    }
}
