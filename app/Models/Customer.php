<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'customer';
    protected $fillable = ['fullname','phone','email','username','user_token','status','photo','dob','gender','device_token'];

}
