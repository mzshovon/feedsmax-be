<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bucket extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
        "status",
        "type",
        "nps_ques_id",
        "promoter_range"
    ];

    public function questions()
    {
        return $this->belongsToMany(Questionnaire::class, 'group_questions');
    }

    public function topQuestion()
    {
        return $this->hasOne(Questionnaire::class, 'id', 'nps_ques_id');
    }
}
