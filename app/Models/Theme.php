<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Theme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "name",
        "value"
    ];

    public function channels()
    {
        return $this->hasMany(Channel::class, "theme", "id");
    }
}
