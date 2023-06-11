<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;
use App\Statuses\EmployeeStatus;
use App\Statuses\UserTypes;
use Illuminate\Validation\Rule;

class CreateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|max:40|min:5',
            'phone' => 'sometimes|unique:users,phone',
            'departement' => 'nullable|string',
            'skills' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'serial_number' => 'required|unique:users,serial_number',
            'gender' => 'nullable|in:male,female',
            'status' => [Rule::in(EmployeeStatus::$statuses)],
            'type' => [Rule::in(UserTypes::$statuses)],
            "biography" => "nullable|mimes:pdf|max:2048"

        ];
    }
}
