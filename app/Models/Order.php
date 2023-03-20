<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $table = 'order';
    public $timestamps = false;
    protected $fillable = ['invoices_number','amount','order_type','discount','final_amount','created_date','payment_date','status' ,'customer_note' ,'customer_id','fullname','address','phone' ,'longitude','latitude','address_name','expired_date', 'date_info','order_name'];

}
