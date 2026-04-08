<?php

namespace App\Repositories\Hrm;

use App\Helpers\Helper;
use App\Models\Accounts;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Rats\Zkteco\Lib\ZKTeco;

class EmployeeRepositories
{
    /**
     * @var employe
     */
    private $model;

    /**
     * Repository Position.
     * @param emplyee $emplyee
     */
    public function __construct(Employee $emplyee)
    {
        $this->model = $emplyee;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {
        $result = $this->model::latest()->get();
        return $result;
    }


    /**
     * @param $request
     * @return mixed
     */

    public function getList($request)
    {
        $columns = array(
            0 => 'id',
            1 => 'name',
        );

        $edit = Helper::roleAccess('hrm.employee.edit') ? 1 : 0;
        $delete = Helper::roleAccess('hrm.employee.destroy') ? 1 : 0;
        $view = Helper::roleAccess('hrm.employee.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->model::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $emplyee = $this->model::offset($start)
                ->limit($limit)
                // ->orderBy($order, $dir)
                ->orderBy('id_card', 'asc');
                if((isset($request->status)) && $request->status != "all"){
                    $emplyee = $emplyee->where("employee_status",$request->status);
                }else{
                    $emplyee = $emplyee->where("employee_status","present");
                }

                $emplyee = $emplyee->get();
                
            $totalFiltered = $this->model;
            if((isset($request->status)) && $request->status != "all"){
                $totalFiltered = $totalFiltered->where("employee_status",$request->status);
            }else{
                $emplyee = $emplyee->where("employee_status","present");
            }
            $totalFiltered = $totalFiltered->count();

        } else {
            $search = $request->input('search.value');
            $emplyee = $this->model::where('name', 'like', "%{$search}%")
            ->orwhere('dob','like',"%{$search}%")
            ->orwhere('gender','like',"%{$search}%")
            ->orwhere('personal_phone','like',"%{$search}%")
            ->orwhere('office_phone','like',"%{$search}%")
            ->orwhere('nid','like',"%{$search}%")
            ->orwhere('email','like',"%{$search}%")
            ->orwhere('present_address','like',"%{$search}%")
            ->orwhere('department','like',"%{$search}%")
            ->orwhere('salary','like',"%{$search}%")
            ->orwhere('over_time_is','like',"%{$search}%")
            ->orwhere('join_date','like',"%{$search}%")
                ->offset($start)
                ->limit($limit);
                if((isset($request->status)) && $request->status != "all"){
                    $emplyee = $emplyee->where("employee_status",$request->status);
                }else{
                    $emplyee = $emplyee->where("employee_status","present");
                }
                $emplyee =  $emplyee->orderBy('id_card', 'asc')
                ->get();
            $totalFiltered = $this->model::where('name', 'like', "%{$search}%")->count();
        }

        $data = array();
        if ($emplyee) {
            foreach ($emplyee as $key => $value) {
                $nestedData = [];
                $nestedData['id'] = $value->id;
                $nestedData['sl'] = $key + 1;
                $nestedData['name'] = $value->name;
                $nestedData['dob'] = $value->dob;
                $nestedData['gender'] = $value->gender;
                $nestedData['personal_phone'] = $value->personal_phone;
                $nestedData['office_phone'] = $value->office_phone;
                $nestedData['nid'] = $value->nid;
                $nestedData['email'] = $value->email;
                $nestedData['department'] = $value->department;
                $nestedData['present_address'] = $value->present_address;
                $nestedData['salary'] = $value->salary;
                $nestedData['over_time_is'] = $value->over_time_is;
                $nestedData['join_date'] = $value->join_date;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('hrm.employee.edit', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('hrm.employee.show', $value->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('hrm.employee.destroy', $value->id) . '" delete_id="' . $value->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $value->id . '"><i class="fa fa-times"></i></a>';
                    else
                        $delete_data = '';
                    $nestedData['action'] = $edit_data . ' ' . $view_data . ' ' . $delete_data;
                else :
                    $nestedData['action'] = '';
                endif;
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        return $json_data;
    }
    
    /**
     * @param $request
     * @return mixed
     */
    public function details($id)
    {
        $result = $this->model::find($id);
        return $result;
    }

    public function store($request)
    {
        $employee = new Employee();
        $employee->name = $request->name;
        $employee->dob = $request->dob;
        $employee->gender = $request->gender;
        $employee->personal_phone = $request->personal_phone;
        $employee->branch_id = $request->branch_id;
        $employee->office_phone = $request->office_phone;
        $employee->marital_status = $request->marital_status;
        $employee->nid = $request->nid;
        $employee->email = $request->email;
        $employee->last_in_time = $request->last_in_time;
        $employee->reference = $request->reference;
        $employee->department = $request->department;
        $employee->position_id = $request->position_id;
        $employee->experience = $request->experience;
        $employee->present_address = $request->present_address;
        $employee->permanent_address = $request->permanent_address;
        $employee->achieved_degree = $request->achieved_degree;
        $employee->institution = $request->institution;
        $employee->passing_year = $request->passing_year;
        $employee->salary = $request->salary;
        $employee->id_card = $request->id_card;
        $employee->join_date = $request->join_date;
        $employee->blood_group = $request->blood_group;
        $employee->status = "Active";
        $employee->over_time_is = $request->over_time_is;
        $employee->created_by = auth()->id();
        $employee->guardian_number = $request->guardian_number;
        $employee->employee_status = $request->status;
        $employee->auto_checkout = $request->auto_checkout;
        

        $image = $request->file('image');
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imageName  = $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('photo')) {
                Storage::disk('public')->makeDirectory('photo');
            }

            $image->storeAs('photo', $imageName, 'public');
        } else {
            $imageName = null;
        }
        $employee->image = $imageName;

        // Emplyee Signature

        $emp_signature = $request->file('emp_signature');
        if (isset($emp_signature)) {
            $currentDate = Carbon::now()->toDateString();
            $imageNameemp_signature  = $currentDate . '-' . uniqid() . '.' . $emp_signature->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('photo')) {
                Storage::disk('public')->makeDirectory('photo');
            }

            $emp_signature->storeAs('photo', $imageNameemp_signature, 'public');
        } else {
            $imageNameemp_signature = null;
        }

        $employee->emp_signature = $imageNameemp_signature;

        // Emplyee Guardian nid photo

        $guardian_nid = $request->file('guardian_nid');
        if (isset($guardian_nid)) {
            $currentDate = Carbon::now()->toDateString();
            $imageNameguardian_nid  = $currentDate . '-' . uniqid() . '.' . $guardian_nid->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('photo')) {
                Storage::disk('public')->makeDirectory('photo');
            }

            $guardian_nid->storeAs('photo', $imageNameguardian_nid, 'public');
        } else {
            $imageNameguardian_nid = null;
        }

        $employee->guardian_nid = $imageNameguardian_nid;
        $employee->am_name = $request->am_name;
        $employee->area = json_encode($request->area);
        $employee->save();

     if(env("ZKTECO")){
              $employeedf = createZKTecoEmployee([
                "emp_code"=> $employee->id_card,
                "first_name"=> $request->am_name,
                "last_name"=> null,
                "nickname"=> null,
                "card_no"=> null,
                "department"=> 1,
                "position"=> null,
                "hire_date"=> $request->join_date ?? date("Y-m-d"),
                "gender"=> $employee['gender'] ?? null,
                "birthday"=> $employee['dob'] ?? null,
                "verify_mode"=> 0,
                "emp_type"=> null,
                "contact_tel"=> null,
                "office_tel"=> $employee['office_phone'] ?? null,
                "mobile"=> $employee['personal_phone'] ?? null,
                "national"=> null,
                "city"=> null,
                "address"=> $employee['permanent_address'] ?? null,
                "postcode"=> null,
                "email"=> $employee['email'] ?? null,
                "enroll_sn"=> "",
                "ssn"=> null,
                "religion"=> null,
                "enable_att"=> true,
                "enable_overtime"=> false,
                "enable_holiday"=> true,
                "dev_privilege"=> 0,
                "area"=> $request->area,
                "app_status"=> 0,
                "app_role"=> 1
            ]);
            $employee->device_id = $employeedf['id'] ?? 0;
            $employee->save();
        }else{
            $employee->save();
        }

        return $employee;
    }

    public function update($request, $id)
    {
        $employee = $this->model::find($id);
        $employee->name = $request->name;
        $employee->dob = $request->dob;
        $employee->id_card = $request->id_card;
        $employee->gender = $request->gender;
        $employee->personal_phone = $request->personal_phone;
        $employee->branch_id = $request->branch_id;
        $employee->office_phone = $request->office_phone;
        $employee->marital_status = $request->marital_status;
        $employee->nid = $request->nid;
        $employee->email = $request->email;
        $employee->last_in_time = $request->last_in_time;
        $employee->reference = $request->reference;
        $employee->department = $request->department;
        $employee->position_id = $request->position_id;
        $employee->experience = $request->experience;
        $employee->present_address = $request->present_address;
        $employee->permanent_address = $request->permanent_address;
        $employee->achieved_degree = $request->achieved_degree;
        $employee->institution = $request->institution;
        $employee->passing_year = $request->passing_year;
        $employee->salary = $request->salary;
        $employee->join_date = $request->join_date;
        $employee->blood_group = $request->blood_group;
        $employee->over_time_is = $request->over_time_is;
        $employee->updated_by = auth()->id();
        $employee->guardian_number = $request->guardian_number;
        $employee->employee_status = $request->status;
        $employee->auto_checkout = $request->auto_checkout;


        $image = $request->file('image');
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imageName  = $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('photo')) {
                Storage::disk('public')->makeDirectory('photo');
            }

            Storage::disk('public')->delete('photo/' . $employee->image);

            $image->storeAs('photo', $imageName, 'public');
            $employee->image = $imageName;
        }


        // Emplyee Signature

        $emp_signature = $request->file('emp_signature');
        if (isset($emp_signature)) {
            $currentDate = Carbon::now()->toDateString();
            $imageNameemp_signature  = $currentDate . '-' . uniqid() . '.' . $emp_signature->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('photo')) {
                Storage::disk('public')->makeDirectory('photo');
            }

            Storage::disk('public')->delete('photo/' . $employee->emp_signature);

            $emp_signature->storeAs('photo', $imageNameemp_signature, 'public');
            $employee->emp_signature = $imageNameemp_signature;
        }

        // Emplyee Guardian NID Photo

        $guardian_nid = $request->file('guardian_nid');
        if (isset($guardian_nid)) {
            $currentDate = Carbon::now()->toDateString();
            $imageNameguardian_nid  = $currentDate . '-' . uniqid() . '.' . $guardian_nid->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('photo')) {
                Storage::disk('public')->makeDirectory('photo');
            }
            Storage::disk('public')->delete('photo/' . $employee->guardian_nid);
            $guardian_nid->storeAs('photo', $imageNameguardian_nid, 'public');
            $employee->guardian_nid = $imageNameguardian_nid;
        }

        $employee->am_name = $request->am_name;
        $employee->area = json_encode($request->area);
        $employee->save();

        if (env("ZKTECO")) {
            $local =  editZKTecoEmployee($employee->device_id, [
                "emp_code" => $employee->id_card,
                "first_name" => $request->am_name,
                "last_name" => null,
                "nickname" => null,
                "card_no" => null,
                "department" => 1,
                "position" => null,
                "hire_date" => $request->join_date ?? date("Y-m-d"),
                "gender" => null,
                "birthday" => null,
                "verify_mode" => 0,
                "emp_type" => null,
                "contact_tel" => null,
                "office_tel" => null,
                "mobile" => null,
                "national" => null,
                "city" => null,
                "address" => null,
                "postcode" => null,
                "email" => null,
                "enroll_sn" => "",
                "ssn" => null,
                "religion" => null,
                "enable_att" => true,
                "enable_overtime" => false,
                "enable_holiday" => true,
                "dev_privilege" => 0,
                "area" => $request->area,
                "app_status" => 0,
                "app_role" => 1
            ]);



            if (!is_array($local)) {
                $data = json_decode($local, true);
                // dd( $data);
            }

            if (isset($data['detail']) && !is_array($local)) {
                $employeedf = createZKTecoEmployee([
                    "emp_code" => $employee->id_card,
                    "first_name" => $request->am_name,
                    "last_name" => null,
                    "nickname" => null,
                    "card_no" => null,
                    "department" => 1,
                    "position" => null,
                    "hire_date" => $request->join_date ?? date("Y-m-d"),
                    "gender" => null,
                    "birthday" => null,
                    "verify_mode" => 0,
                    "emp_type" => null,
                    "contact_tel" => null,
                    "office_tel" => null,
                    "mobile" => null,
                    "national" => null,
                    "city" => null,
                    "address" => null,
                    "postcode" => null,
                    "email" => null,
                    "enroll_sn" => "",
                    "ssn" => null,
                    "religion" => null,
                    "enable_att" => true,
                    "enable_overtime" => false,
                    "enable_holiday" => true,
                    "dev_privilege" => 0,
                    "area" => $request->area,
                    "app_status" => 0,
                    "app_role" => 1
                ]);
                $employee->device_id = $employeedf['id'] ?? 0;
                $employee->save();
            }
        } else {
            $employee->save();
        }

        return $employee;
    }

    public function statusUpdate($id, $status)
    {
        $customer = $this->model::find($id);
        $customer->status = $status;
        $customer->save();
        return $customer;
    }

    public function destroy($id)
    {
        $customer = $this->model::find($id);
        $customer->delete();
        return true;
    }
}
