<!-- resources/views/tasks/index.blade.php -->

@extends('layouts.app')

@section('content')

    <!-- Bootstrap Boilerplate... -->
@if (!empty($msgs))
	<div class="alert alert-success" style="cursor:pointer;">
        <strong>Success!</strong>

        <br><br>

        <ul>
                <li>{{ $msgs }}</li>
        </ul>
    </div>
    @endif	
    

      <!-- Create Task Form... -->

    <!-- Current Tasks -->
    @if (count($packages) > 0)
        <div class="panel panel-default" style="width:100%;">
            <div class="panel-heading">
                Select a membership plan
            </div>

            <div class="container" style="margin-bottom:10px; margin-top:10px; padding:0px; width:98%;">
              <div class="row">
              @foreach ($packages as $package)
                <div class="col-sm-4">
                <div style="border:#9A9090 1px solid; text-align:center; padding:5px;">
                     <h2>{{ $package->title }}</h2>
                     <p>{{ $package->descrepition }}</p>
                     <h3>${{ $package->price }}</h3>
                     <form action="{{ url('/upgrade') }}" method="post" >
                       {!! csrf_field() !!}
                     <input type="hidden" name="id" value="{{ $package->id }}">
                     <button type="submit" class="btn btn-success">Upgrade Now</button>
                     </form>
                 </div>
                </div>
                @endforeach
                
              </div>
            </div>
        </div>
    @endif
@endsection


 
