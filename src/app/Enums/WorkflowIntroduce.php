<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/4/20
 * Time: 11:07 AM
 */

namespace App\Enums;


class WorkflowIntroduce
{
    // Please mention here all work flow of this individual menu
    // work flow name should be for develoer development purpose identification, to differentiate individually

    /**** Application workflow *****/
    public const appWorkflow = 1;
    public const appWorkflowObjectKey = 'APPLICATION_ID'; // table column id
    public const appWorkflowObjectTable = 'HA_APPLICATION'; // table Name : Its a table where workflow will be initiated

    /**** Point Approval workflow *****/
   // public const advertisementWorkflow = 2;
   // public const advertisementWorkflowObjectKey = 'ADV_ID'; // table column id
   // public const advertisementWorkflowObjectTable = 'HA_ADV_MST'; // table Name : Its a table where workflow will be initiated

    /**** Depart Acknowledge workflow *****/
    public const deptAckWorkflow = 3;
    public const deptAckWorkflowObjectKey = 'DEPT_ACK_ID'; // table column id
    public const deptAckWorkflowObjectTable = 'DEPT_ACKNOWLEDGEMENT'; // table Name : Its a table where workflow will be initiated

    /**** Advertisement workflow *****/
    public const advertisementWorkflow = 4;
    public const advertisementWorkflowObjectKey = 'ADV_ID'; // table column id
    public const advertisementWorkflowObjectTable = 'HA_ADV_MST'; // table Name : Its a table where workflow will be initiated


}
