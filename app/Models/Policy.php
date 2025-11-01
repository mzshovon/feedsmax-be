<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quarantine_policies';
    protected $fillable = [
        'name',
        'call_object_notation',
        'update_params',
        'args',
        'definition',
        'status'
    ];

    public function getArgsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getUpdateParamsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setArgsAttribute($value)
    {
        $this->attributes['args'] = json_encode($value);
    }
    public function setUpdateParamsAttribute($value)
    {
        $this->attributes['update_params'] = json_encode($value);
    }

}
