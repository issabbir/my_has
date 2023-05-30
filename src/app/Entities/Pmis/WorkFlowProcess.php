<?php

namespace App\Entities\Pmis;

use Illuminate\Database\Eloquent\Model;


class WorkFlowProcess extends Model
{
    protected $table = 'pmis.workflow_process';
    protected $primaryKey = 'workflow_process_id';

    protected $fillable = ['workflow_object_id','workflow_step_id','note','price_bdt','price_usd'];
    protected $with = ['workflowStep'];

    public function workflowStep(){
        return $this->belongsTo(WorkFlowStep::class, 'workflow_step_id');
    }
}
