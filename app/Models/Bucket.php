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
        "quota",
        "served"
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'bucket_questions');
    }

}
