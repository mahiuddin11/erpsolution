<?php

namespace App\Repositories\Recruitment;

use App\Helpers\Helper;
use App\Models\AssetsCategory;
use App\Models\CandidateShortlist;
use Illuminate\Support\Facades\Auth;
use App\Models\CandidateInforamtion;
use App\Services\Recruitment\candidateShortlistService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\PseudoTypes\False_;

class CandidateShortlistRepositories
{
    /**
     * @var user_id
     */
    private $user_id;
    /**
     * @var CandidateShortlist
     */
    private $candidateShortlist;
    /**
     * CourseRepository constructor.
     * @param productUnit $productUnit
     */
    public function __construct(CandidateShortlist $candidateShortlist)
    {
        $this->candidateShortlist = $candidateShortlist;
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
        $result = $this->candidateShortlist::latest()->get();
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

        $edit = Helper::roleAccess('candidate.shortlist.edit') ? 1 : 0;
        $delete = Helper::roleAccess('candidate.shortlist.destroy') ? 1 : 0;
        $view = Helper::roleAccess('candidate.shortlist.show') ? 0 : 0;
        $ced = $edit + $delete + $view;

        $totalData = $this->candidateShortlist::count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $candidateShortlists = $this->candidateShortlist::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                //->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->candidateShortlist::count();
        } else {
            $search = $request->input('search.value');
            $candidateShortlists = $this->candidateShortlist::where('category_name', 'like', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                // ->orderBy('status', 'desc')
                ->get();
            $totalFiltered = $this->candidateShortlist::where('category_name', 'like', "%{$search}%")->count();
        }



        $data = array();
        if ($candidateShortlists) {
            foreach ($candidateShortlists as $key => $candidateShortlist) {
                $nestedData['id'] = $key + 1;
                $nestedData['candidateinfo_id'] = $candidateShortlist->candiateInfo->first_name;
                $nestedData['position'] = $candidateShortlist->position;
                $nestedData['interview_date'] = $candidateShortlist->interview_date;
                $nestedData['date'] = $candidateShortlist->date;

                if ($ced != 0) :
                    if ($edit != 0)
                        $edit_data = '<a href="' . route('candidate.shortlist.edit', $candidateShortlist->id) . '" class="btn btn-xs btn-default"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    else
                        $edit_data = '';
                    if ($view != 0)
                        $view_data = '<a href="' . route('candidate.shortlist.show', $candidateShortlist->id) . '" class="btn btn-xs btn-default"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    else
                        $view_data = '';
                    if ($delete != 0)
                        $delete_data = '<a delete_route="' . route('candidate.shortlist.destroy', $candidateShortlist->id) . '" delete_id="' . $candidateShortlist->id . '" title="Delete" class="btn btn-xs btn-default delete_row uniqueid' . $candidateShortlist->id . '"><i class="fa fa-times"></i></a>';
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
        $result = $this->candidateShortlist::find($id);
        return $result;
    }

    public function store($request)
    {

        $data = CandidateInforamtion::findOrFail($request->candidateinfo_id);
        $data->status = 'shortlisted';
        $data->save();

        $candidateShortlist = new $this->candidateShortlist();
        $candidateShortlist->candidateinfo_id = $request->candidateinfo_id;
        $candidateShortlist->position = $request->position;
        $candidateShortlist->interview_date = $request->interview_date;
        $candidateShortlist->date = $request->date;
        $candidateShortlist->save();
        return $candidateShortlist;
    }

    public function update($request, $id)
    {
        $candidateShortlist = $this->candidateShortlist::findOrFail($id);
        $candidateShortlist->candidateinfo_id = $request->candidateinfo_id;
        $candidateShortlist->position = $request->position;
        $candidateShortlist->interview_date = $request->interview_date;
        $candidateShortlist->date = $request->date;
        $candidateShortlist->save();
        return $candidateShortlist;
    }

    public function statusUpdate($id, $status)
    {
        $candidateShortlist = $this->candidateShortlist::find($id);
        $candidateShortlist->status = $status;
        $candidateShortlist->save();
        return $candidateShortlist;
    }

    public function destroy($id)
    {
        $candidateShortlist = $this->candidateShortlist::find($id);
        $candidateShortlist->delete();
        return true;
    }
}
