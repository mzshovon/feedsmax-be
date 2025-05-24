<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Dhaka',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\BindServiceProvider::class,
        // App\Providers\RedisExceptionHandlerServiceProvider::class,

    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
    ])->toArray(),

    'survey_url' => env('SURVEY_URL', 'http://localhost:3000/survey'),
    'nps' => [
        "question_en" => "How likely are you to recommend MyBL App to a friends or family member?",
        "question_bn" => " আপনি MyBL অ্যাপ ব্যবহার করতে আপনার বন্ধু বা পরিবারের সদস্যকে কতটুকু উৎসাহিত করবেন?",
        "selection_type"=> "rating_0_10_number",
        "options" => '[{"id":"1","value":"0","title_bn":"0","title_en":"0"},
                        {"id":"2","value":"1","title_bn":"\u09e7","title_en":"1"},
                        {"id":"3","value":"2","title_bn":"\u09e8","title_en":"2"},
                        {"id":"4","value":"3","title_bn":"\u09e9","title_en":"3"},
                        {"id":"5","value":"4","title_bn":"\u09ea","title_en":"4"},
                        {"id":"6","value":"5","title_bn":"\u09eb","title_en":"5"},
                        {"id":"7","value":"6","title_bn":"\u09ec","title_en":"6"},
                        {"id":"8","value":"7","title_bn":"\u09ed","title_en":"7"},
                        {"id":"9","value":"8","title_bn":"\u09ee","title_en":"8"},
                        {"id":"10","value":"9","title_bn":"\u09ef","title_en":"9"},
                        {"id":"11","value":"10","title_bn":"\u09e7\u09e6","title_en":"10"}
                    ]',
        "range"=> "0-0",
        "status"=> 1,
        "children"=> [],
        "nps_rating_mapping"=> [
            "en"=> [
                "low"=> "Not at all likely",
                "high"=> "Extremely likely"
            ],
            "bn"=> [
                "low"=> "মোটেই না",
                "high"=> "অবশ্যই"
                ]
            ]
    ],
    'csat' => [
        "question_en" => "How likely are you to recommend MyBL App to a friends or family member?",
        "question_bn" => " আপনি MyBL অ্যাপ ব্যবহার করতে আপনার বন্ধু বা পরিবারের সদস্যকে কতটুকু উৎসাহিত করবেন?",
        "selection_type"=> "rating_0_1_no-yes",
        "options" => '[{"id":"1","value":"0","title_bn":"\u09a8\u09be","title_en":"No"},
                        {"id":"2","value":"1","title_bn":"\u09b9\u09cd\u09af\u09be\u0981","title_en":"Yes"}]',
        "range"=> "0-0",
        "status"=> 1,
        "children"=> [],
    ],
    'default_theme' => [
        'error' => [
            "403" => [
                "header" => [
                    "text_en" => "Sorry!",
                    "text_bn" => "দুঃখিত!",
                    "text_color" => "#f16522",
                    "bg_color" => "#f16522"
                ],
                "content" => [
                    "text_en" => "Feedback submission failed.",
                    "text_bn" => "মতামত প্রদান সফল হয়নি।",
                    "text_color" => "#000",
                ],
                "submit_button" => [
                    "text_en" => "Close",
                    "text_bn" => "বন্ধ করুন",
                    "text_color" => "#fff",
                    "bg_color" => "#f16522"
                ],
            ],
            "500" => [
                "header" => [
                    "text_en" => "Sorry!",
                    "text_bn" => "দুঃখিত!",
                    "text_color" => "#f16522",
                    "bg_color" => "#f16522"
                ],
                "content" => [
                    "text_en" => "Feedback submission failed.",
                    "text_bn" => "মতামত প্রদান সফল হয়নি।",
                    "text_color" => "#000",
                ],
                "submit_button" => [
                    "text_en" => "Close",
                    "text_bn" => "বন্ধ করুন",
                    "text_color" => "#fff",
                    "bg_color" => "#f16522"
                ],
            ],
            "default" => [
                "header" => [
                    "text_en" => "Sorry!",
                    "text_bn" => "দুঃখিত!",
                    "text_color" => "#f16522",
                    "bg_color" => "#f16522"
                ],
                "content" => [
                    "text_en" => "Feedback submission failed.",
                    "text_bn" => "মতামত প্রদান সফল হয়নি।",
                    "text_color" => "#000",
                ],
                "submit_button" => [
                    "text_en" => "Close",
                    "text_bn" => "বন্ধ করুন",
                    "text_color" => "#fff",
                    "bg_color" => "#f16522"
                ],
            ],
        ],
        'form' => [
            "header" => [
                "text_en" => "Feedback",
                "text_bn" => "ফিডব্যাক",
                "text_color" => "#fff",
                "bg_color" => "#f16522"
            ],
            "submit_button" => [
                "text_en" => "Submit",
                "text_bn" => "জমা দিন",
                "text_color" => "#fff",
                "bg_color" => "#f16522"
            ],
        ],
        'end' => [
            "header" => [
                "text_en" => "Thank You!",
                "text_bn" => "ধন্যবাদ!",
                "text_color" => "#1ec993",
                "bg_color" => "#f16522"
            ],
            "content" => [
                "text_en" => "We have received your feedback.",
                "text_bn" => "আপনার মতামতটি সফলভাবে সংগৃহীত হয়েছে।",
                "text_color" => "#000",
            ],
            "submit_button" => [
                "text_en" => "Close",
                "text_bn" => "বন্ধ করুন",
                "text_color" => "#fff",
                "bg_color" => "#f16522"
            ],
        ],
    ],
    "session_time_modulus_of" => 5,
];
