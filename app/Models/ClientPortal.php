<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPortal extends Model
{
    use HasFactory;

    protected $fillable = [
        'subdomain',
        'is_active',
        'client_id',
    ];
}
