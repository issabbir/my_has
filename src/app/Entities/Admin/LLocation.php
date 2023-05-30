<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LLocation extends Model
{
    protected $table = "pmis.l_location";
    protected $primaryKey = "location_id";
}
