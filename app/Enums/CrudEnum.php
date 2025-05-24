<?php

declare(strict_types=1);

namespace App\Enums;

enum CrudEnum: string {
    case Create = 'c';
    case Read = 'r';
    case Update = 'u';
    case Delete = 'd';
}
