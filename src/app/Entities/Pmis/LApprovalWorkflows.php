<?php

namespace App\Entities\Pmis;

use Illuminate\Database\Eloquent\Model;


class LApprovalWorkflows extends Model
{
    protected $table = 'pmis.l_approval_workflows';
    protected $primaryKey = 'approval_workflow_id';
}
