<?php

namespace App\Services\Requests;

use App\Filter\VacationRequests\MonthlyShiftFilter;
use App\Filter\VacationRequests\VacationRequestFilter;
use App\Interfaces\Requests\VacationServiceInterface;
use App\Repository\Requests\VacationRequestRepository;
use App\Statuses\UserTypes;
use App\Statuses\VacationRequestTypes;

class VacationRequestService implements VacationServiceInterface
{
    public function __construct(private VacationRequestRepository $vacationRequestRepository)
    {
    }

    public function add_vacation_request($data)
    {
        return $this->vacationRequestRepository->add_vacation_request($data);
    }

    public function approve_vacation_request($id)
    {
        if (auth()->user()->type == UserTypes::ADMIN) {
            $vacationAfterAccept = $this->vacationRequestRepository->updateById($id, ['type' => VacationRequestTypes::APPROVED]);
            return $vacationAfterAccept->load('user');
        } else {
            throw new \Exception('You Dont Have Permission To Approve Vacation Requests..!!');
        }
    }
    public function reject_vacation_request($id)
    {
        if (auth()->user()->type == UserTypes::ADMIN) {
            $vacationAfterReject = $this->vacationRequestRepository->updateById($id, ['type' => VacationRequestTypes::REJECTED]);
            return $vacationAfterReject->load('user');
        } else {
            throw new \Exception('You Dont Have Permission To Reject Vacation Requests..!!');
        }
    }

    public function show($id)
    {
        if (auth()->user()->type == UserTypes::ADMIN) {
            return $this->vacationRequestRepository->with('user')->getById($id);
        } else {

            throw new \Exception('Unauthorized');
        }
    }

    public function getMyVacationRequests(VacationRequestFilter $vacationRequestFilter = null)
    {
        if ($vacationRequestFilter != null) {
            return $this->vacationRequestRepository->getFilterItems($vacationRequestFilter);
        } else {
            return $this->vacationRequestRepository->paginate();
        }
    }


    public function getVacationRequests(VacationRequestFilter $vacationRequestFilter = null)
    {
        if ($vacationRequestFilter != null) {
            return $this->vacationRequestRepository->getFilterItemsForAdmin($vacationRequestFilter);
        } else {
            return $this->vacationRequestRepository->paginate();
        }
    }

    public function getMonthlyShiftListRequest(MonthlyShiftFilter $monthlyShiftFilter = null)
    {
        if ($monthlyShiftFilter != null) {
            return $this->vacationRequestRepository->getMonthlyShiftList($monthlyShiftFilter);
        } else {
            return $this->vacationRequestRepository->paginate();
        }
    }
}
