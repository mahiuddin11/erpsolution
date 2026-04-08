<?php

namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Navigation;

class NavigationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $navigation = Navigation::all();
        // dd($navigation);
        return view('backend.setup.index', get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $navigation = Navigation::all();
        return view('backend.setup.create', get_defined_vars());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $navigation = new Navigation();
        $navigation->navigation_id  = $request->navigation_id;
        $navigation->parent_id  = $request->parent_id;
        $navigation->object_id  = $request->object_id;
        $navigation->label = $request->label;
        $navigation->url = $request->url;
        $navigation->icon = $request->icon;
        $navigation->object_class = $request->object_class;
        $navigation->extra_attribute = $request->extra_attribute;
        $navigation->target = $request->target;
        $navigation->user_type = $request->user_type;
        $navigation->save();
        return redirect()->route('setup.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $navigation = Navigation::findOrFail($id);
        return view('backend.setup.edit', get_defined_vars());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $navigation = new Navigation();
        $data = [

            'label' => $request->label,
            'url' => $request->url,
            'icon' => $request->icon,
            'object_class' => $request->object_class,
            'extra_attribute' => $request->extra_attribute,
            'target' => $request->target,
            'user_type' => $request->user_type,

        ];
        $navigation->where('id', $id)->update($data);
        return redirect()->route('setup.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function destroy(Request $request, $id)
    {
        $delete = Navigation::destroy($id);

        return redirect()->route('setup.index');
    }
}