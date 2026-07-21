<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['department_name'];

    public function guestbooks()
    {
        return $this->hasMany(Guestbook::class, 'department_id');
    }
}