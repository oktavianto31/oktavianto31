<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logger extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'logger';
    protected $fillable = ['page','value','ts','customer_id', 'device_type' ,'source' ];

}