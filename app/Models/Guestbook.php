<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guestbook extends Model
{
    protected $fillable = [
        'visit_date',
        'name',
        'position',
        'visitor_type',
        'department_id',
        'external_agency',
        'phone_number',
        'service_type_id',
        'feedback',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}