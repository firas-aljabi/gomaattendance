<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;


class DashboardDataResource extends JsonResource
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
            'all_employees_count' => number_format($this->all_employees_count),
            'on_duty_employees_count' => number_format($this->on_duty_employees_count),
            'on_vacation_employees_count' => number_format($this->on_vacation_employees_count),
        ];
    }
}
