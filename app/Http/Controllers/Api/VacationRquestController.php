<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employees\GetMonthlyShiftListRequest;
use App\Http\Requests\Requests\CreateVacationRequest;
use App\Http\Requests\Requests\GetVacationRequestListRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\Requests\MonthlyShiftResource;
use App\Http\Resources\Requests\VacationResource;
use App\Services\Requests\VacationRequestService;
use Illuminate\Http\Request;

class VacationRquestController extends Controller
{

    public function __construct(private VacationRequestService $vacationRequestService)
    {
    }


    public function store(CreateVacationRequest $request)
    {
        $createdData =  $this->vacationRequestService->add_vacation_request($request->validated());

        $returnData = VacationResource::make($createdData);

        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }

    public function approve_vacation_request($id)
    {
        $vacationRequest = $this->vacationRequestService->approve_vacation_request($id);
        $returnData = VacationResource::make($vacationRequest);
        return ApiResponseHelper::sendResponse(
            new Result($returnData,  "DONE")
        );
    }

    public function reject_vacation_request($id)
    {
        $vacationRequest = $this->vacationRequestService->reject_vacation_request($id);
        $returnData = VacationResource::make($vacationRequest);
        return ApiResponseHelper::sendResponse(
            new Result($returnData,  "DONE")
        );
    }

    public function show($id)
    {
        $vacationRequest = $this->vacationRequestService->show($id);
        $returnData = VacationResource::make($vacationRequest);
        return ApiResponseHelper::sendResponse(
            new Result($returnData,  "DONE")
        );
    }


    public function getMyVacationRequests(GetVacationRequestListRequest $request)
    {
        $data = $this->vacationRequestService->getMyVacationRequests($request->generateFilter());
        $returnData = VacationResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }

    public function getVacationRequests(GetVacationRequestListRequest $request)
    {
        $data = $this->vacationRequestService->getVacationRequests($request->generateFilter());
        $returnData = VacationResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }


    public function getMyMonthlyShift(GetMonthlyShiftListRequest $request)
    {
        $data = $this->vacationRequestService->getMonthlyShiftListRequest($request->generateFilter());
        $returnData = MonthlyShiftResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }
}
