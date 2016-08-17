<!-- resources/views/tasks/index.blade.php -->

@extends('layouts.app')

@section('content')

    <!-- Bootstrap Boilerplate... -->

    <div class="panel-body">
        <!-- Display Validation Errors -->
       

        <!-- New Dashboard Form -->
        <form action="{{ url('updateDashboard/'.$id) }}" method="POST" class="form-horizontal">
            {!! csrf_field() !!}

            <!-- Task Name -->
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="task-name" class="col-sm-3 control-label">Dashboard Name</label>

                <div class="col-sm-6">
                    <input type="text" name="name" id="board-name" class="form-control" value="{{ $data->name }}">
                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Add Task Button -->
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="submit" class="btn btn-default">
                        <i class="fa fa-plus"></i> Update Dashboard
                    </button>
                </div>
            </div>
        </form>
    </div>

      <!-- Create Task Form... -->

    <!-- Current Tasks -->
    
@endsection


 
