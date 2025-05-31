<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "question_en",
        "question_bn",
        "field_type",
        "options",
        "score_range",
        "ref_id",
        "ref_val",
        "parent_id",
        "order",
        "status",
        "is_required"
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function buckets()
    {
        return $this->belongsToMany(Bucket::class, 'bucket_questions');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->where('status', 1)->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
