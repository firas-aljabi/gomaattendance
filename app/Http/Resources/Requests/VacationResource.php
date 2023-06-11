<?php

namespace App\Http\Resources\Requests;

use App\Http\Resources\Admin\EmployeeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VacationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "status" => $this->status,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            "type" => $this->type,
            'user' => EmployeeResource::make($this->whenLoaded('user')),
        ];
    }
}
