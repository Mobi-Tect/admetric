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

        <div class="panel panel-default" style="width:100%;">
            <div class="panel-heading">
               Check Out
            </div>

            <div class="container" style="margin-bottom:10px; margin-top:10px; padding:0px; width:98%;">
              <div class="row">
              	<form action="{{ url('/charge') }}" method="post">
  <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
          data-key="pk_test_Qsa41uGqdjgLvxlso8rlg3hQ"
          data-description="{{ $packages->title }}"
          data-amount="{{ $packages->price }}00"
          data-locale="auto"></script>
          {!! csrf_field() !!}
          <input type="hidden" name="pid" value="{{ $packages->id }}">
</form>
                 </div>
                </div>
             
                
              </div>
            </div>
        </div>

@endsection


 
