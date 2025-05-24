<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hit extends Model
{
    use HasFactory;

    protected $fillable = [
        'msisdn', 'trigger_id', 'channel_id', 'attempt_id', 'attempt_date'
    ];
}
