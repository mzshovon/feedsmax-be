<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strive extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'device_id',
        'bucket_id',
        'event',
        'client_id',
        'channel',
        'client',
        'platform',
        'tier',
        'language',
        'quarantine_rule_set',
        'device_name',
        'device_model',
        'user_app_version',
        'user_os_version',
        'network',
        'view',
        'tried_at',
        'submitte_at',
        'score',
        'arpu',
        'customer_behavior',
        'geo_location',
        'ip',
        'feedsmax_status',
        'updated_at',
        'created_at',
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

    public function bucket()
    {
        return $this->hasOne(Bucket::class, 'id', 'bucket_id');
    }
}
