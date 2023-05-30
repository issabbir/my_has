<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LBranch extends Model
{
    protected $table = "pmis.L_BRANCH";
    protected $primaryKey = "branch_id";

    protected $appends = ['text', 'value'];


    protected function getTextAttribute() {
        return $this->branch_name;
    }

    protected function getValueAttribute() {
        return $this->branch_id;
    }
}
