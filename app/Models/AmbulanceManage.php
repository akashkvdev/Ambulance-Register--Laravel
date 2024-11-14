<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmbulanceManage extends Model
{
    use HasFactory;

    protected $table = 'ambulance_manages'; // Specify the table name if different from pluralized model name
    protected $primaryKey = 'ambulace_manage_id'; // Specify the primary key

    protected $fillable = [
        'date_of_visit', // Date of Visit
        'arrival_time', // Arrival Time
        'hospitality_id', // Hospitality ID
        'patient_id', // Patient ID
        'location_id', // Location ID
        'ambulance_id', // Ambulance ID
        'entry_by', // Created By
        'updated_by', // Updated By
        'payment_id', // Payment ID
        'driver_name',
        'driver_contact_no',
        'coming_from_which_hospital',
        'organization_id'
    ];


    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class, 'ambulance_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function hospitality()
    {
        return $this->belongsTo(Hospitality::class, 'hospitality_id');
    }

    public function entryBy()
    {
        return $this->belongsTo(User::class, 'entry_by', 'user_id');
    }

    // Relationship to the User model for updated_by
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    public function payments()
    {
        return $this->hasOne(Payment::class, 'ambulace_manage_id', 'ambulace_manage_id');
    }
  
   
  

}
