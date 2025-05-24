<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SentimentMapper extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "keywords",
        "sentiment_category",
    ];
}
