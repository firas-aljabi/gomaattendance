<?php

namespace App\Repository\Requests;

use App\ApiHelper\SortParamsHelper;
use App\Filter\JustifyRequests\JustifyRequestsFilter;
use App\Models\JustifyRequest;
use App\Repository\BaseRepositoryImplementation;
use App\Statuses\UserTypes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class JustifyRequestRepository extends BaseRepositoryImplementation
{
    public function getFilterItems($filter)
    {
        $records = JustifyRequest::query()->where('user_id', Auth::id());
        if ($filter instanceof JustifyRequestsFilter) {
            $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                $sortParams = SortParamsHelper::getSortParams($filter->getOrderBy());
                if ($sortParams->getIsRelated()) {
                    $records
                        ->join(
                            $sortParams->getTable(),
                            'justify_requests.' . $sortParams->getJoinColumn(),
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
            $records = JustifyRequest::query();
            if ($filter instanceof JustifyRequestsFilter) {


                $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                    $sortParams = SortParamsHelper::getSortParams($filter->getOrderBy());
                    if ($sortParams->getIsRelated()) {
                        $records
                            ->join(
                                $sortParams->getTable(),
                                'justify_requests.' . $sortParams->getJoinColumn(),
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

    public function add_justify_request($data)
    {

        DB::beginTransaction();

        try {
            $user_id = Auth::id();
            $current_date = date('Y-m-d');

            // Check if the user has already created a justify request on the same day
            $existing_request = JustifyRequest::where('user_id', $user_id)
                ->whereDate('created_at', $current_date)
                ->first();

            if ($existing_request) {
                throw new \Exception('A justify request has already been created for this user today.');
            }

            $justifyRequest = new JustifyRequest();
            $justifyRequest->user_id =  $user_id;
            $justifyRequest->reason = $data['reason'];
            $justifyRequest->type = $data['type'];

            if (Arr::has($data, 'medical_report_file')) {
                $file = Arr::get($data, 'medical_report_file');
                $extention = $file->getClientOriginalExtension();
                $file_name = Str::uuid() . date('Y-m-d') . '.' . $extention;
                $file->move(public_path('uploaded'), $file_name);

                $image_file_path = public_path('uploaded/' . $file_name);
                $image_data = file_get_contents($image_file_path);
                $base64_image = base64_encode($image_data);
                $justifyRequest->medical_report_file = $base64_image;
            }


            $justifyRequest->save();

            DB::commit();

            if ($justifyRequest === null) {
                throw new \Exception('Justify Request was not created');
            }

            return $justifyRequest->load(['user']);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error($e->getMessage());

            throw $e;
        }
    }
    /**
     * Specify Model class name.
     * @return mixed
     */
    public function model()
    {
        return JustifyRequest::class;
    }
}
