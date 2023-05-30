<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LBillHead extends Model
{
    protected $table = "pmis.l_bill_heads";
    protected $primaryKey = "bill_head_id";
}