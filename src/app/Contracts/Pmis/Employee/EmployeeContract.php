<?php

namespace App\Contracts\Pmis\Employee;


interface EmployeeContract
{
    public function findEmployeeInformation($employeeCode);
    public function findEmployeeBasicInformationWithAllocatedHouse($employeeCode);
    public function employeeInfoWithAllottedHouse($employeeCode);
    public function employeesWithAllottedHouses($employeeCode, $excludeEmpCodes);
    public function findEmployeeCodesByRepApproved();
    //public function employeesWithAllottedHousesExcept($employeeCode, $exceptEmployeeCode);
}
