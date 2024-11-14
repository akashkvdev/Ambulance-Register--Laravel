<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients'; // Specify the table name if different from pluralized model name
    protected $primaryKey = 'patient_id'; // Specify the primary key

    protected $fillable = [
        'patient_name', // Patient Name
        'adhar_no', // Aadhar Number
        'other_id_type', // Other ID Type
        'other_id_no', // Other ID Number
        'mobile_number', // Mobile Number
        'entry_by', // Created By
        'updated_by', // Updated By
    ];
}
