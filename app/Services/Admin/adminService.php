<?php

namespace App\Services\Admin;

use App\Filter\Employees\EmployeeFilter;
use App\Interfaces\Admin\AdminServiceInterface;
use App\Models\Attendance;
use App\Query\Admin\AdminDashboardQuery;
use App\Repository\Admin\AdminRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminService implements AdminServiceInterface
{


    public function __construct(private AdminRepository $adminRepository, private AdminDashboardQuery $adminDashboardQuery)
    {
    }

    public function create_employee($data)
    {
        return $this->adminRepository->create_employee($data);
    }


    public function getDashboardData()
    {

        return $this->adminDashboardQuery->getDashboardData();
    }

    public function getEmployees(EmployeeFilter $employeeFilter = null)
    {
        if ($employeeFilter != null)
            return $this->adminRepository->getFilterItems($employeeFilter);
        else
            return $this->adminRepository->paginate();
    }

    public function showEmployee(int $id)
    {

        return $this->adminRepository->getById($id);
    }
    public static function careteAttendance()
    {

        $today = Carbon::today()->format('Y-m-d');
        $userId = Auth::id();

        $existingAttendance = Attendance::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->exists();

        // If an attendance record does not exist, create a new one
        if (!$existingAttendance) {
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'status' => 1
            ]);
        }
    }

    public static function AttendancePercentage($id)
    {

        $startDate = date('Y-m-01');

        $endDate = date('Y-m-d');

        $totalDays = date_diff(date_create($startDate), date_create($endDate))->format('%a');

        $attendanceDays = DB::table('attendances')
            ->where('user_id', $id)
            ->where('status', 1)
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        $percentage = ($attendanceDays / $totalDays) * 100;
        return number_format($percentage);
    }


    public function profile()
    {
        return $this->adminRepository->profile();
    }
}
