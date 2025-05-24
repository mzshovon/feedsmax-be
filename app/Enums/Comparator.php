<?php

declare(strict_types=1);

namespace App\Enums;

enum Comparator: int {
    case Equal = 1;
    case GreaterThan = 2;
    case LessThan = 3;
    case GreaterThanEqual = 4;
    case LessThanEqual = 5;
}
