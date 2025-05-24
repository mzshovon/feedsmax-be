<?php

declare(strict_types=1);

namespace App\Enums;

enum SentimentCategory: string {
    case Good = 'good';
    case Bad = 'bad';
    case Neutral = 'neutral';
}
