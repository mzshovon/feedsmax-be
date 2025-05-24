<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionnaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "question_en",
        "question_bn",
        "selection_type",
        "options",
        "range",
        "parent_id",
        "order",
        "status",
        "nps_rating_mapping",
        "is_required"
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_questionnaires');
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
