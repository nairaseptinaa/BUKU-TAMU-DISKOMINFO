<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected function casts(): array
    {
        return [
            'visit_date' => 'datetime',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}
