<?php

namespace App\Repository\Requests;

use App\ApiHelper\SortParamsHelper;
use App\Filter\VacationRequests\MonthlyShiftFilter;
use App\Filter\VacationRequests\VacationRequestFilter;
use App\Models\Attendance;
use App\Models\VacationRequest;
use App\Repository\BaseRepositoryImplementation;
use App\Statuses\UserTypes;
use App\Statuses\VacationRequestStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VacationRequestRepository extends BaseRepositoryImplementation
{

    public function add_vacation_request($data)
    {
        DB::beginTransaction();

        try {



            $user_id = Auth::id();
            $current_date = date('Y-m-d');

            // Check if the user has already created a Vacation request on the same day
            $existing_request = VacationRequest::where('user_id', $user_id)
                ->whereDate('created_at', $current_date)
                ->first();

            if ($existing_request) {
                throw new \Exception('A Vacation request has already been created for this user today.');
            }

            $vacationRequest = new VacationRequest();
            $vacationRequest->user_id = $user_id;
            $vacationRequest->reason = $data['reason'];
            $vacationRequest->status = $data['status'];
            $vacationRequest->start_date = $data['start_date'];
            $vacationRequest->end_date = $data['end_date'];
            $vacationRequest->save();

            DB::commit();
            if ($vacationRequest === null) {
                throw new \Exception('Vacation Request was not created');
            }

            return $vacationRequest->load(['user']);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error($e->getMessage());

            throw $e;
        }
    }

    public function getFilterItems($filter)
    {
        $records = VacationRequest::query()->where('user_id', Auth::id());
        if ($filter instanceof VacationRequestFilter) {

            $records->when(isset($filter->duration), function ($records) use ($filter) {
                $duration_filter = $filter->duration;

                if ($duration_filter == 1) {
                    $records->where(function ($query) {
                        $query->where('status', VacationRequestStatus::HOURLY);
                    });
                } else if ($duration_filter == 2) {
                    $records->where(function ($query) {
                        $query->where('status', VacationRequestStatus::DAILY)
                            ->orWhere('status', VacationRequestStatus::ANNUL);
                    });
                }
            });

            $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                $sortParams = SortParamsHelper::getSortParams($filter->getOrderBy());
                if ($sortParams->getIsRelated()) {
                    $records
                        ->join(
                            $sortParams->getTable(),
                            'vacation_requests.' . $sortParams->getJoinColumn(),
                            '=',
                            $sortParams->getRelation()
                        )
                        ->orderBy($sortParams->getOrderColumn(), $filter->getOrder());
                } else {
                    $records->orderBy($filter->getOrderBy(), $filter->getOrder());
                }
            });
            return $records->with('user')->paginate($filter->per_page);
        }
        return $records->with('user')->paginate($filter->per_page);
    }


    public function getFilterItemsForAdmin($filter)
    {
        if (auth()->user()->type == UserTypes::ADMIN) {
            $records = VacationRequest::query();
            if ($filter instanceof VacationRequestFilter) {

                $records->when(isset($filter->duration), function ($records) use ($filter) {
                    $duration_filter = $filter->duration;

                    if ($duration_filter == 1) {
                        $records->where(function ($query) {
                            $query->where('status', VacationRequestStatus::HOURLY);
                        });
                    } else if ($duration_filter == 2) {
                        $records->where(function ($query) {
                            $query->where('status', VacationRequestStatus::DAILY)
                                ->orWhere('status', VacationRequestStatus::ANNUL);
                        });
                    }
                });

                $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                    $sortParams = SortParamsHelper::getSortParams($filter->getOrderBy());
                    if ($sortParams->getIsRelated()) {
                        $records
                            ->join(
                                $sortParams->getTable(),
                                'vacation_requests.' . $sortParams->getJoinColumn(),
                                '=',
                                $sortParams->getRelation()
                            )
                            ->orderBy($sortParams->getOrderColumn(), $filter->getOrder());
                    } else {
                        $records->orderBy($filter->getOrderBy(), $filter->getOrder());
                    }
                });
                return $records->with('user')->paginate($filter->per_page);
            }
            return $records->with('user')->paginate($filter->per_page);
        } else {

            throw new \Exception('Unauthorized');
        }
    }


    public function getMonthlyShiftList($filter)
    {
        if (Attendance::where('user_id', Auth::id())) {
            $records = Attendance::query();
            if ($filter instanceof MonthlyShiftFilter) {
                $records->where('user_id', Auth::id());

                $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                    $sortParams = SortParamsHelper::getSortParams($filter->getOrderBy());
                    if ($sortParams->getIsRelated()) {
                        $records
                            ->join(
                                $sortParams->getTable(),
                                'attendances.' . $sortParams->getJoinColumn(),
                                '=',
                                $sortParams->getRelation()
                            )
                            ->orderBy($sortParams->getOrderColumn(), $filter->getOrder());
                    } else {
                        $records->orderBy($filter->getOrderBy(), $filter->getOrder());
                    }
                });
                return $records->paginate($filter->per_page);
            }
            return $records->paginate($filter->per_page);
        } else {
            throw new \Exception('Unauthorized');
        }
    }

    /**
     * Specify Model class name.
     * @return mixed
     */
    public function model()
    {
        return VacationRequest::class;
    }
}
