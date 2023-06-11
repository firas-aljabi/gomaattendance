<?php

namespace App\Query\Admin;

use App\Models\User;
use App\Statuses\EmployeeStatus;
use App\Statuses\UserTypes;

class AdminDashboardQuery
{


    public function getDashboardData(): object
    {

        $employeesCount = $this->getAllEmployees();
        $onDutyEmployeesCount = $this->getOnDutyEmployees();
        $onVacationEmployeesCount = $this->getOnVacationEmployees();

        $result = [
            'all_employees_count' => $employeesCount,
            'on_duty_employees_count' => $onDutyEmployeesCount,
            'on_vacation_employees_count' => $onVacationEmployeesCount,
        ];
        return (object) $result;
    }


    private function getAllEmployees()
    {
        $allEmployeesCount = User::where('type', UserTypes::EMPLOYEE)->count();
        return $allEmployeesCount;
    }

    private function getOnDutyEmployees()
    {
        $onDutyEmployeesCount = User::where('type', UserTypes::EMPLOYEE)->where('status', EmployeeStatus::ON_DUTY)->count();
        return $onDutyEmployeesCount;
    }

    private function getOnVacationEmployees()
    {
        $onVacationEmployeesCount = User::where('type', UserTypes::EMPLOYEE)->where('status', EmployeeStatus::ON_VACATION)->count();
        return $onVacationEmployeesCount;
    }
}
