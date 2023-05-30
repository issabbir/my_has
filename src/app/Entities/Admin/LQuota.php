<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LQuota extends Model
{
    protected $table = "pmis.l_quota";
    protected $primaryKey = "quota_id";
}
