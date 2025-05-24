<?php

use App\Enums\ChoiceType;
use App\Enums\SentimentCategory;
use Illuminate\Support\Facades\Cache;

if (!function_exists('formatMsisdnInLocal')) {
    /**
     * @param string $msisdn
     *
     * @return string
     */
    function formatMsisdnInLocal(string $msisdn):string
    {
        if(strlen($msisdn) > 11)
        {
            $offset = strlen($msisdn) - 11;
            $length = strlen($msisdn);
            return substr($msisdn, $offset, $length);
        }
        return $msisdn;
    }
}

if (!function_exists('getSelectionTypes')) {
    /**
     *
     * @return array
     */
    function getSelectionTypes():array
    {
        return array_column(ChoiceType::cases(), 'value');
    }
}

if (!function_exists('getSentimentCaterories')) {
    /**
     *
     * @return array
     */
    function getSentimentCaterories():array
    {
        return array_column(SentimentCategory::cases(), 'value');
    }
}

if (!function_exists('checkAndUnsetRecusiveArrayKey')) {
    /**
     * @param array|bool $data
     * @param array $keys
     *
     * @return void
     */
    function checkAndUnsetRecusiveArrayKey(array|bool &$data, array $keys):void
    {
        $current = &$data; // Reference to the current level
        foreach ($keys as $key) {
          if (!is_array($current) || !array_key_exists($key, $current)) {
            return; // Key not found
          }

          if ($key === end($keys)) { // If it's the last key, check for match
            if (isset($current[$key])) { // Check if key exists before unsetting
              unset($current[$key]); // Unset the key
            }
          } else {
            $current = &$current[$key]; // Descend into the nested level
          }
        }
    }
}

if (!function_exists('deleteCacheDataByTableName')) {
    /**
     * @param string $tableName
     *
     * @return void
     */
    function deleteCacheDataByTableName($tableName):void
    {
        $tableWiseCacheKeys = config("cache.table_wise_cache_keys.{$tableName}");
        if(!empty($tableWiseCacheKeys)) {
            foreach ($tableWiseCacheKeys as $value) {
                Cache::deleteMatching($value);
            }
        }
    }
}

if (!function_exists('generateRandomString')) {
    /**
     * @param string $tableName
     *
     * @return void
     */
    function generateRandomString($maxLength = 8) {

        $upperCase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowerCase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';

        $randomString = '';
        $randomString .= $upperCase[random_int(0, strlen($upperCase) - 1)];
        $randomString .= $lowerCase[random_int(0, strlen($lowerCase) - 1)];
        $randomString .= $numbers[random_int(0, strlen($numbers) - 1)];

        $remainingLength = random_int(0, $maxLength - 4);

        $allChars = $upperCase . $lowerCase . $numbers;
        for ($i = 0; $i < $remainingLength; $i++) {
            $randomString .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        $randomString = str_shuffle($randomString);

        return $randomString;
    }
}

if (!function_exists('getTableNameFromQuery')) {
    /**
     * @param string $query
     *
     * @return [type]
     */
    function getTableNameFromQuery(string $query) {
        // Define the regex patterns
        $patterns = [
            '/from\s+`?(\w+)`?\s+/i',  // For SELECT queries
            '/into\s+`?(\w+)`?\s+/i',  // For INSERT queries
            '/update\s+`?(\w+)`?\s+/i' // For UPDATE queries (if needed in the future)
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $query, $matches)) {
                return $matches[1];
            }
        }

        return null; // If no match found
    }
}
