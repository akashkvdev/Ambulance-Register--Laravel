<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_details_id'; // Specify the custom primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'login_id',      // Unique 5-digit login ID
        'user_id',       // Foreign key referencing users table
        'contact_no',    // User's contact number
        'entry_by',      // User ID who created the record
        'updated_by',    // User ID who last updated the record
        'status',        // Status of the user detail
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Define relationships if necessary.
     */
    public function user()
    {
        return $this->hasMany(User::class, 'user_id',); // Define relationship to User model
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'entry_by'); // Relationship to creator user
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by'); // Relationship to updater user
    }
}
