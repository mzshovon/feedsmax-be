<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        "tag",
        "name",
        "retry",
        "status",
        "pagination",
        "client_id",
        "theme_id"
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function themes()
    {
        return $this->hasOne(Theme::class, "id", "theme_id");
    }
}
