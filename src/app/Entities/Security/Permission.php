<?php

namespace App\Entities\Security;
use Illuminate\Database\Eloquent\Model;


class Permission extends Model
{
    protected $primaryKey = 'permission_id';
    protected $table = "cpa_security.sec_permissions";
}
