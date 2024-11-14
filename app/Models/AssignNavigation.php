<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignNavigation extends Model
{
    use HasFactory;

    protected $primaryKey = 'assign_navigations_id'; // Custom primary key

    // Specify the fields that can be mass assigned
    protected $fillable = [
        'nav_id',
        'role_id',
        'entry_by',
        'updated_by',
    ];


    public function navigation()
    {
        return $this->belongsTo(Navigation::class, 'nav_id', 'nav_id');
    }

    // Define the relationship with the UserRole model
    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'role_id', 'role_id');
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'entry_by', 'user_id');
    }
}
