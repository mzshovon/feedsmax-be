<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'client_id',
        'package_id',
        'start_date',
        'end_date',
        'status',
        'is_auto_renew'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_auto_renew' => 'boolean',
    ];

    /**
     * Get the client that owns this subscription.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the package for this subscription.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get all billing records for this subscription.
     */
    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    /**
     * Get all usage tracking records for this subscription.
     */
    public function usageTracking()
    {
        return $this->hasMany(UsageTracking::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->end_date === null || $this->end_date >= Carbon::today());
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->end_date && $this->end_date < Carbon::today());
    }

    /**
     * Check if subscription is about to expire (within 7 days).
     */
    public function isAboutToExpire(): bool
    {
        return $this->end_date && 
               $this->end_date <= Carbon::today()->addDays(7) &&
               $this->end_date >= Carbon::today();
    }

    /**
     * Get remaining days until expiration.
     */
    public function getRemainingDays(): ?int
    {
        if (!$this->end_date) {
            return null;
        }
        
        return Carbon::today()->diffInDays($this->end_date, false);
    }

    /**
     * Scope to get active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get expired subscriptions.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope to get subscriptions with auto-renewal.
     */
    public function scopeAutoRenew($query)
    {
        return $query->where('is_auto_renew', true);
    }
}
