<?php

namespace App\Repository\Admin;

use App\Filter\Employees\EmployeeFilter;
use App\Models\User;
use App\Repository\BaseRepositoryImplementation;
use App\Statuses\EmployeeStatus;
use App\Statuses\UserTypes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminRepository extends BaseRepositoryImplementation
{
    public function getFilterItems($filter)
    {
        $records = User::query()->where('type', UserTypes::EMPLOYEE);
        if ($filter instanceof EmployeeFilter) {

            $records->when(isset($filter->name), function ($records) use ($filter) {
                $records->where('name', 'LIKE', '%' . $filter->getName() . '%');
            });

            $records->when(isset($filter->orderBy), function ($records) use ($filter) {
                $records->orderBy($filter->getOrderBy(), $filter->getOrder());
            });


            return $records->paginate($filter->per_page);
        }
        return $records->paginate($filter->per_page);
    }

    public function create_employee($data)
    {

        DB::beginTransaction();

        try {

            if (auth()->user()->type == UserTypes::SUPER_ADMIN || auth()->user()->type == UserTypes::ADMIN) {
                $user = new User();
                if (Arr::has($data, 'image')) {
                    $file = Arr::get($data, 'image');
                    $extention = $file->getClientOriginalExtension();
                    $file_name = Str::uuid() . date('Y-m-d') . '.' . $extention;
                    $file->move(public_path('images'), $file_name);

                    $image_file_path = public_path('images/' . $file_name);
                    $image_data = file_get_contents($image_file_path);
                    $base64_image = base64_encode($image_data);
                    $user->image = $base64_image;
                }
                if (Arr::has($data, 'id_photo')) {
                    $file = Arr::get($data, 'id_photo');
                    $extention = $file->getClientOriginalExtension();
                    $file_name = Str::uuid() . date('Y-m-d') . '.' . $extention;
                    $file->move(public_path('images'), $file_name);

                    $image_file_path = public_path('images/' . $file_name);
                    $image_data = file_get_contents($image_file_path);
                    $base64_image = base64_encode($image_data);
                    $user->id_photo = $base64_image;
                }

                if (Arr::has($data, 'biography')) {
                    $file = Arr::get($data, 'biography');
                    $extention = $file->getClientOriginalExtension();
                    $file_name = Str::uuid() . date('Y-m-d') . '.' . $extention;
                    $file->move(public_path('images'), $file_name);

                    $image_file_path = public_path('images/' . $file_name);
                    $image_data = file_get_contents($image_file_path);
                    $base64_image = base64_encode($image_data);
                    $user->biography = $base64_image;
                }

                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->password = Hash::make($data['password']);
                $user->departement = $data['departement'];
                $user->skills = $data['skills'];
                $user->gender = $data['gender'];
                $user->type = $data['type'];
                $user->status = $data['status'];
                $user->phone = $data['phone'];
                $user->serial_number = $data['serial_number'];
                $user->save();
            } else {
                throw new \Exception('You Cannnot Add New Employee Beacuse dont have Permission To Do That..!');
            }

            DB::commit();

            if ($user === null) {
                throw new \Exception('User was not created');
            }

            return $user;
        } catch (\Exception $e) {
            DB::rollback();

            Log::error($e->getMessage());

            throw $e;
        }
    }

    public function profile()
    {
        return User::where('id', Auth::id())->first();
    }

    /**
     * Specify Model class name.
     * @return mixed
     */
    public function model()
    {
        return User::class;
    }
}
