<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambulance extends Model
{
    use HasFactory;

    protected $table = 'ambulances'; // Specify the table name if different from pluralized model name
    protected $primaryKey = 'ambulance_id'; // Specify the primary key

    protected $fillable = [
        'ambulance_number', // Unique Ambulance Number
        'entry_by', // Created By
        'updated_by', // Updated By
    ];


    public function payments()
    {
        return $this->hasMany(Payment::class, 'ambulance_id', 'ambulance_id'); // Corrected foreign key
    }
}
