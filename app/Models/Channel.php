<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "tag",
        "name",
        "app_key",
        "app_secret",
        "jwks",
        "status",
        "number_of_questions",
        "redirection_link",
        "theme"
    ];

    public function triggers()
    {
        return $this->hasMany(Trigger::class);
    }

    public function themes()
    {
        return $this->hasOne(Theme::class, "id", "theme");
    }
}
