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
				<div class="panel panel-primary">
      	<div class="panel-heading c-height-100">
        	<h3 class="panel-title col-sm-4"><a href="#metric-step1" class="btn btn-success c-width-100" id="add" type="button">Add A Metric</a> <a href="{{ url('newDashboard') }}" class="btn btn-success" type="button">Create New Dashboard</a></h3>
             <ul class="nav navbar-nav btn-success col-sm-2" style="height:35px;">
             	 <li class="dropdown">
                 <a style="padding-top:7px; color:#fff;" href="javascrip:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                    {{ getDashbordName($boardid) }}<span class="caret"></span>
                </a>
                    <ul class="dropdown-menu" role="menu">
                    	 @if (count($boards) > 0)
                          	@foreach ($boards as $board)
                        		<li><a href="{{ url('/setDashboard') }}/{{ $board->no_of_board }}">{{ $board->name }}</a></li>
                             @endforeach
                         @endif
                     </ul>
                 </li>
              </ul>
              <div class="col-sm-3" style="color:#000000;">
              	<div class="daterange daterange--double oneone" ></div>
              </div>
        </div>
        <div class="panel-body">
        	 <table class="table">
                 <tr>
                 	<td>
                        <ul id="sortable1" class="connectedSortable" data-position="left">
                        @if (count($metrics) > 0)
 						@foreach ($metrics as $metric)
                         @if ($metric->position == 'left')
                          <li class="ui-state-default" id="{{ $metric->id}}" data-id="{{ $metric->id}}" data-mf="{{ $metric->metric_id }}" data-mv="{{ $metric->metric_value }}">
                          
                             
                            <div class="pre preloader{{ $metric->account_id}} ct-perfect-fourth" style=" background:#FFF; width:100%; height:200px; padding-top:50px; padding-left:39%; display:none;"> 
								<img src="{{ url('') }}/images/preloader.gif"/>
                            </div>
                            <div class="row con cant{{ $metric->account_id}}" id="first{{ $metric->id}}">
                            	<div class="col-sm-5">
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            @if ($metric->set_aacount > 0)
                                        {{ getSettingAccounts($metric->set_aacount) }}
                                        @else
                                           Select Account
                                            @endif
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu account-childs{{ $metric->account_id}}" aria-labelledby="dropdownMenu{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                                        
                                  
                                    {!! getSettingA($metric->account_id) !!} 
                                   
                                 
                                            
                                   
                                   
                                        </ul>
                                    </div>
                                    
                                    </div>
                                    <div class="col-sm-5 prec preloader{{ $metric->account_id}}" style=" background:#FFF; height:40px; display:none;">
										<img src="{{ url('') }}/images/aloader.gif"/> Loading Campaigns
                            		</div>
                                    <div class="col-sm-5 ca campaignData" @if ($metric->set_aacount == 0) style="display:none;"  @endif>
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownCampaign{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                           @if ($metric->set_campaign > 0)
                                            {{ getSettingCampaignName($metric->set_campaign) }}
                                            @else
                                                Select Camapaign
                                                @endif
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu campaignLiz" data-id="0" data-val="{{ $metric->account_id}}" aria-labelledby="dropdownCampaign{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                                          @if ($metric->set_aacount > 0)
                                            {!! getSettingCampaigns($metric->set_aacount) !!} 
                                           
                                          @endif
                                        </ul>
                                    </div>
                                    
                                    </div>
                                    <div class="col-sm-2" style="float:right; text-align:center;">
                                    <a href="#metric-setting{{ $metric->id}}" class="cs advance-options" id="{{ $metric->id}}" @if ($metric->set_aacount == 0) style="display:none;"  @endif><img src="{{ url('') }}/images/setting.png"/></a>
                              			<a class="remv" href="{{ url('metricDelete') }}/{{ $metric->id}}" data-id="{{ $metric->id}}">X</a>
                                 	</div>
                         	</div>
                          
                            <div class="prep preloader{{ $metric->account_id}}" style=" background:#FFF; width:100%; height:200px; padding-top:75px; padding-left:26%; display:none;"> 
								<img src="{{ url('') }}/images/aloader.gif"/> Loading Metric {{ $metric->metric_id }}
                            </div>
                          <div class="row con cv" style="height:100%;">
                          	<div class="col-sm-12">
                            	<div class="bar" data-met="{{ $metric->id }}" data-tar="{{ $metric->cpc_target }}"data-aid="{{ $metric->account_id}}" data-c="{{ $metric->set_aacount}}" data-mv="{{ $metric->metric_value }}" style="text-align:center; font-size:24px;"><span class="bari"></span><span class="barp" style="margin-left:10px; font-size:16px; position:absolute; top:0px;"></span></div>
                            </div>
                          </div>
                           
                          <div class="row cv">
                          	<div class="col-sm-12">
                            	<div class="graph{{ $metric->id }} ct-perfect-fourth"></div>
                            </div>
                          </div>
                          
                          </li>
                          <div id="metric-setting{{ $metric->id}}"  data-id="{{ $metric->id}}" style="width:400px;height:800px; display:none;" data-mf="{{ $metric->metric_id }}" data-mv="{{ $metric->metric_value }}">
<div class="row" style="margin-bottom:15px; text-align:center;">
<input type="hidden" class="allmetricdata" value="{{ $metric->set_aacount}}" data-c="{{ $metric->set_campaign}}" data-ag="{{ $metric->set_adgroup}}" data-k="{{ $metric->set_keyword}}" data-a="{{ $metric->set_ad}}" data-r="{{ $metric->report}}" data-d="{{ $metric->date_time}}" data-t="{{ $metric->date_type}}"/>
	<div class="col-sm-12">
    <h4>Setting</h4>
    </div>
</div>
<div class="row" style="margin-bottom:15px;">
	<div class="col-sm-4">
        <h4>
            Date Range
        </h4>
    </div>
</div>
<div class="row">
<div class="col-sm-6">
	<div class="daterange daterange--double one" did="setting" f="{{ conDate($metric->date_time) }}" ></div>
</div>
       
    </div>
   
    <div class="row" style="margin-top:15px;">
    	<div class="col-sm-4">
            <h4>
                Choose Target 
            </h4>
        </div>
    </div>
	 <div class="row" style="margin-top:15px;">
    <div class="col-sm-10">
        <div class="dropdown">
            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            @if ($metric->set_aacount > 0)
            {{ getSettingAccounts($metric->set_aacount) }}
            @else
               Select Account
                @endif
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu account-childs{{ $metric->account_id}}" aria-labelledby="dropdownMenu{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
           
       
                {!! getSettingA($metric->account_id) !!}
       
       
            </ul>
        </div>
        
        </div>
    </div>
    
    <div class="row" style="margin-top:15px;">
    <div class="col-sm-5 prec preloader{{ $metric->account_id}}" style=" background:#FFF; height:40px; display:none;">
        <img src="{{ url('') }}/images/aloader.gif"/> Loading Campaigns
    </div>
    	<div class="col-sm-5 ca campaignData" @if ($metric->set_aacount == 0) style="display:none;" @endif>
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownCampaign{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                 @if ($metric->set_campaign > 0)
            {{ getSettingCampaignName($metric->set_campaign) }}
            @else
                Select Camapaign
                @endif
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu campaignLiz" data-id="1" data-val="{{ $metric->account_id}}" aria-labelledby="dropdownCampaign{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                 @if ($metric->set_aacount > 0)
                	{!! getSettingCampaigns($metric->set_aacount) !!} 
                   
                  @endif
                </ul>
            </div>
            
            </div>
    </div>
     <div class="row" style="margin-top:15px;">
      <div class="col-sm-5 prea preloader{{ $metric->account_id}}" style=" background:#FFF; height:40px; display:none;">
        <img src="{{ url('') }}/images/aloader.gif"/> Loading Adgroups
    </div>
    	<div class="col-sm-5 aa adgroupData"  @if ($metric->set_campaign == 0) style="display:none;" @endif>
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownadgroup{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            @if ($metric->set_adgroup > 0)
                                            {{ getSettingAdgroupName($metric->set_adgroup) }}
                                            @else
                                                Select Adgroup
                                                @endif
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu adgroups" data-id="1" data-val="{{ $metric->account_id}}" aria-labelledby="dropdownadgroup{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                                         @if ($metric->set_campaign > 0)
                                            {!! getSettingAdgroups($metric->set_campaign) !!} 
                                           
                                          @endif
                                        </ul>
                                    </div>
                                    
                                    </div>
    </div>
     <div class="row" style="margin-top:15px;">
      <div class="col-sm-5 prek preloader{{ $metric->account_id}}" style=" background:#FFF; height:40px; display:none;">
        <img src="{{ url('') }}/images/aloader.gif"/> Loading Keywords
    </div>
    	<div class="col-sm-5 ka keyData" @if ($metric->set_adgroup == 0) style="display:none;" @endif>
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownkey{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    @if ($metric->set_keyword > 0)
                    {{ getSettingKeywordName($metric->set_keyword) }}
                    @else
                        Select Keyword
                        @endif
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu key" data-id="1" data-val="{{ $metric->account_id}}" aria-labelledby="dropdownkey{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                 @if ($metric->set_adgroup > 0)
                {!! getSettingKeywords($metric->set_adgroup) !!} 
               
              @endif
                </ul>
            </div>
            
            </div>
    </div>
    <div class="row" style="margin-top:15px;">
     <div class="col-sm-5 pred preloader{{ $metric->account_id}}" style=" background:#FFF; height:40px; display:none;">
        <img src="{{ url('') }}/images/aloader.gif"/> Loading Ads
    </div>
    	<div class="col-sm-5 da adsData" @if ($metric->set_adgroup == 0) style="display:none;" @endif>
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownads{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                 @if ($metric->set_ad > 0)
            {{ getSettingAdName($metric->set_ad) }}
            @else
                Select Ad
                @endif
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu ads" data-id="1" data-val="{{ $metric->account_id}}" aria-labelledby="dropdownads{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                 @if ($metric->set_adgroup > 0)
                {!! getSettingAds($metric->set_adgroup) !!} 
               
              @endif
                </ul>
            </div>
            
            </div>
    </div>
     <div class="row">
    	<div class="col-sm-5" style="float:right;">
        	<div class="btn-group"> 
            	<button class="btn btn-success setting-save" type="button">Save </button> 
            </div>
        	
        </div>
    </div>
    <div class="prep preloader{{ $metric->account_id}}" style=" background:#FFF; width:100%; height:50px; padding-top:75px; padding-left:26%; display:none;"> 
								<img src="{{ url('') }}/images/aloader.gif"/> Loading Metric {{ $metric->metric_id }}
      </div>
     <div class="row con cv" style="height:100%;">
    <div class="col-sm-12">
        <div class="bar1" data-met="{{ $metric->id }}" data-tar="{{ $metric->cpc_target }}"data-aid="{{ $metric->account_id}}" data-c="{{ $metric->set_aacount}}" style="text-align:center; font-size:24px;"><span class="bari"></span><span class="barp" style="margin-left:10px; font-size:16px; position:absolute; top:0px;"></span></div>
    </div>
  </div>
</div>
                           @endif
                          @endforeach
						  @endif
                          
                        </ul>
                    
                    </td>
                    <td>
                    	<ul id="sortable2" class="connectedSortable" data-position="right">
                         @if (count($metrics) > 0)
 						@foreach ($metrics as $metric)
                         @if ($metric->position == 'right')
                          <li class="ui-state-default" id="{{ $metric->id}}"  data-id="{{ $metric->id}}" data-mf="{{ $metric->metric_id }}" data-mv="{{ $metric->metric_value }}">
                          
                             
                            <div class="pre preloader{{ $metric->account_id}} ct-perfect-fourth" style=" background:#FFF; width:100%; height:200px; padding-top:50px; padding-left:39%; display:none;"> 
								<img src="{{ url('') }}/images/preloader.gif"/>
                            </div>
                            <div class="row con cant{{ $metric->account_id}}" id="first{{ $metric->id}}">
                            	<div class="col-sm-5">
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            @if ($metric->set_aacount > 0)
                                        {{ getSettingAccounts($metric->set_aacount) }}
                                        @else
                                           Select Account
                                            @endif
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu account-childs{{ $metric->account_id}}" aria-labelledby="dropdownMenu{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                                       
                                   {!! getSettingA($metric->account_id) !!}
                                            
                                   
                                   
                                        </ul>
                                    </div>
                                    
                                    </div>
                                    <div class="col-sm-5 prec preloader{{ $metric->account_id}}" style=" background:#FFF; height:40px; display:none;">
										<img src="{{ url('') }}/images/aloader.gif"/> Loading Campaigns
                            		</div>
                                    <div class="col-sm-5 ca campaignData" @if ($metric->set_aacount == 0) style="display:none;"  @endif>
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownCampaign{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                           @if ($metric->set_campaign > 0)
                                            {{ getSettingCampaignName($metric->set_campaign) }}
                                            @else
                                                Select Camapaign
                                                @endif
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu campaignLiz" data-id="0" data-val="{{ $metric->account_id}}" aria-labelledby="dropdownCampaign{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                                         @if ($metric->set_aacount > 0)
                                            {!! getSettingCampaigns($metric->set_aacount) !!} 
                                           
                                          @endif
                                        </ul>
                                    </div>
                                   
                                    </div>
                                    <div class="col-sm-2" style="float:right; text-align:center;">
                                    	<a href="#metric-setting{{ $metric->id}}" class="cs advance-options" id="{{ $metric->id}}" @if ($metric->set_aacount == 0) style="display:none;"  @endif><img src="{{ url('') }}/images/setting.png"/></a>
                              			<a class="remv" href="{{ url('metricDelete') }}/{{ $metric->id}}" data-id="{{ $metric->id}}">X</a>
                                 	</div>
                         	</div>
                           <div class="prep preloader{{ $metric->account_id}}" style=" background:#FFF; width:100%; height:200px; padding-top:75px; padding-left:26%; display:none;"> 
								<img src="{{ url('') }}/images/aloader.gif"/> Loading Metric {{ $metric->metric_id }}
                            </div>
                          <div class="row con cv" style="height:100%;">
                          	<div class="col-sm-12">
                            	<div class="bar" data-met="{{ $metric->id }}" data-tar="{{ $metric->cpc_target }}"data-aid="{{ $metric->account_id}}" data-c="{{ $metric->set_aacount}}" data-mv="{{ $metric->metric_value }}" style="text-align:center; font-size:24px;"><span class="bari"></span><span class="barp" style="margin-left:10px; font-size:16px; position:absolute; top:0px;"></span></div>
                            </div>
                          </div>
                          
                           <div class="row cv">
                          	<div class="col-sm-12">
                            	<div class="graph{{ $metric->id }} ct-perfect-fourth"></div>
                            </div>
                          </div>
                         
                          </li>
                          <div id="metric-setting{{ $metric->id}}" data-id="{{ $metric->id}}" style="width:400px;height:800px; display:none;" data-mf="{{ $metric->metric_id }}" data-mv="{{ $metric->metric_value }}">
                    <input type="hidden" class="allmetricdata" value="{{ $metric->set_aacount}}" data-c="{{ $metric->set_campaign}}" data-ag="{{ $metric->set_adgroup}}" data-k="{{ $metric->set_keyword}}" data-a="{{ $metric->set_ad}}" data-r="{{ $metric->report}}" data-d="{{ $metric->date_time}}" data-t="{{ $metric->date_type}}"/>
<div class="row" style="margin-bottom:15px; text-align:center;">
	<div class="col-sm-12">
    <h4>Setting</h4>
    </div>
</div>
<div class="row" style="margin-bottom:15px;">
	<div class="col-sm-4">
        <h4>
            Date Range
        </h4>
    </div>
</div>
<div class="row">
<div class="col-sm-6">
	<div class="daterange daterange--double one" did="setting" f="{{ conDate($metric->date_time) }}"></div>
</div>		
       
    </div>
   
     <div class="row" style="margin-top:15px;">
    	<div class="col-sm-4">
            <h4>
                Choose Target 

            </h4>
        </div>
    </div>
    <div class="row" style="margin-top:15px;">
    <div class="col-sm-10">
        <div class="dropdown">
            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
               @if ($metric->set_aacount > 0)
            {{ getSettingAccounts($metric->set_aacount) }}
            @else
               Select Account
                @endif
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu account-childs{{ $metric->account_id}}" aria-labelledby="dropdownMenu{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
            
       {!! getSettingA($metric->account_id) !!}
                
       
       
            </ul>
        </div>
       
        </div>
    </div>
     <div class="row" style="margin-top:15px;">
     <div class="col-sm-5 prec preloader{{ $metric->account_id}}" style=" background:#FFF; height:40px; display:none;">
        <img src="{{ url('') }}/images/aloader.gif"/> Loading Campaigns
    </div>
    	<div class="col-sm-5 ca campaignData"  @if ($metric->set_aacount == 0) style="display:none;" @endif>
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownCampaign{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  @if ($metric->set_campaign > 0)
            {{ getSettingCampaignName($metric->set_campaign) }}
            @else
                Select Camapaign
                @endif
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu campaignLiz" data-id="1" data-val="{{ $metric->account_id}}" aria-labelledby="dropdownCampaign{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                  @if ($metric->set_aacount > 0)
                	{{ getSettingCampaigns($metric->set_aacount) }} 
                   
                  @endif
                </ul>
            </div>
            
            </div>
    </div>
     <div class="row" style="margin-top:15px;">
     <div class="col-sm-5 prea preloader{{ $metric->account_id}}" style=" background:#FFF; height:40px; display:none;">
        <img src="{{ url('') }}/images/aloader.gif"/> Loading Adgroups
    </div>
    	<div class="col-sm-5 aa adgroupData"  @if ($metric->set_campaign == 0) style="display:none;" @endif>
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownadgroup{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                   @if ($metric->set_adgroup > 0)
            {{ getSettingAdgroupName($metric->set_adgroup) }}
            @else
                Select Adgroup
                @endif
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu adgroups" data-id="1" data-val="{{ $metric->account_id}}" aria-labelledby="dropdownadgroup{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                @if ($metric->set_campaign > 0)
                {!! getSettingAdgroups($metric->set_campaign) !!} 
               
              @endif
                </ul>
            </div>
            
            </div>
    </div>
     <div class="row" style="margin-top:15px;">
      <div class="col-sm-5 prek preloader{{ $metric->account_id}}" style=" background:#FFF; height:40px; display:none;">
        <img src="{{ url('') }}/images/aloader.gif"/> Loading Keywords
    </div>
    	<div class="col-sm-5 ka keyData" @if ($metric->set_adgroup == 0) style="display:none;" @endif>
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownkey{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                 @if ($metric->set_keyword > 0)
            {{ getSettingKeywordName($metric->set_keyword) }}
            @else
                Select Keyword
                @endif
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu key" data-id="1" data-val="{{ $metric->account_id}}" aria-labelledby="dropdownkey{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                 @if ($metric->set_adgroup > 0)
                {!! getSettingKeywords($metric->set_adgroup) !!} 
               
              @endif
                </ul>
            </div>
            
            </div>
    </div>
    <div class="row" style="margin-top:15px;">
     <div class="col-sm-5 pred preloader{{ $metric->account_id}}" style=" background:#FFF; height:40px; display:none;">
        <img src="{{ url('') }}/images/aloader.gif"/> Loading Ads
    </div>
    	<div class="col-sm-5 da adsData" @if ($metric->set_adgroup == 0) style="display:none;" @endif>
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownads{{ $metric->account_id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                   @if ($metric->set_ad > 0)
            {{ getSettingAdName($metric->set_ad) }}
            @else
                Select Ad
                @endif
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu ads" data-id="1" data-val="{{ $metric->account_id}}" aria-labelledby="dropdownads{{ $metric->account_id}}" style="height:200px; overflow:scroll;">
                 @if ($metric->set_adgroup > 0)
                {!! getSettingAds($metric->set_adgroup) !!} 
               
              @endif
                </ul>
            </div>
            
            </div>
    </div>
    <div class="row">
    	<div class="col-sm-5" style="float:right;">
        	<div class="btn-group"> 
            	<button class="btn btn-success setting-save" type="button">Save </button> 
            </div>
        	
        </div>
    </div>
     <div class="prep preloader{{ $metric->account_id}}" style=" background:#FFF; width:100%; height:50px; padding-top:75px; padding-left:26%; display:none;"> 
								<img src="{{ url('') }}/images/aloader.gif"/> Loading Metric {{ $metric->metric_id }}
      </div>
     <div class="row con cv" style="height:100%;">
    <div class="col-sm-12">
        <div class="bar1" data-met="{{ $metric->id }}" data-tar="{{ $metric->cpc_target }}"data-aid="{{ $metric->account_id}}" data-c="{{ $metric->set_aacount}}" style="text-align:center; font-size:24px;"><span class="bari"></span><span class="barp" style="margin-left:10px; font-size:16px; position:absolute; top:0px;"></span></div>
    </div>
  </div>
</div>
                           @endif
                          @endforeach
						  @endif
                        </ul>
                    </td>
                 </tr>
             </table>
        </div>
      </div>
			</div>
           
<div id="metric-step1" style="display:none; width:400px;">
<div id="step-form">
<form id="add_account_form" class="form-horizontal">
                <div class="">
					    <div class="col-sm-12">
					      	<div class="checkbox">
					        	<label>
					          		<input type="radio" name="api" class="api" value="Google Adwords"> Google Adwords
				        		</label>
					      	</div>
					    </div>
				  	</div>
                    <div class="">
					    <div class="col-sm-12">
					      	<div class="checkbox">
					        	<label>
					          		<input type="radio" name="api" class="api" value="Facebook Ads"> Facebook Ads
				        		</label>
					      	</div>
					    </div>
				  	</div>
                    <div class="">
					    <div class="col-sm-12">
					      	<div class="checkbox">
					        	<label>
					          		<input type="radio" name="api" class="api" value="Bing Ads"> Bing Ads
				        		</label>
					      	</div>
					    </div>
				  	</div>
                    <div class="">
					    <div class="col-sm-12">
					      	<div class="checkbox">
					        	<label>
					          		<input type="radio" name="api" class="api" value="Twitter Ads"> Twitter Adwords
				        		</label>
					      	</div>
					    </div>
				  	</div>
                    <div class="">
					    <div class="col-sm-12">
					      	<div class="checkbox">
					        	<label>
					          		<input type="radio" name="api" class="api" value="Google Analytics"> Google Analytics
				        		</label>
					      	</div>
					    </div>
				  	</div>
				  	
                    
				  	
				</form>
                </div>
</div>



<div id="acct" style="display:none;">
<div class="col-sm-12" style="margin-bottom:15px;">
<form id="add_account_form1" class="form-horizontal" method="post" action="{{ url('/addMetric') }}">
{!! csrf_field() !!}
<div id="f">
<input type="hidden" name="apival" id="apival" value="Google Adwords">
<input type="hidden" name="apidiv" id="apidiv">
<input type="hidden" name="apidivtype" id="apidivtype">
<input type="hidden" name="board" value="{{ $boardid }}" id="boardid">
</div>
 @if (count($accounts) > 0)
 @foreach ($accounts as $account)
	<div class="row">
    	<div class="col-sm-12">
        	<div class="checkbox">
            	<label>
                	<input type="radio" name="account" class="account" value="{{ $account->id }}"> {{ $account->name }}
                 </label>
        	</div>
       </div>
    </div>
@endforeach
@endif
</form>
</div>
</div>
@if (count($childs) > 0)
 @foreach ($childs as $k=>$v)
<input class="all-account" type="hidden" value="{{ $v }}">
 @endforeach
@endif

<form id="sortform">
{!! csrf_field() !!}
<input type="hidden" value="" id="sortfield" name="data">
</form>
<form id="metricform">
{!! csrf_field() !!}
<input type="hidden" value="0" id="metric" name="metric">
<input type="hidden" value="0" id="setaccount" name="account">
<input type="hidden" value="0" id="setcampaign" name="campaign">
<input type="hidden" value="0" id="setadgroups" name="adgroup">
<input type="hidden" value="0" id="setkeywords" name="keywords">
<input type="hidden" value="0" id="setads" name="ads">
<input type="hidden" value="" id="report" name="report">
<input type="hidden" value="" id="datetime" name="date">
<input type="hidden" value="" id="datetype" name="type">
</form>
@endsection


 
