<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "attempt_id",
        "context",
        "section",
        "category_id",
        "sub_category_id",
        "status",
        "query"
    ];
}
