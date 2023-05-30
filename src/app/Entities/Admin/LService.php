<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LService extends Model
{
    protected $table = "pmis.l_service";
    protected $primaryKey = "service_id";
}
