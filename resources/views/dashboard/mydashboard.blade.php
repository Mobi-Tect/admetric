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
    <div class="panel-body">
        <!-- Display Validation Errors -->
        
		 
        <!-- New Task Form -->
        <form action="{{ url('storeNewDashboard') }}" method="POST" class="form-horizontal">
            {!! csrf_field() !!}

            <!-- Task Name -->
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="task-name" class="col-sm-3 control-label">Dashboard</label>

                <div class="col-sm-6">
                    <input type="text" name="name" id="task-name" class="form-control">
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
                        <i class="fa fa-plus"></i> Add Dashboard
                    </button>
                </div>
            </div>
        </form>
    </div>

      <!-- Create Task Form... -->

    <!-- Current Tasks -->
    @if (count($dashboards) > 0)
        <div class="panel panel-default">
            <div class="panel-heading">
                Current Dashboard
            </div>

            <div class="panel-body">
                <table class="table table-striped task-table">

                    <!-- Table Headings -->
                    <thead>
                        <th width="70%">Name</th>
                        <th width="30%" align="center" style="text-align:center;">Action</th>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @foreach ($dashboards as $dashboard)
                            <tr style="padding:5px; background-color:#FFF;">
                                <td style="padding:5px;">
                                    <div>{{ $dashboard->name }}</div>
                                </td>

                                <td align="center" style="text-align:center;padding:5px;">
                                     
                        			<a href="{{ url('editDashboard/'.$dashboard->id) }}" style="color:#000000;">
                                        <i class="fa fa-btn fa-edit"></i>
                                    </a> | 
                                    <a href="{{ url('deleteDashboard/'.$dashboard->id) }}" style="color:#000000;">
                                        <i class="fa fa-btn fa-trash"></i>
                                    </a>
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection


 
