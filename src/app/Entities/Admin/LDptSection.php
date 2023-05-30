<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LDptSection extends Model
{
    protected $table = "pmis.l_dpt_section";
    protected $primaryKey = "dpt_section_id";


    public function sections() {
        return $this->belongsTo(LDptSection::class, 'section_id');
    }
    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->dpt_section;
    }

    public function getValueAttribute() {
        return $this->dpt_section_id;
    }
}
