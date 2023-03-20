<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;
    protected $table = 'push_notification';    
    protected $primaryKey = 'push_notification_id';
    public $timestamps = false;
    protected $fillable = ['customer_id','push_notification_text','push_notification_status','push_notification_date','push_notification_title','push_notification_url','push_notification_url_host','push_notification_type','push_notification_token','push_notification_response','push_notification_image','push_notification_url_label','push_notification_schedule_date'];
}

