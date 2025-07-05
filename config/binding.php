<?php
return [
    'interface-to-service' => [
        App\Services\Contracts\TriggerServiceInterface::class => App\Services\TriggerService::class,
        App\Services\Contracts\QuestionServiceInterface::class => App\Services\QuestionService::class,
        App\Services\Contracts\CMS\EventServiceInterface::class => App\Services\CMS\EventService::class,
        App\Services\Contracts\CMS\ChannelServiceInterface::class => App\Services\CMS\ChannelService::class,
        App\Services\Contracts\CMS\QuestionServiceInterface::class => App\Services\CMS\QuestionService::class,
        App\Services\Contracts\CMS\RuleServiceInterface::class => App\Services\CMS\RuleService::class,
        App\Services\Contracts\CMS\CategorySubCategoryServiceInterface::class => App\Services\CMS\CategorySubCategoryService::class,
        App\Services\Contracts\CMS\SentimentMapperServiceInterface::class => App\Services\CMS\SentimentMapperService::class,
        App\Services\Contracts\CMS\StatusServiceInterface::class => App\Services\CMS\StatusService::class,
        App\Services\Contracts\CMS\SectionServiceInterface::class => App\Services\CMS\SectionService::class,
        App\Services\Contracts\CMS\FeedbackServiceInterface::class => App\Services\CMS\FeedbackService::class,
        App\Services\Contracts\CMS\BucketServiceInterface::class => App\Services\CMS\BucketService::class,
        App\Services\Contracts\CMS\ThemeServiceInterface::class => App\Services\CMS\ThemeService::class,
        App\Services\Contracts\CMS\ClientServiceInterface::class => App\Services\CMS\ClientService::class,
    ],
];