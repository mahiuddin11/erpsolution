<?php

namespace App\Repositories\Recruitment;

use App\Helpers\Helper;
use App\Models\AssetsCategory;
use App\Models\CandidateInforamtion;
use App\Models\EducationInfo;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductUnit;
use App\Models\WorkExpInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\PseudoTypes\False_;

class CandidateInformationRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var CandidateInformation
     */
    private $candidateInformation;
    /**
     * CourseRepository constructor.
     * @param productUnit $productUnit
     */
    public function __construct(CandidateInforamtion $candidateInformation)
    {
        $this->candidateInformation = $candidateInformation;
        //$this->middleware(function ($request, $next) {
        $this->user_id = 1; //auth()->user()->id;
        //  return $next($request);
        //});
    }


    /**
     * @param $request
     * @return mixed
     */
    public function getAllList()
    {

        $result = $this->candidateInformation::latest()->get();
        // dd($result);
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

        $edit = Helper::roleAccess('candidate.edit') ? 1 : 0;
        $delete = Helper::roleAccess('candidate.destroy') ? 1 : 0;
        $view = Helper::roleAccess('candidate.show') ? 1 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->candidateInformation::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $candidateinformations = $this->candidateInformation::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->candidateInformation::count();
        } else {
            $search = $request->input('search.value');
            $candidateinformations = $this->candidateInformation::where('category_name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->candidateInformation::where('category_name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($candidateinformations) {
            foreach ($candidateinformations as $key => $candidateinformation) {
                $nestedData['id'] = $key + 1;
                $nestedData['first_name'] = $candidateinformation->first_name;
                $nestedData['email'] = $candidateinformation->email;
                $nestedData['phone'] = $candidateinformation->phone;
                $nestedData['permanent_address'] = $candidateinformation->permanent_address;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('candidate.edit', $candidateinformation->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('candidate.show', $candidateinformation->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('candidate.destroy', $candidateinformation->id) . '" delete_id="' . $candidateinformation->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $candidateinformation->id . '"><i class="fa fa-times"></i></a>';
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

        $result = $this->candidateInformation::with('candidateinfo_id', 'obtain_degree')->find($id);

        return $result;
    }

    public function store($request)
    {
        // dd($request->all());
        $candidateinformation = new $this->candidateInformation();
        $candidateinformation->first_name = $request->first_name;
        $candidateinformation->last_name = $request->last_name;
        $candidateinformation->email = $request->email;
        $candidateinformation->phone = $request->phone;
        $candidateinformation->alternate_phone = $request->alternate_phone;
        $candidateinformation->ssn = $request->ssn;
        $candidateinformation->present_address = $request->present_address;
        $candidateinformation->permanent_address = $request->permanent_address;
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
        $candidateinformation->image = $imageName;
        $candidateinformation->save();

        for ($i = 0; $i < count($request->obtain_degree); $i++) {
            $eduInfo = new EducationInfo();
            $eduInfo->candidateinfo_id = $candidateinformation->id;
            $eduInfo->obtain_degree = $request->obtain_degree[$i];
            $eduInfo->institution = $request->institution[$i];
            $eduInfo->cgpa = $request->cgpa[$i];
            $eduInfo->comments = $request->comments[$i];
            $eduInfo->save();
        }

        for ($i = 0; $i < count($request->company_name); $i++) {
            $workInfo = new WorkExpInfo();
            $workInfo->candidateinfo_id = $candidateinformation->id;
            $workInfo->company_name = $request->company_name[$i];
            $workInfo->experience = $request->experience[$i];
            $workInfo->supervisor = $request->supervisor[$i];
            $workInfo->save();
        }


        return $candidateinformation;
    }

    public function update($request, $id)
    {
        $candidateinformation = $this->candidateInformation::findOrFail($id);
        $candidateinformation->first_name = $request->first_name;
        $candidateinformation->last_name = $request->last_name;
        $candidateinformation->email = $request->email;
        $candidateinformation->phone = $request->phone;
        $candidateinformation->alternate_phone = $request->alternate_phone;
        $candidateinformation->ssn = $request->ssn;
        $candidateinformation->present_address = $request->present_address;
        $candidateinformation->permanent_address = $request->permanent_address;
        $candidateinformation->obtain_degree = $request->obtain_degree;
        $candidateinformation->university = $request->university;
        $candidateinformation->cgpa = $request->cgpa;
        $candidateinformation->comments = $request->comments;
        $candidateinformation->company_name = $request->company_name;
        $candidateinformation->work_experience = $request->work_experience;
        $candidateinformation->supervisor = $request->supervisor;

        $image = $request->file('image');
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imageName  = $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('photo')) {
                Storage::disk('public')->makeDirectory('photo');
            }

            Storage::disk('public')->delete('photo/' . $candidateinformation->image);

            $image->storeAs('photo', $imageName, 'public');
            $candidateinformation->image = $imageName;
        } else {
        }
        $candidateinformation->save();

        for ($i = 0; $i < count($request->obtain_degree); $i++) {
            $eduInfo = new EducationInfo();
            $eduInfo->candidateinfo_id = $candidateinformation->id;
            $eduInfo->obtain_degree = $request->obtain_degree[$i];
            $eduInfo->institution = $request->institution[$i];
            $eduInfo->cgpa = $request->cgpa[$i];
            $eduInfo->comments = $request->comments[$i];
            $eduInfo->save();
        }

        for ($i = 0; $i < count($request->company_name); $i++) {
            $workInfo = new WorkExpInfo();
            $workInfo->candidateinfo_id = $candidateinformation->id;
            $workInfo->company_name = $request->company_name[$i];
            $workInfo->experience = $request->experience[$i];
            $workInfo->supervisor = $request->supervisor[$i];
            $workInfo->save();
        }

        return $candidateinformation;
    }

    public function statusUpdate($id, $status)
    {
        $productUnit = $this->candidateInformation::find($id);
        $productUnit->status = $status;
        $productUnit->save();
        return $productUnit;
    }

    public function destroy($id)
    {
        $productUnit = $this->candidateInformation::find($id);
        $productUnit->delete();
        return true;
    }
}
