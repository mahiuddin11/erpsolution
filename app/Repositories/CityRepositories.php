<?php

namespace App\Repositories;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class CityRepositories
{

    /**
     * @var city
     */
    private $city;

    /**
     * CourseRepository constructor.
     * @param city $schedule
     */
    public function __construct(Branch $city)
    {
        $this->city = $city;
    }


    /**
     * @param $request
     * @return mixed
     */
    public function getList()
    {

        $admin_id = Auth::guard('admins')->user()->id;
        $result = $this->city::with('province')->orderBy('title', 'ASC')->get();
        return $result;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function details($id)
    {

        $result = $this->city::where('id', $id)->first();
        return $result;
    }

    public function store($request)
    {
        $city = new $this->city();
        $city->province_id = $request->province_id;
        $city->title = $request->title;
        $city->status = $request->status;
        $city->save();
        return $city;
    }


    public function update($request, $id)
    {
        $city = $this->city::find($id);
        $city->province_id = $request->province_id;
        $city->title = $request->title;
        $city->status = $request->status;
        $city->save();
        return $city;
    }
    public function statusUpdate($request, $id)
    {
        $city = $this->city::find($id);
        $city->status = $request->status;
        $city->save();
        return $city;
    }
    public function delete($id)
    {
        $city = $this->city::find($id);
        $city->delete();
        return true;
    }
}