<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    protected $primaryKey = 'organization_id';
    protected $fillable = [
        'organization_name',
        'org_short_name',
        'organization_location',
        // 'organization_inclusion_date',
        'status',
        'entry_by',
    ];


    public function users()
    {
        return $this->hasMany(User::class, 'organization_id', 'organization_id');
    }
    public function departments()
    {
        return $this->belongsTo(Department::class, 'organization_id', 'organization_id');
    }
}
