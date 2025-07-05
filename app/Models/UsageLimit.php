<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageLimit extends Model
{
    use HasFactory;

    protected $primaryKey = 'limit_id';
    
    protected $fillable = [
        'package_id',
        'limit_type',
        'limit_value',
        'feature_name'
    ];

    /**
     * Get the package that owns this usage limit.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Scope to get daily limits.
     */
    public function scopeDaily($query)
    {
        return $query->where('limit_type', 'daily');
    }

    /**
     * Scope to get monthly limits.
     */
    public function scopeMonthly($query)
    {
        return $query->where('limit_type', 'monthly');
    }

    /**
     * Scope to get yearly limits.
     */
    public function scopeYearly($query)
    {
        return $query->where('limit_type', 'yearly');
    }

    /**
     * Scope to get limits for a specific feature.
     */
    public function scopeForFeature($query, string $featureName)
    {
        return $query->where('feature_name', $featureName);
    }
}
