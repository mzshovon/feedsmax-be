<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_tag',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'address',
        'client_key',
        'client_secret',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the active subscription for this client.
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latest('start_date');
    }

    /**
     * Get all billing records for this client.
     */
    public function billings()
    {
        return $this->hasMany(Billing::class, 'client_id', 'client_id');
    }

    /**
     * Get all users for this client.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'client_id', 'client_id');
    }

    /**
     * Get all usage tracking records for this client.
     */
    public function usageTracking()
    {
        return $this->hasMany(UsageTracking::class, 'client_id');
    }

    /**
     * Get admin users for this client.
     */
    public function adminUsers()
    {
        return $this->users()->where('role', 'admin');
    }

    /**
     * Get unpaid billings for this client.
     */
    public function unpaidBillings()
    {
        return $this->billings()->where('status', 'unpaid');
    }

    /**
     * Get overdue billings for this client.
     */
    public function overdueBillings()
    {
        return $this->billings()->where('status', 'overdue');
    }

    /**
     * Get current package through active subscription.
     */
    public function currentPackage()
    {
        return $this->activeSubscription?->package;
    }

    /**
     * Get portal subdomain.
     */
    public function getSubdomain(): ?string
    {
        return $this->portal?->subdomain;
    }
}
