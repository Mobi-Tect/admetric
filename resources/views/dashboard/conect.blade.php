<!-- resources/views/tasks/index.blade.php -->

@extends('layouts.app')

@section('content')

    <!-- Bootstrap Boilerplate... -->

  

      <!-- Create Task Form... -->

    <!-- Current Tasks -->
    
<div class="content-wrapper">
@if (!empty($msg))
	<div class="alert alert-danger" style="cursor:pointer;">
        <strong>Whoops! Something went wrong!</strong>

        <br><br>

        <ul>
                <li>{{ $msg }}</li>
        </ul>
    </div>
    @endif	
    @if (!empty($msgs))
	<div class="alert alert-success" style="cursor:pointer;">
        <strong>Success!</strong>

        <br><br>

        <ul>
                <li>{{ $msgs }}</li>
        </ul>
    </div>
    @endif			

<div class="jumbotron">
  <div class="container text-center">
    <h1>Wellcome</h1>      
    <p><a href="{{ url('connection') }}" class="btn btn-success btn-lg">Connect</a></p>
  </div>
</div>
@endsection


 
