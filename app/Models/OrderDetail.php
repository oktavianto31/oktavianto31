<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'order_detail';
    public $timestamps = false;
    protected $fillable = ['order_id','product_name','product_description','product_amount','product_discount','product_icon','product_image','product_service_type','product_quantity','product_service_id' , 'type_quantity'];
}