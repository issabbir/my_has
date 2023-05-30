<?php

namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class SecIpAllowed extends Model
{
    protected $table = 'cpa_security.sec_ip_allowed';
    protected $primaryKey = null;
    protected $fillable = ['update_at','user_id', 'allowed_ip'];
    public $incrementing = false;
}
