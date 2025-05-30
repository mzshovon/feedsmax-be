<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuota extends Model
{
    use HasFactory;

    protected $fillable = [
        "type",
        "param",
        "quota",
        "count"
    ];
}
