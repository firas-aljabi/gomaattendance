<?php

namespace App\Services\Requests;

use App\Filter\JustifyRequests\JustifyRequestsFilter;
use App\Repository\Requests\JustifyRequestRepository;
use App\Statuses\UserTypes;

class JustifyRequestService
{
    public function __construct(private JustifyRequestRepository $justifyRequestRepository)
    {
    }

    public function add_justify_request($data)
    {

        return $this->justifyRequestRepository->add_justify_request($data);
    }

    public function show($id)
    {
        if (auth()->user()->type == UserTypes::ADMIN) {
            return $this->justifyRequestRepository->with('user')->getById($id);
        } else {

            throw new \Exception('Unauthorized');
        }
    }


    public function getMyJustifyRequests(JustifyRequestsFilter $justifyRequestRepository)
    {
        if ($justifyRequestRepository != null) {
            return $this->justifyRequestRepository->getFilterItems($justifyRequestRepository);
        } else {
            return $this->justifyRequestRepository->paginate();
        }
    }


    public function getJustifyRequests(JustifyRequestsFilter $justifyRequestRepository = null)
    {
        if ($justifyRequestRepository != null) {
            return $this->justifyRequestRepository->getFilterItemsForAdmin($justifyRequestRepository);
        } else {
            return $this->justifyRequestRepository->paginate();
        }
    }
}
