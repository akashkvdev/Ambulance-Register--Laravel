<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    use HasFactory;

    protected $primaryKey = 'nav_id';
    protected $fillable = [
        'nav_name',
        'nav_url',
        'nav_icon',
        'entry_by',
        'updated_by',
        'status',
    ];


    public function assignedNavigations()
    {
        return $this->hasMany(AssignNavigation::class, 'nav_id', 'nav_id');
    }
}
