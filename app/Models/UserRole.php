<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;
    protected $primaryKey = 'role_id';
    protected $fillable = [
        'role_name',
        'entry_by',
        'updated_by',
        'status',
    ];

    public function users() {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
