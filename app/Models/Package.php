<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_name',
        'description',
        'price',
        'duration',
        'is_active',
    ];

    public function usageLimits()
    {
        return $this->hasMany(UsageLimit::class, 'package_id', 'id')->select('limit_type', 'limit_value', 'feature_name');
    }
}
