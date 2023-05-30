<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LAuthDocType extends Model
{
    protected $table = "pmis.l_auth_doc_type";
    protected $primaryKey = "auth_doc_type_id";

    protected $appends = ['text', 'value'];


    protected function getTextAttribute() {
        return $this->auth_doc_type_name;
    }

    protected function getValueAttribute() {
        return $this->auth_doc_type_id;
    }
}