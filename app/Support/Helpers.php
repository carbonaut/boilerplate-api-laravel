<?php

namespace App\Support;

use App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class Helpers
{
    /**
     * Filter a multidimensional array to only display desired keys.
     *
     * @param array $array      array with the data to be filtered
     * @param array $attributes array of attributes in dot.notation to use as filter
     *
     * @return array array with only the desired attributes
     */
    public static function recursive_array_only($array, $attributes)
    {
        $attributes = (array) $attributes;

        $attributes_array = [];

        // Convert a dot.notation array into multidimensional array
        foreach ($attributes as $attribute) {
            Arr::set($attributes_array, $attribute, null);
        }

        // Recursively set the fields to display
        return self::recursive_array_only_do($array, $attributes_array);
    }

    /**
     * Recursively iterates an array to display only desired keys.
     *
     * @param array $array1 array with the data to be filtered
     * @param array $array2 multidimensional array of attributes to use as filter
     *
     * @return array array with only the desired attributes
     */
    protected static function recursive_array_only_do(array $array1, array $array2)
    {
        // Store if current array is an "object" or "array of objects"
        $multidimensional = null;

        foreach ($array2 as $key => $value) {
            // If the filter is set as array, it means it's an array of "objects"
            if ($key == 'array') {
                $multidimensional = [];
                // In this case we have to iterate and filter item per item
                if (is_array($value)) {
                    foreach ($array1 as &$arr1) {
                        $multidimensional[] = self::recursive_array_only_do($arr1, $value);
                    }
                    // Or just return if filter for itens aren't set
                } else {
                    return $array1;
                }
                // If just an "object" filter without iterating
            } else {
                // If it has sub-filter, recursively filter it
                if (is_array($value) && isset($array1[$key])) {
                    if ($array1[$key] instanceof Illuminate\Support\Collection) {
                        $array1[$key] = $array1[$key]->toArray();
                    }

                    $array1[$key] = self::recursive_array_only_do($array1[$key], $value);
                }
            }
        }

        // Return the array intersection if there is no more levels to descend to
        if ($multidimensional === null) {
            return array_intersect_key($array1, $array2);
            // Or the list of objects
        }

        return $multidimensional;
    }

    /**
     * Returns date string formatted based on the locale.
     *
     * @param string $date
     *
     * @return string
     */
    public static function format_localized_date(string $date)
    {
        $parsed_date = Carbon::parse($date);

        switch (App::getLocale()) {
            case 'en':
                return $parsed_date->locale(App::getLocale())->isoFormat('dddd, MMMM Do');
            case 'de-at':
                return $parsed_date->locale(App::getLocale())->isoFormat('dddd, DD. MMMM');
            default:
                return $parsed_date->locale(App::getLocale())->isoFormat('dddd, MMMM Do');
        }
    }

    /**
     * Returns time string formatted based on the locale.
     *
     * @param string $time
     *
     * @return string
     */
    public static function format_localized_time(string $time)
    {
        $time = Carbon::parse($time);

        switch (App::getLocale()) {
            case 'en':
                return $time->locale(App::getLocale())->isoFormat('hh:mm A');
            case 'de-at':
                return $time->locale(App::getLocale())->isoFormat('HH:mm');
            default:
                return $time->locale(App::getLocale())->isoFormat('hh:mm A');
        }
    }

    /**
     * Returns a formatted short date string based on the users locale.
     *
     * Example: '1995-02-13' => '13.02.1995' (de-at)
     *          '1995-02-13' => '13/02/1995' (en)
     *
     * @param string $date
     *
     * @return string
     */
    public static function format_localized_short_date(string $date)
    {
        $parsed_date = Carbon::parse($date);
        switch (App::getLocale()) {
        case 'en':
            return $parsed_date->locale(App::getLocale())->isoFormat('DD/MM/YYYY');
        case 'de-at':
            return $parsed_date->locale(App::getLocale())->isoFormat('DD.MM.YYYY');
        default:
            return $parsed_date->locale(App::getLocale())->isoFormat('DD/MM/YYYY');
        }
    }

    /**
     * Anonymize an IP Address (V4 or V6).
     *
     * @param string $ip
     *
     * @return string
     */
    public static function anonymize_ip_address(string $ip)
    {
        return preg_replace(['/\.\d*$/', '/([\da-f]*:){4}[\da-f]*$/'], ['.0', '::::'], $ip);
    }

    /**
     * Return the namespace from a php file.
     *
     * @param string $path of the file to look for
     *
     * @return string namespace or null if namespace is not found in file
     */
    public static function getNamespace(string $path)
    {
        // Get the contents of the file in the directory
        $file = file_get_contents($path);

        // Namespace must be declared on its own line, starting with "namespace" (no spaces)
        if (preg_match('#namespace\s+(.+?);+#sm', $file, $match)) {
            return $match[1];
        }

        return null;
    }

    /**
     * Return classes from a directory.
     *
     * @param string $dir directory to look for
     *
     * @return Collection array with classes in the specified directory
     */
    public static function getClasses(string $dir)
    {
        $classes = collect();
        $files = new Finder();

        // Find all php files in the directory parameter
        $files->files()->in($dir);

        foreach ($files as $file) {
            $namespace = Helpers::getNamespace($file->getRealPath());
            $class = new ReflectionClass('\\' . $namespace . '\\' . $file->getBasename('.php'));
            $classes->push($class);
        }

        return $classes;
    }
}
