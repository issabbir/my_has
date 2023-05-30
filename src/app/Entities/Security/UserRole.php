<?php

namespace App\Entities\Security;
use Illuminate\Database\Eloquent\Model;


class UserRole extends Model
{
    protected  $primaryKey='user_id';
    protected $table = "cpa_security.sec_user_roles";

}
