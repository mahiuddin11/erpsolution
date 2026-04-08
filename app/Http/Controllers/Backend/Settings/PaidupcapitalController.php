<?php

namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaidupCapital;

class PaidupcapitalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

   

    public function index()
    {
        $paidcapital = PaidupCapital::latest()->first();
        $title = "Paidup Capital";
        return view('backend.pages.settings.account.paidup_capital',compact('paidcapital','title'));
    }


    public function paidupcapital(Request $request)
    {

        // Assuming $request contains the values for price and share
   $price = $request->input('price');
   $share = $request->input('share');

   // Find the first row or create a new instance if not found

   $model = PaidupCapital::firstOrNew(["id"=>1]);
   $model->price = $price;
   $model->share = $share;
   $model->save();
  

   return redirect()->back();
       
   
       // return redirect()->route('consoltant.index')->with('success', 'Consultant saved successfully.');
   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
