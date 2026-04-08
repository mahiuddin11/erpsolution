<?php

namespace App\Http\Controllers\Backend\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function list(){
        return view('backend.pages.test.list');
    }
    public function create(){
     return view('backend.pages.test.create');
 }
}
