<?php

namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LInstitute extends Model
{
    protected $table = "pmis.l_institute";
    protected $primaryKey = "instutute_id";
    protected $fillable = ['instutute_id','institute_name'];

    protected $appends = ['text', 'value'];

    protected function getTextAttribute() {
        return $this->institute_name;
    }

    protected function getValueAttribute() {
        return $this->instutute_id;
    }
}
