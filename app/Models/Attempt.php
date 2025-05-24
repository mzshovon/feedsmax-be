<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'msisdn',
        'device_id',
        'group_id',
        'event',
        'event_id',
        'channel',
        'channel_id',
        'platform',
        'loyalty',
        'lang',
        'trigger_matches',
        'device_name',
        'model',
        'app_version',
        'os_version',
        'user_network',
        'view',
        'tried_at',
        'nps_score',
        'arpu',
        'customer_behavior',
        'district',
        'city',
        'thana',
        'feedsmax_status',
        'pick_agent_id',
        'feedsmax_status_updated_at',
        'remarks',
        'extra',
    ];

    protected $attributes = [
        'tried_at' => null,
    ];

    public static function booted()
    {
        parent::booted();
        static::creating(function ($model) {
            $model->tried_at = now();
        });
    }

    public function groups()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }
}
