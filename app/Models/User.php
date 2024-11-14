<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id'; // Specify primary key if different from default 'id'

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',        // Name of the user
        'gender',           // Gender of the user
        'user_type',        // Type of user (e.g., Temporary, Permanent)
        'email',            // Email of the user
        'entry_by',         // ID of the user who created the record
        'updated_by',       // ID of the user who last updated the record
        'organization_id',  // Organization ID
        'role_id',          // Role ID (nullable)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',    // Keep remember_token hidden
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // Cast for email verification timestamp
    ];

    /**
     * The attributes that are nullable.
     *
     * @var array<string>
     */
    protected $nullable = [
        'gender',            // Gender is nullable
        'role_id',           // Role ID is nullable
        'email_verified_at'  // Email verification timestamp is nullable
    ];
    
    // Define relationships if necessary
    // For example:
    public function organization() {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }
    

    public function role() {
        return $this->belongsTo(UserRole::class, 'role_id', 'role_id');
    }
    

    public function userDetails()
    {
        return $this->hasOne(UserDetail::class, 'user_id', 'user_id');
    }
    
    public function ambulanceEntries()
    {
        return $this->hasMany(AmbulanceManage::class, 'entry_by', 'user_id');
    }
}
