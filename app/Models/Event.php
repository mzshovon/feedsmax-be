<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'context',
        'description',
        'client_id',
        'channel_id',
        'bucket_id',
        'lang',
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function bucket()
    {
        return $this->belongsTo(Bucket::class);
    }
}
