<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'date_consultation',
        'motif',
        'diagnostic',
        'prescription',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}