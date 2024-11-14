<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations'; // Specify the table name if different from pluralized model name
    protected $primaryKey = 'location_id'; // Specify the primary key

    protected $fillable = [
        'location', // Location Name
        'dist_id', // District ID
        'entry_by', // Created By
        'updated_by', // Updated By
    ];



public function district()
{
    return $this->belongsTo(District::class, 'dist_id');
}

}
