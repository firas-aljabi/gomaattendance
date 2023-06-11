<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employees\CreateEmployeeRequest;
use App\Http\Requests\Employees\GetEmployeesList;
use App\Http\Requests\Employees\GetEmployeesListRequest;
use App\Http\Resources\Admin\DashboardDataResource;
use App\Http\Resources\Admin\EmployeeResource;
use App\Http\Resources\PaginationResource;
use App\Services\Admin\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function __construct(private AdminService $adminService)
    {
    }

    public function store(CreateEmployeeRequest $request)
    {
        $createdData =  $this->adminService->create_employee($request->validated());

        $returnData = EmployeeResource::make($createdData);

        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }


    public function getDashboardData()
    {
        $data = $this->adminService->getDashboardData();
        $returnData = DashboardDataResource::make($data);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "DONE")
        );
    }

    public function getEmployeesList(GetEmployeesListRequest $request)
    {
        $data = $this->adminService->getEmployees($request->generateFilter());

        $returnData = EmployeeResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }
    public function getEmployee($id)
    {
        $employeeData = $this->adminService->showEmployee($id);
        $returnData = EmployeeResource::make($employeeData);
        return ApiResponseHelper::sendResponse(
            new Result($returnData,  "DONE")
        );
    }
    public function profile()
    {
        $employeeData = $this->adminService->profile();
        $returnData = EmployeeResource::make($employeeData);
        return ApiResponseHelper::sendResponse(
            new Result($returnData,  "DONE")
        );
    }
}
