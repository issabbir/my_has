<?php


namespace App\Entities\Pf;


use Illuminate\Database\Eloquent\Model;

class GpfEmployee extends Model
{
    protected $table = 'GPF_EMPLOYEE';
    protected $primaryKey = 'GPF_ID';
    public $timestamps = false;
}
