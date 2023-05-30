<?php

namespace App\Entities\Pmis;

use Illuminate\Database\Eloquent\Model;


class WorkFlowStep extends Model
{
    protected $table = 'pmis.workflow_steps';
    protected $primaryKey = 'workflow_step_id';
}
