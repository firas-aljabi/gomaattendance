<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\CreateJustifyRequest;
use App\Http\Requests\Requests\GetJustifyRequestListRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\Requests\JustifyResource;
use App\Services\Requests\JustifyRequestService;
use Illuminate\Http\Request;

class JustifyRquestController extends Controller
{
    public function __construct(private JustifyRequestService $justifyRequestService)
    {
    }


    public function store(CreateJustifyRequest $request)
    {

        $createdData =  $this->justifyRequestService->add_justify_request($request->validated());

        $returnData = JustifyResource::make($createdData);

        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }


    public function show($id)
    {
        $vacationRequest = $this->justifyRequestService->show($id);
        $returnData = JustifyResource::make($vacationRequest);
        return ApiResponseHelper::sendResponse(
            new Result($returnData,  "DONE")
        );
    }

    public function getMyJustifyRequests(GetJustifyRequestListRequest $request)
    {
        $data = $this->justifyRequestService->getMyJustifyRequests($request->generateFilter());
        $returnData = JustifyResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }


    public function getJustifyRequests(GetJustifyRequestListRequest $request)
    {
        $data = $this->justifyRequestService->getJustifyRequests($request->generateFilter());
        $returnData = JustifyResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }
}
