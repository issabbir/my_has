<?php


namespace App\Entities\Admin;


use Illuminate\Database\Eloquent\Model;

class PrBillCodeMaster extends Model
{
    protected $table = "pmis.pr_bill_code_master";
    protected $primaryKey = "";

    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->bill_code;
    }

    public function getValueAttribute() {
        return $this->bill_code;
    }
}
