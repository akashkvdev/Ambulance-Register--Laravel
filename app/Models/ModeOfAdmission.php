<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeOfAdmission extends Model
{
    use HasFactory;

    protected $primaryKey = 'mode_of_admission_id';

    protected $fillable = [
        'mode_of_admission',
        'entry_by',
        'updated_by',
        'status'
    ];

    public function hospitalities()
    {
        return $this->hasMany(Hospitality::class, 'mode_of_admission_id', 'mode_of_admission_id');
    }
}
