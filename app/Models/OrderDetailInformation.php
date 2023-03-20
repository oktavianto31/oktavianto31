<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetailInformation extends Model
{
    use HasFactory;
      protected $table = 'order_detail_information';
    public $timestamps = false;
    protected $fillable = ['order_id','information_id','status'];
}