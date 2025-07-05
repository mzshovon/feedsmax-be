<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'client_id',
        'subscription_id',
        'amount',
        'billing_date',
        'due_date',
        'status',
        'payment_method',
        'payment_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    /**
     * Get the client that owns this billing record.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    /**
     * Get the subscription for this billing record.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'subscription_id');
    }

    /**
     * Check if billing is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if billing is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'unpaid' && $this->due_date < Carbon::today();
    }

    /**
     * Get days until due date.
     */
    public function getDaysUntilDue(): int
    {
        return Carbon::today()->diffInDays($this->due_date, false);
    }

    /**
     * Get days overdue.
     */
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return $this->due_date->diffInDays(Carbon::today());
    }

    /**
     * Mark as paid.
     */
    public function markAsPaid(?string $paymentMethod): void
    {
        $this->update([
            'status' => 'paid',
            'payment_date' => Carbon::today(),
            'payment_method' => $paymentMethod
        ]);
    }

    /**
     * Scope to get paid bills.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to get unpaid bills.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * Scope to get overdue bills.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'unpaid')
                    ->where('due_date', '<', Carbon::today());
    }
}
