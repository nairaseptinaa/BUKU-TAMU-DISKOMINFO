<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['service_name'];

    public function guestbooks()
    {
        return $this->hasMany(Guestbook::class, 'service_type_id');
    }
}