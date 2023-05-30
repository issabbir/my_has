<?php

namespace App\Entities\Security;

use Illuminate\Database\Eloquent\Model;
class IpAllowed extends Model
{

    protected $primaryKey = "user_id";
    protected $table = "cpa_security.sec_ip_allowed";

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
