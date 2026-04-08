<?php

namespace App\Repositories\Recruitment;

use App\Helpers\Helper;
use App\Models\Employee;
use App\Models\CandidateInforamtion;
use Illuminate\Support\Facades\Auth;
use App\Models\CandidateSelection;
use App\Services\Recruitment\CandidateSelectionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\PseudoTypes\False_;

class CandidateSelectionRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var CandidateSelection
     */
    private $candidateSelection;
    /**
     * CourseRepository constructor.
     * @param productUnit $productUnit
     */
    public function __construct(CandidateSelection $candidateSelection)
    {
        $this->candidateSelection = $candidateSelection;
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
        $result = $this->candidateSelection::latest()->get();
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

        $edit = Helper::roleAccess('candidate.selection.edit') ? 0 : 0;
        $delete = Helper::roleAccess('candidate.selection.destroy') ? 1 : 0;
        $view = Helper::roleAccess('candidate.selection.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->candidateSelection::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $candidateSelections = $this->candidateSelection::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->candidateSelection::count();
        } else {
            $search = $request->input('search.value');
            $candidateSelections = $this->candidateSelection::where('category_name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->candidateSelection::where('category_name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($candidateSelections) {
            foreach ($candidateSelections as $key => $candidateSelection) {
                $nestedData['id'] = $key + 1;
                $nestedData['candidateinfo_id'] = $candidateSelection->candiateInfo->first_name;
                $nestedData['position'] = $candidateSelection->position;
                $nestedData['terms'] = $candidateSelection->interview_date;
                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('candidate.selection.edit', $candidateSelection->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('candidate.selection.show', $candidateSelection->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('candidate.selection.destroy', $candidateSelection->id) . '" delete_id="' . $candidateSelection->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $candidateSelection->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->candidateSelection::find($id);
        return $result;
    }

    public function store($request)
    {

        $data = CandidateInforamtion::findOrFail($request->candidateinfo_id);
        $data->status = 'selected';
        $data->save();


        $addEmployee = new Employee();
        $addEmployee->name = $data->first_name;
        $addEmployee->email = $data->email;
        $addEmployee->personal_phone = $data->phone;
        $addEmployee->experience = $data->work_experience;
        $addEmployee->present_address = $data->present_address;
        $addEmployee->permanent_address = $data->permanent_address;
        $addEmployee->achieved_degree = $data->obtain_degree;
        $addEmployee->institution = $data->university;
        $addEmployee->experience = $data->work_experience;
        $addEmployee->save();

        $candidateSelection = new $this->candidateSelection();
        $candidateSelection->candidateinfo_id = $request->candidateinfo_id;
        $candidateSelection->employee_id = $request->employee_id;
        $candidateSelection->position = $request->position;
        $candidateSelection->save();
        return $candidateSelection;
    }

    public function update($request, $id)
    {
        $candidateSelection = $this->candidateSelection::findOrFail($id);
        $candidateSelection->candidateinfo_id = $request->candidateinfo_id;
        $candidateSelection->position = $request->position;
        $candidateSelection->interview_date = $request->interview_date;
        $candidateSelection->date = $request->date;
        $candidateSelection->save();
        return $candidateSelection;
    }

    public function statusUpdate($id, $status)
    {
        $candidateSelection = $this->candidateSelection::find($id);
        $candidateSelection->status = $status;
        $candidateSelection->save();
        return $candidateSelection;
    }

    public function destroy($id)
    {
        $candidateSelection = $this->candidateSelection::find($id);
        $candidateSelection->delete();
        return true;
    }
}
