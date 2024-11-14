<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'payment_amount',
        'ambulance_id',
        'entry_by',
        'updated_by',
        'ambulace_manage_id'
    ];

    public function ambulanceManage()
    {
        return $this->belongsTo(AmbulanceManage::class, 'ambulace_manage_id', 'ambulace_manage_id');
    }
}
