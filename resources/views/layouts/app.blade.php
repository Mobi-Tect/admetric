<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admetric | Dashboard</title>

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="{{ url('') }}/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('') }}/assets/jquery_ui/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="{{ url('') }}/assets/css/daterangepicker.css" />
   <!-- <link rel="stylesheet" type="text/css" href="{{url('')}}/assets/bar/css/bi-style.css" />-->
   <link rel="stylesheet" href="{{ url('') }}/assets/chartist/chartist.min.css">
   <link rel="stylesheet" href="{{ url('') }}/assets/fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
   <link rel="stylesheet" href="{{ url('') }}/assets/fancybox/source/helpers/jquery.fancybox-buttons.css" type="text/css" media="screen" />
   <link rel="stylesheet" href="{{ url('') }}/assets/fancybox/source/helpers/jquery.fancybox-thumbs.css" type="text/css" media="screen" />
   <link rel="stylesheet" href="{{ url('') }}/assets/c/css/application.css" type="text/css" media="screen" />

    <style>
body {
	font-family: 'Lato';
}

.fa-btn {
	margin-right: 6px;
}
.panel-body table{
	border-collapse:separate;
	border-spacing: 5px;
}
.panel-body table tr td {
	border-top:none;
	border:2px solid #ddd;
	width:48%;
	margin:0px 1%;
	padding:0px;
}

.tb1 {
	width:100%;
	height:auto;
	float:left;
}

.tb2 {
	width:100%;
	height:auto;
	float:left;
}

.tb table  {
	width:100%;
}
			
.tb table tr {
	width:100%;
	min-height:150px;
}	
	
.tb table tr td .min-h {
	min-height:200px;
	padding:10px;
}

  #sortable1, #sortable2 {
    /* border: 1px solid #eee; */
    width: 100%;
    height: 100%;
    list-style-type: none;
    margin: 0;
    padding: 5px 0 0 0;
    float: left;
    margin-right: 10px;
	margin-bottom:-9999px;
	padding-bottom:9999px;
	overflow:hidden;
  }
  
  #sortable1>li, #sortable2>li {
    margin: 0 5px 5px 5px;
    font-size: 1.2em;
	padding:5px;
	min-height:200px;
    width: 98%;
  }
  .panel-body{
	  overflow:hidden;
  }
  .c-width-100{
	  width:100px;
  }
  
  .c-height-100{
	  height:100px;
  }
  .show-calendar{
	  z-index:9999;
  }
    </style>
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    Admetric
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li><a href="{{ url('/') }}">Home</a></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @else
                    	<li><a href="{{ url('/plans') }}">Upgrade</a></li>
                    	<li><a href="javascript:void(0);">{{ remaingDays(Auth::user()->id) }}</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }}<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                             <li><a href="{{ url('/myDashboard') }}"><i class="fa fa-btn fa-dashboard"></i>My Dashboard</a></li>
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
<div class="container">
    @yield('content')
</div>


    <!-- JavaScripts -->
    <script>
	var cpc_url = "{{ url('getCpc') }}";
	var account_url = "{{ url('getChildAccounts') }}";
	var campaign_url = "{{ url('getCampaignsString') }}";
	var sort_url = "{{ url('metricSorting') }}";
	var group_url = "{{ url('getAdGroups') }}";
	var ad_url = "{{ url('getAds') }}";
	var key_url = "{{ url('getKeywords') }}";
	var setting_url = "{{ url('saveSettings') }}";
	var get_setting_url = "{{ url('getSettingsCPC') }}";
	var savea_url = "{{ url('saveAccount') }}";
	var progress_url = "{{ url('progressBar') }}";
	var per_url = "{{ url('persontage') }}";
	var token = '{{ csrf_token() }}';
	var acnform = "{{ url('accountName') }}";
	var base_url = "{{ url('') }}";
	var get30Days = "{{ get30Days() }}";
	var msetting_url = "{{ url('masterSetting') }}";
	var progress_url2 = "{{ url('progressBar2') }}";
	var xhr = [];
	</script>
    <script src="{{ url('') }}/assets/js/jquery.js"></script>
    <script src="{{ url('') }}/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="{{ url('') }}/assets/jquery_ui/jquery-ui.js"></script>
    <!-- Add fancyBox -->
<script type="text/javascript" src="{{ url('') }}/assets/fancybox/source/jquery.fancybox.pack.js"></script>



<script type="text/javascript" src="{{ url('') }}/assets/fancybox/source/helpers/jquery.fancybox-buttons.js"></script>
<script type="text/javascript" src="{{ url('') }}/assets/fancybox/source/helpers/jquery.fancybox-media.js"></script>


<script type="text/javascript" src="{{ url('') }}/assets/fancybox/source/helpers/jquery.fancybox-thumbs.js"></script>
<script type="text/javascript" src="{{ url('') }}/assets/js/timing.js"></script>
<script type="text/javascript" src="{{ url('') }}/assets/js/moment.js"></script>
<script type="text/javascript" src="{{ url('') }}/assets/js/daterangepicker.js"></script>
<!--<script type="text/javascript" src="{{ url('') }}/assets/bar/jquery-barIndicator.js"></script>-->
 <script src="{{ url('') }}/assets/chartist/chartist.js"></script>
 <script src="{{ url('') }}/assets/c/js/Calendar.js"></script>
 <script src="{{ url('') }}/assets/c/js/app.js"></script>
    <script type="text/javascript" src="{{ url('') }}/assets/js/scripts.js"></script>
</body>
</html>
