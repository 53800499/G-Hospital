<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'last_name',
        'first_name',
        'birth_date',
        'gender',
        'address',
        'phone',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'created_at' => 'datetime',
    ];}