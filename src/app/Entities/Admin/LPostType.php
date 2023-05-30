<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LPostType extends Model
{
    protected $table = "pmis.l_post_type";
    protected $primaryKey = "post_type_id";
}