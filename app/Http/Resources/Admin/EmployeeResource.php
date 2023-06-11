<?php

namespace App\Http\Resources\Admin;

use App\Services\Admin\AdminService;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user_id = $this->id;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'type' => $this->type,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'departement' => $this->departement,
            'skills' => $this->skills,
            'serial_number' => $this->serial_number,
            'image' => $this->image,
            'id_photo' => $this->id_photo,
            'biography' => $this->biography ? $this->biography : NULL,
            'percentage' => AdminService::AttendancePercentage($user_id)
        ];
    }
}
