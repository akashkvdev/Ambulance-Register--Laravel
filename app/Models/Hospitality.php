<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospitality extends Model
{
    use HasFactory;

    protected $table = 'hospitalities'; // Specify the table name if different from pluralized model name
    protected $primaryKey = 'hospitality_id'; // Specify the primary key

    protected $fillable = [
        'mode_of_admission_id', // Mode of Admission ID
        'admitted_area', // Admitted Area
        'referred_by', // Referred By
        'remarks', // Remarks
        'ip', // Inpatient Status
        'op', // Outpatient Status
        'patient_id', // Patient ID
        'entry_by', // Created By
        'updated_by', // Updated By
        'department_id', // Department ID
    ];


    public function modeOfAdmission()
    {
        return $this->belongsTo(ModeOfAdmission::class, 'mode_of_admission_id', 'mode_of_admission_id');
    }
    public function departments()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
}
