<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $primaryKey = 'department_id';

    protected $fillable = [
        'department_name',
        'entry_by',
        'updated_by',
        'status',
        'organization_id'
    ];


    public function organization()
{
    return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
}
}
