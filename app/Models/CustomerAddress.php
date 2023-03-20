<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'customer_address';
    protected $fillable = ['name', 'address', 'longitude', 'latitude', 'is_primary', 'status' , 'customer_id'];
}
