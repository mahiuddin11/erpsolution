@extends('attends.layout')
    @section('content')
        <div class="row" style="margin-top: 20px">
            <div class="col-lg-12 margin-tb">
                <div style="text-align:center;">
                    <h4>Show All Emplyee Information</h4>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{route('attends.index')}}">Back</a>
                </div>
            </div>    
        </div>
        <div class="row" style="margin-top: 20px; text-align:center;">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Emplyee_Id</strong><br>
                    {{ $attendance->emplyee_id}}

                </div>
            </div>
        </div>
         
        <div class="row" style="margin-top: 20px; text-align:center;">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Date</strong><br>
                    {{ $attendance->date}}

                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 20px; text-align:center;">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Sign_In</strong><br>
                    {{ $attendance->sign_in}}

                </div>
            </div>
        </div>
        
        <div class="row" style="margin-top: 20px; text-align:center;">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Sign_Out</strong><br>
                    {{ $attendance->sign_out}}

                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 20px; text-align:center;">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Status</strong><br>
                    {{ $attendance->status}}

                </div>
            </div>
        </div>
        
    @endsection