<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/9/20
 * Time: 12:08 PM
 */

namespace App\Entities\HouseAllotment;

use Illuminate\Database\Eloquent\Model;

class ReplacementApplication extends Model
{
    protected $table = 'replacement_application';
    protected $primaryKey = 'replace_app_id';

    public function allotment()
    {
        return $this->belongsTo(HouseAllotment::class, 'allot_id');
    }

    public function house()
    {
        return $this->belongsTo(HouseList::class, 'replace_house_id');
    }
}