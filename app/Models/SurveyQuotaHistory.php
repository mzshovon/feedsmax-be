<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuotaHistory extends Model
{
    use HasFactory;

    protected $table = "survey_quota_history";

    protected $fillable = [
        "quota_history",
        "quota_from"
    ];
}
