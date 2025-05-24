<?php

declare(strict_types=1);

namespace App\Enums;

enum QuotaType: string {
    case Session = "session";
    case Location = "location";
}
