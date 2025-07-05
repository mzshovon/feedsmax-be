<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageTracking extends Model
{
    use HasFactory;

    protected $table = 'usage_tracking';
    
    protected $fillable = [
        'client_id',
        'subscription_id',
        'feature_name',
        'usage_date',
        'usage_count'
    ];

    protected $casts = [
        'usage_date' => 'date',
        'created_at' => 'datetime',
    ];

    /**
     * Get the client that owns this usage record.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    /**
     * Get the subscription for this usage record.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'subscription_id');
    }

    /**
     * Scope to get usage for today.
     */
    public function scopeToday($query)
    {
        return $query->where('usage_date', Carbon::today());
    }

    /**
     * Scope to get usage for current month.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('usage_date', Carbon::now()->month)
                    ->whereYear('usage_date', Carbon::now()->year);
    }

    /**
     * Scope to get usage for a specific feature.
     */
    public function scopeForFeature($query, string $featureName)
    {
        return $query->where('feature_name', $featureName);
    }

    /**
     * Scope to get usage for a specific client.
     */
    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope to get usage between dates.
     */
    public function scopeBetweenDates($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('usage_date', [$startDate, $endDate]);
    }

}
