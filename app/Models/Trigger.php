<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trigger extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'event',
        'context',
        'description',
        'channel_id',
        'group_id',
        'lang',
        'status'
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
