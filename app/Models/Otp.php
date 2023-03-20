<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;
    protected $table = 'otp_key';
    public $timestamps = false;
    protected $fillable = ['otp','status','created_date','username'];
}
