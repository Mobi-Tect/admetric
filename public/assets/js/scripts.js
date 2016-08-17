var cpc_url = cpc_url;
var account_url = account_url;
var campaign_url = campaign_url;
var sort_url = sort_url;
var group_url = group_url;
var ad_url = ad_url;
var key_url = key_url;
var setting_url = setting_url;
var get_setting_url = get_setting_url;
var savea_url = savea_url;
var progress_url = progress_url;
var per_url = per_url;
var token = token;
var acnform = acnform;
var base_url = base_url;
var xhr = [];
var ulId = 1;
var bar = [];
  $(function() {
	  "use strict";
    $( "#sortable1, #sortable2" ).sortable({
      connectWith: ".connectedSortable",
	  placeholder: "ui-state-highlight",
	  tolerance: "pointer",
	  cursor: "move",
	  receive: function( event, ui ) {
			
			var dataValues = '';
			var sortId = 1;
			$.each( $('#sortable1').find('.ui-state-default'), function( key, value ) {
				  dataValues += $(this).attr('id')+'-'+sortId+'-left,';
				  sortId++;
			});
			$.each( $('#sortable2').find('.ui-state-default'), function( key, value ) {
				  dataValues += $(this).attr('id')+'-'+sortId+'-right,';
				  sortId++;
			});
			dataValues = dataValues.substring(0,dataValues.lastIndexOf(","));
			$('#sortfield').val(dataValues);
			$.ajax({
				type : 'POST',
				url: sort_url,
				data:$('#sortform').serialize(),
				success: function(data){
				}
			});
			
			
		},
		update: function( event, ui ) {
			var dataValues = '';
			var sortId = 1;
			$.each( $('#sortable1').find('.ui-state-default'), function( key, value ) {
				  dataValues += $(this).attr('id')+'-'+sortId+'-left,';
				  sortId++;
			});
			$.each( $('#sortable2').find('.ui-state-default'), function( key, value ) {
				  dataValues += $(this).attr('id')+'-'+sortId+'-right,';
				  sortId++;
			});
			dataValues = dataValues.substring(0,dataValues.lastIndexOf(","));
			$('#sortfield').val(dataValues);
			$.ajax({
				type : 'POST',
				url: sort_url,
				data:$('#sortform').serialize(),
				success: function(data){
				}
			});
			
			
		}
    }).disableSelection();
	/*$(document).on('click','#add',function(){
		var div = $('.ui-state-default');
		div = parseInt(parseInt(div.length)+1);
		$('#sortable'+ulId).append('<li id="li-'+div+'" class="ui-state-default">Item '+div+' <a class="remv" href="javascript:void(0);" data-id="'+div+'">X</a></li>');
		if(ulId == 1){
			ulId = 2;
		}else{
			ulId = 1;
		}
	});*/
	$("#add").fancybox({
		minWidth : 300,
		width : 400,
		minHeigh : 300,
		beforeLoad : function(){
			$('#step-form').html('');
			//var html = $('#stepApi1').html();
			//var html = '<form id="add_account_form" class="form-horizontal"><div class=""><div class="col-sm-12"><div class="checkbox"><label><input type="radio" name="api" class="api" value="Google Adwords"> Google Adwords</label></div></div></div><div class=""><div class="col-sm-12"><div class="checkbox"><label><input type="radio" name="api" class="api" value="Facebook Ads"> Facebook Ads</label></div></div></div><div class=""><div class="col-sm-12"><div class="checkbox"><label><input type="radio" name="api" class="api" value="Bing Ads"> Bing Ads</label></div></div></div><div class=""><div class="col-sm-12"><div class="checkbox"><label><input type="radio" name="api" class="api" value="Twitter Ads"> Twitter Adwords</label></div></div></div><div class=""><div class="col-sm-12"><div class="checkbox"><label><input type="radio" name="api" class="api" value="Google Analytics"> Google Analytics</label></div></div></div></form>';
			var html = '<div class="col-sm-12 col-md-12" style="margin-bottom:10px;"><label class="col-sm-3 col-md-3 control-label">CPC Metric</label><div class="col-sm-9 col-md-9"><div style="border:#000000 1px solid; width:100%; height:100px;"  class="divapi" id="AverageCpc">&nbsp;</div></div></div><div class="col-sm-12 col-md-12" style="margin-bottom:10px;"><label class="col-sm-3 col-md-3 control-label">Cpa Metric</label><div class="col-sm-9 col-md-9"><div style="border:#000000 1px solid; width:100%; height:100px;" class="divapi" id="Cpa">&nbsp;</div></div></div><div class="col-sm-12 col-md-12" style="margin-bottom:10px;"><label class="col-sm-3 col-md-3 control-label">CTR Metric</label><div class="col-sm-9 col-md-9"><div style="border:#000000 1px solid; width:100%; height:100px;"  class="divapi" id="Ctr">&nbsp;</div></div></div><div class="col-sm-12 col-md-12" style="margin-bottom:10px;"><label class="col-sm-3 col-md-3 control-label">Conversion Rate Metric</label><div class="col-sm-9 col-md-9"><div style="border:#000000 1px solid; width:100%; height:100px;" class="divapi" id="ConversionRate">&nbsp;</div></div></div><div class="col-sm-12 col-md-12" style="margin-bottom:10px;"><label class="col-sm-3 col-md-3 control-label">Conversions Metric</label><div class="col-sm-9 col-md-9"><div style="border:#000000 1px solid; width:100%; height:100px;" class="divapi" id="Conversions">&nbsp;</div></div></div>';
			$('#step-form').append(html);
		}
	});
	$(document).on('click','.api',function(){
		$('#apival').val($(this).val());
		$('#step-form').html('');
		//var html = $('#cmetric').html();
		var html = '<div class="col-sm-12 col-md-12" style="margin-bottom:10px;"><label class="col-sm-3 col-md-3 control-label">CPC Metric</label><div class="col-sm-9 col-md-9"><div style="border:#000000 1px solid; width:100%; height:100px;"  class="divapi" id="AverageCpc">&nbsp;</div></div></div><div class="col-sm-12 col-md-12" style="margin-bottom:10px;"><label class="col-sm-3 col-md-3 control-label">Cpa Metric</label><div class="col-sm-9 col-md-9"><div style="border:#000000 1px solid; width:100%; height:100px;" class="divapi" id="Cpa">&nbsp;</div></div></div><div class="col-sm-12 col-md-12" style="margin-bottom:10px;"><label class="col-sm-3 col-md-3 control-label">CTR Metric</label><div class="col-sm-9 col-md-9"><div style="border:#000000 1px solid; width:100%; height:100px;"  class="divapi" id="Ctr">&nbsp;</div></div></div><div class="col-sm-12 col-md-12" style="margin-bottom:10px;"><label class="col-sm-3 col-md-3 control-label">Conversion Rate Metric</label><div class="col-sm-9 col-md-9"><div style="border:#000000 1px solid; width:100%; height:100px;" class="divapi" id="ConversionRate">&nbsp;</div></div></div><div class="col-sm-12 col-md-12" style="margin-bottom:10px;"><label class="col-sm-3 col-md-3 control-label">Conversions Metric</label><div class="col-sm-9 col-md-9"><div style="border:#000000 1px solid; width:100%; height:100px;" class="divapi" id="Conversions">&nbsp;</div></div></div>';
		$('#step-form').html(html);
	});
	$(document).on('click','.divapi',function(){
		$('#apidiv').val($(this).attr('id'));
		//$('#apidivtype').val($(this).val());
		$('#apidivtype').val(1);
		$('#step-form').html('');
		var html1 = $('#acct').html();
		$('#step-form').append(html1);
		//var html = $('#stepApi2').html();
		var html = '<div class="col-sm-12"><div class="col-sm-12"><button type="button" id="contect">Connect a new account</button></div></div>';
		$('#step-form').append(html);
	});
	$(document).on('click','#contect',function(){
		//$('#apidiv').val($(this).attr('id'));
		$('#step-form').html('');
		var html = '<div style="display:none;"><form id="acn" action="'+acnform+'" method="post" class="form-horizontal"><input type="hidden" name="_token" value="'+token+'"><div class="form-group" id="err1"><label class="col-md-4 control-label">Account Name</label><div class="col-md-6" id="errtext1"><input type="text" class="form-control" name="acnam" id="na" value="test"></div></div><div class="form-group"><div class="col-md-6 col-md-offset-4"><button type="button" id="adC" class="btn btn-primary"><i class="fa fa-btn fa-sign-in"></i>Add</button></div></div></form></div>';
		$('#step-form').html(html);
		$('#acn').append($('#f').html());
		$.fancybox.close();
		$('#acn').submit();
	});
	/*$(document).on('click','.remv',function(){
		var id = $(this).attr('data-id');
		var $this = this;
		$.ajax({
			type : "POST",
			url  : delete_url,
			data : {id:id},
			success: function(data){
				
			}	
		});
	});*/
	$(document).on('click','#adC',function(){
			var val = $('#na').val();
			if(val.length>0){
				$('#acn').submit();
			}else{
				$('#err1').addClass('has-error');
				$('#errtext1').append('<span id="remerr" class="help-block"><strong>This field is required.</strong></span>');
			}
	});
	$(document).on('keyup','#na',function(){
		var val = $('#na').val();
		if(val.length>0){
			$('#err1').removeClass('has-error');
			$('#remerr').remove();
		}
	});
	$(document).on('click','.account',function(){
		$(this).parent().parent().parent().parent().parent().submit();
	});
	$(document).on('click','.childs',function(){
		var id = $(this).attr('data-id');
		var account = $(this).attr('data-val');
		
		if(account>0){
			$('#report').val('ACCOUNT_PERFORMANCE_REPORT');
		}
		$('#setaccount').val(account);
		$('#setcampaign').val(0);
		$('#setads').val(0);
		$('#setkeywords').val(0);
		$('#setadgroups').val(0);
		var aText = $(this).text();
		var parentLi = $(this).parent().parent().parent().parent().parent().parent();
		var mv = parentLi.attr('data-mv');
		var mf = parentLi.attr('data-mf');
		$('#metric').val(parentLi.attr('data-id'));
		var mId = parentLi.attr('data-id');
		var metricDiv = $('#metric-setting'+mId);
		
		//parentLi.find('.pre').show();
		//parentLi.find('.con').hide();
		parentLi.find('.prec').show();
		parentLi.find('.ca').hide();
		parentLi.find('.cs').hide();
		//console.log(parentLi);
		aText += '&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		var bText = 'Select Campaign&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		var cText = 'Select Adgroup&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		var dText = 'Select Keyword&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		var eText = 'Select Ad&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		$(this).parent().parent().parent().find('button').html(aText);
		if(parentLi.find('button').length == 2){
			parentLi.find('button:eq(1)').html(bText);
			metricDiv.find('button:eq(0)').html('ALL TIME');
			metricDiv.find('button:eq(1)').html(aText);
			metricDiv.find('button:eq(2)').html(bText);
			metricDiv.find('.adgroupData').hide();
			metricDiv.find('.keyData').hide();
			metricDiv.find('.adsData').hide();
			metricDiv.find('.dt').val('');
			metricDiv.find('.days').val('');
			metricDiv.find('.allmetricdata').val(account);
			metricDiv.find('.allmetricdata').attr('data-c',0);
			metricDiv.find('.allmetricdata').attr('data-ag',0);
			metricDiv.find('.allmetricdata').attr('data-k',0);
			metricDiv.find('.allmetricdata').attr('data-a',0);
			metricDiv.find('.allmetricdata').attr('data-r','ACCOUNT_PERFORMANCE_REPORT');
			metricDiv.find('.allmetricdata').attr('data-d','7');
			metricDiv.find('.allmetricdata').attr('data-t','lower');
			$.ajax({
				type : 'GET',
				url: savea_url+"/"+account+"/"+mId,
				success: function(data){
						//data = $.parseJSON(data);
						
				}	
			});	
		}else if(parentLi.find('button').length == 7){
			//console.log(parentLi.prev('li'));
			parentLi.find('button:eq(1)').html(aText);
			parentLi.find('button:eq(2)').html(bText);
			$('#first'+mId).find('button:eq(0)').html(aText);
			$('#first'+mId).find('button:eq(1)').html(bText);
			$.ajax({
				type : 'POST',
				data : $('#metricform').serialize(),
				url : get_setting_url,
				 beforeSend: function( xhr ) {
					parentLi.find('.prep').show();
					parentLi.find('.cv').hide();
					
				  },
				success: function(data){
					data = $.parseJSON(data);
					parentLi.find('.metcpc').html('')
					parentLi.find('.metcpc').html(data.cpc);
					parentLi.find('.bari').html('')
					parentLi.find('.bari').html(data.cpc);
					//console.log(parentLi.parent());
					//parentLi.prev().find('.bari').html('');
					//parentLi.prev().find('.bari').html(data.cpc);
					parentLi.find('.prep').hide();
					parentLi.find('.cv').show();
					$.ajax({
						type : 'POST',
						data : {id:mId,_token:token},
						url: per_url,
						beforeSend: function( xhr ) {
							parentLi.find('.barp').html('');
						  },
						success: function(data){
							data = $.parseJSON(data);
							parentLi.find('.barp').html('');
							if(data.p != 0){
								if(data.p > 0){
									parentLi.find('.barp').html(data.p+'% <img src="'+base_url+'/images/up.png"/>');
								}else{
									parentLi.find('.barp').html(data.p+'% <img src="'+base_url+'/images/down.png"/>');
								}
							}
						}
					}); 
					//$.fancybox.close();
				}
			});
		}
		if(id>0){
			$.ajax({
				type : 'GET',
				url: campaign_url+"/"+id+"/"+account+"/"+mf,
				success: function(data){
						data = $.parseJSON(data);
						parentLi.find('.campaignLiz').html('');
						metricDiv.find('.campaignLiz').html('');
						parentLi.find('.campaignLiz').parent().find('button').html(bText);
						parentLi.find('.adgroups').parent().find('button').html(cText);
						parentLi.find('.key').parent().find('button').html(dText);
						parentLi.find('.ads').parent().find('button').html(eText);
						var html = '<li style="height:25px !important; min-height:25px;" ><a class="childsCampaigns" href="javascript:void(0);" data-id="0" data-val="0">Select Campaign</a></li>';
						$.each(data.camaigns, function( index, value ) {
						  html += '<li style="height:25px !important; min-height:25px;" ><a href="javascript:void(0);" data-id="'+value.id+'"  data-val="'+value.cpc+'"  data-account="'+value.a+'" class="childsCampaigns">'+value.name+'</a></li>';
						});
						//console.log(html);
						
						parentLi.find('.campaignLiz').append(html);
						metricDiv.find('.campaignLiz').append(html);
						parentLi.find('.campaignData').show();
						parentLi.find('.prec').hide();
						parentLi.find('.ca').show();
						if(parentLi.find('button').length == 2){
							setTimeout(function(){
								xhr[mId] = $.ajax({
									type : 'POST',
									data : {id:mId,d:30,_token:token},
									url: progress_url,
									  beforeSend: function( xhr ) {
										parentLi.find('.prep').show();
										parentLi.find('.cv').hide();
									  },
									success: function(data){
										data = $.parseJSON(data);
										//parentLi.find('.bar').html('');
										//parentLi.find('.bari').text(data.cpc);
										//parentLi.next().find('.bari').text(data.cpc);
										//p.find('.cpcVal').html('');
										//p.find('.cpcVal').html(data.acpc);
										//if(mv == 1){
											parentLi.find('.bari').text(data.cpc);
											if(data.p != 0){
												if(data.p > 0){
													parentLi.find('.barp').html(data.p+'% <img src="'+base_url+'/images/up.png"/>');
												}else{
													parentLi.find('.barp').html(data.p+'% <img src="'+base_url+'/images/down.png"/>');
												}
											}
										//}else{
											var lab = [];
											var da = [];
											$.each(data.cpc1,function(i,v){
												da.push(parseFloat(data.cpc1[i].v));
												lab.push(data.cpc1[i].d);
											});
											
											//console.log(da);
											//console.log(lab);
											var d = {labels:lab, series:[da]};
											console.log(d);
											//p.find('.cpcVal').html('');
											//p.find('.cpcVal').html(data.acpc);
											//var g = p.find('.graph');
											new Chartist.Line('.graph'+mId, d);
										//}
										parentLi.find('.prep').hide();
										parentLi.find('.cv').show();
										parentLi.find('.cs').show();
										xhr.splice(mId,1);
										/*$.ajax({
											type : 'POST',
											data : {id:mId,d:30,_token:token},
											url: per_url,
											beforeSend: function( xhr ) {
												parentLi.find('.barp').html('');
											  },
											success: function(data){
												data = $.parseJSON(data);
												parentLi.find('.barp').html('');
												if(data.p != 0){
													if(data.p > 0){
														parentLi.find('.barp').html(data.p+'% <img src="'+base_url+'/images/up.png"/>');
													}else{
														parentLi.find('.barp').html(data.p+'% <img src="'+base_url+'/images/down.png"/>');
													}
												}
											}
										}); */
									}
								});
							},10000);
						}
						/*if(parentLi.find('button').length == 7){
							$('#first'+mId).find('.campaignLiz').html('');
							$('#first'+mId).find('.campaignLiz').append(html);
							$('#first'+mId).find('.campaignData').show();
							
						}*/
				}	
			});	
			/*$.ajax({
				type : 'GET',
				url: cpc_url+"/"+id+"/"+account+"/"+mf,
				success: function(data){
						data = $.parseJSON(data);
						parentLi.find('.acountCPC').show();
						parentLi.find('.cpcVal').html('');
						parentLi.find('.cpcVal').html(data.aCPC);
						$('#first'+mId).find('.acountCPC').show();
						$('#first'+mId).find('.cpcVal').html('');
						$('#first'+mId).find('.cpcVal').html(data.aCPC);
						//console.log(html);
						parentLi.find('.pre').hide();
						parentLi.find('.con').show();
				}	
			});	*/
		}else{
			parentLi.find('.acountCPC').hide();
			parentLi.find('.campaignData').hide();
		}
	});
	$(document).on('click','.childsCampaigns',function(){
		var id = $(this).attr('data-id');
		var account = $(this).attr('data-account');
		if(id>0){
			$('#report').val('CAMPAIGN_PERFORMANCE_REPORT');
		}
		var cpc = $(this).attr('data-val');
		$('#setcampaign').val(id);
		$('#setads').val(0);
		$('#setkeywords').val(0);
		$('#setadgroups').val(0);
		var aText = $(this).text();
		var div = $(this).parent().parent();
		var aid = div.attr('data-val');
		var parentLi = $(this).parent().parent().parent().parent().parent().parent();
		var mv = parentLi.attr('data-mv');
		var mf = parentLi.attr('data-mf');
		var cText = 'Select Adgroup&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		var dText = 'Select Keyword&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		var eText = 'Select Ad&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		var mId = parentLi.attr('data-id');
		if(div.attr('data-id') == 0){
			if(id>0){
				if(typeof xhr[mId] === 'undefined') {
					// does not exist
				}
				else {
					xhr[mId].abort();
				}
				parentLi.find('.prep').show();
				parentLi.find('.cv').hide(); 
				parentLi.find('.cs').hide();
				$('#metric').val(parentLi.attr('data-id'));
				var metricDiv = $('#metric-setting'+mId);
				parentLi.find('.campaignCPC').show();
				parentLi.find('.campaignVal').html('');
				parentLi.find('.campaignVal').html(cpc);
				aText += '&nbsp;&nbsp;&nbsp;<span class="caret"></span>'
				$(this).parent().parent().parent().find('button').html(aText);
				metricDiv.find('button:eq(0)').html('ALL TIME');
				metricDiv.find('button:eq(2)').html(aText);
				metricDiv.find('.adgroupData').show();
				metricDiv.find('.keyData').hide();
				metricDiv.find('.adsData').hide();
				metricDiv.find('.dt').val('');
				metricDiv.find('.days').val('');
				//metricDiv.find('.allmetricdata').val(account);
				metricDiv.find('.allmetricdata').attr('data-c',id);
				metricDiv.find('.allmetricdata').attr('data-ag',0);
				metricDiv.find('.allmetricdata').attr('data-k',0);
				metricDiv.find('.allmetricdata').attr('data-a',0);
				metricDiv.find('.allmetricdata').attr('data-r','CAMPAIGN_PERFORMANCE_REPORT');
				metricDiv.find('.allmetricdata').attr('data-d',get30Days);
				metricDiv.find('.allmetricdata').attr('data-t','lower');
				$('#datetime').val(get30Days);
				$('#datetype').val('lower');
				$.ajax({
					type : 'GET',
					url: group_url+"/"+aid+"/"+account+"/"+id,
					 beforeSend: function( xhr ) {
						//parentLi.find('.prea').show();
						//parentLi.find('.aa').hide();
						
					  },
					success: function(data){
							data = $.parseJSON(data);
							metricDiv.find('.adgroups').html('');
							metricDiv.find('.adgroups').parent().find('button').html(cText);
							metricDiv.find('.key').parent().find('button').html(dText);
							metricDiv.find('.ads').parent().find('button').html(eText);
							var html = '<li style="height:25px !important; min-height:25px;" ><a class="childsAdgroups" href="javascript:void(0);" data-id="0" data-val="0">Select Adgroup</a></li>';
							$.each(data.adgroups, function( index, value ) {
							  html += '<li style="height:25px !important; min-height:25px;" ><a href="javascript:void(0);" data-id="'+value.id+'"  data-aid="'+data.id+'"  data-account="'+value.a+'" data-c="'+value.c+'" class="childsAdgroups">'+value.name+'</a></li>';
							});
							metricDiv.find('.adgroups').append(html);
							metricDiv.find('.adgroupData').show();
							//parentLi.find('.prea').hide();
							//parentLi.find('.aa').show();
							
					}
				});
				if(parentLi.find('button').length == 2){
					$.ajax({
						type : 'POST',
						data : $('#metricform').serialize(),
						url : setting_url,
						success: function(data){
							data = $.parseJSON(data);
							$.ajax({
								type : 'POST',
								data : {id:mId,d:30,_token:token},
								url: progress_url,
								  beforeSend: function( xhr ) {
									parentLi.find('.prep').show();
									parentLi.find('.cv').hide();
									parentLi.find('.cs').hide();
								  },
								success: function(data){
									data = $.parseJSON(data);
									//if(mv == 1){
										parentLi.find('.bari').text(data.cpc);
										if(data.p != 0){
											if(data.p > 0){
												parentLi.find('.barp').html(data.p+'% <img src="'+base_url+'/images/up.png"/>');
											}else{
												parentLi.find('.barp').html(data.p+'% <img src="'+base_url+'/images/down.png"/>');
											}
										}
									//}else{
										var lab = [];
										var da = [];
										$.each(data.cpc1,function(i,v){
											da.push(parseFloat(data.cpc1[i].v));
											lab.push(data.cpc1[i].d);
										});
										
										//console.log(da);
										//console.log(lab);
										var d = {labels:lab, series:[da]};
										//p.find('.cpcVal').html('');
										//p.find('.cpcVal').html(data.acpc);
										//var g = p.find('.graph');
										new Chartist.Line('.graph'+mId, d);
									//}
									//parentLi.find('.bar').html('');
									//parentLi.find('.bari').text(data.cpc);
									//p.find('.cpcVal').html('');
									//p.find('.cpcVal').html(data.acpc);
									parentLi.find('.prep').hide();
									parentLi.find('.cv').show();
									parentLi.find('.cs').show();
									xhr.splice(mId,1);
									
								}
							});
							
						}
					});
				}
				
			}else{
				parentLi.find('.campaignCPC').hide();
			}
		}else{
			
			parentLi.find('.campaignVal').html('');
			parentLi.find('.campaignVal').html(cpc);
			aText += '&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
			$(this).parent().parent().parent().find('button').html(aText);
			$('#first'+mId).find('button:eq(1)').html(aText);
			$('#first'+mId).find('.campaignCPC').show();
			$('#first'+mId).find('.campaignVal').html('');
			$('#first'+mId).find('.campaignVal').html(cpc);
			$.ajax({
				type : 'GET',
				url: group_url+"/"+aid+"/"+account+"/"+id,
				 beforeSend: function( xhr ) {
						parentLi.find('.prea').show();
						parentLi.find('.aa').hide();
				  },
				success: function(data){
						data = $.parseJSON(data);
						parentLi.find('.adgroups').html('');
						parentLi.find('.adgroups').parent().find('button').html(cText);
						parentLi.find('.key').parent().find('button').html(dText);
						parentLi.find('.ads').parent().find('button').html(eText);
						var html = '<li style="height:25px !important; min-height:25px;" ><a class="childsAdgroups" href="javascript:void(0);" data-id="0" data-val="0">Select Adgroup</a></li>';
						$.each(data.adgroups, function( index, value ) {
						  html += '<li style="height:25px !important; min-height:25px;" ><a href="javascript:void(0);" data-id="'+value.id+'"  data-aid="'+data.id+'"  data-account="'+value.a+'" data-c="'+value.c+'" class="childsAdgroups">'+value.name+'</a></li>';
						});
						parentLi.find('.adgroups').append(html);
						parentLi.find('.adgroupData').show();
						parentLi.find('.prea').hide();
						parentLi.find('.aa').show();
						$.ajax({
							type : 'POST',
							data : {id:mId,_token:token},
							url: per_url,
							 beforeSend: function( xhr ) {
								parentLi.find('.barp').html('');
							  },
							success: function(data){
								data = $.parseJSON(data);
								parentLi.find('.barp').html('');
								if(data.p != 0){
									if(data.p > 0){
										parentLi.find('.barp').html(data.p+'% <img src="'+base_url+'/images/up.png"/>');
									}else{
										parentLi.find('.barp').html(data.p+'% <img src="'+base_url+'/images/down.png"/>');
									}
								}
							}
						}); 
				}
			});
			$.ajax({
					type : 'POST',
					data : $('#metricform').serialize(),
					url : get_setting_url,
					 beforeSend: function( xhr ) {
						parentLi.find('.prep').show();
						parentLi.find('.cv').hide();
						
					  },
					success: function(data){
						data = $.parseJSON(data);
						parentLi.find('.metcpc').html('')
						parentLi.find('.metcpc').html(data.cpc);
						parentLi.find('.bari').html('')
						parentLi.find('.bari').html(data.cpc);
						parentLi.find('.prep').hide();
						parentLi.find('.cv').show();
					}
				});
		}
	});
	
	$(document).on('click','.childsAdgroups',function(){
		var id = $(this).attr('data-id');
		var account = $(this).attr('data-account');
		if(id>0){
			$('#report').val('ADGROUP_PERFORMANCE_REPORT');
		}
		$('#setadgroups').val(id);
		$('#setads').val(0);
		$('#setkeywords').val(0);
		var aid = $(this).attr('data-aid');
		var c = $(this).attr('data-c');
		var aText = $(this).text();
		//var div = $(this).parent().parent();
		var parentLi = $(this).parent().parent().parent().parent().parent().parent();
		var dText = 'Select Keyword&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		var eText = 'Select Ad&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		//parentLi.find('.campaignVal').html('');
		//parentLi.find('.campaignVal').html(account);
		aText += '&nbsp;&nbsp;&nbsp;<span class="caret"></span>'
		$(this).parent().parent().parent().find('button').html(aText);
		$.ajax({
				type : 'POST',
				data : $('#metricform').serialize(),
				url : get_setting_url,
				beforeSend: function( xhr ) {
					parentLi.find('.prep').show();
					parentLi.find('.cv').hide();
					
				  },
				success: function(data){
					data = $.parseJSON(data);
					parentLi.find('.metcpc').html('')
					parentLi.find('.metcpc').html(data.cpc);
					parentLi.find('.bari').html('')
					parentLi.find('.bari').html(data.cpc);
					parentLi.find('.prep').hide();
					parentLi.find('.cv').show();
				}
			});
		$.ajax({
			type : 'GET',
			url: key_url+"/"+aid+"/"+account+"/"+c+"/"+id,
			beforeSend: function( xhr ) {
				parentLi.find('.prek').show();
				parentLi.find('.ka').hide();
				
			  },
			success: function(data){
					data = $.parseJSON(data);
					parentLi.find('.key').html('');
					parentLi.find('.ads').parent().find('button').html(eText);
					var html = '<li style="height:25px !important; min-height:25px;" ><a class="childswords" href="javascript:void(0);" data-id="0" data-val="0">Select Keyword</a></li>';
					$.each(data.keywords, function( index, value ) {
					  html += '<li style="height:25px !important; min-height:25px;" ><a href="javascript:void(0);" data-id="'+value.id+'"  data-aid="'+data.id+'"  data-account="'+value.a+'" data-c="'+value.ai+'" class="childswords">'+value.name+'</a></li>';
					});
					parentLi.find('.key').append(html);
					parentLi.find('.keyData').show();
					parentLi.find('.prek').hide();
					parentLi.find('.ka').show();
					$.ajax({
					type : 'GET',
					url: ad_url+"/"+aid+"/"+account+"/"+c+"/"+id,
					beforeSend: function( xhr ) {
						parentLi.find('.pred').show();
						parentLi.find('.da').hide();
						
					  },
					success: function(data){
							data = $.parseJSON(data);
							parentLi.find('.ads').html('');
							parentLi.find('.key').parent().find('button').html(dText);
							var html = '<li style="height:25px !important; min-height:25px;" ><a class="childsAds" href="javascript:void(0);" data-id="0" data-val="0">Select Ad</a></li>';
							$.each(data.ads, function( index, value ) {
							  html += '<li style="height:25px !important; min-height:25px;" ><a href="javascript:void(0);" data-id="'+value.id+'"  data-aid="'+data.id+'"  data-account="'+value.a+'" data-c="'+value.ai+'" class="childsAds">'+value.name+'</a></li>';
							});
							parentLi.find('.ads').append(html);
							parentLi.find('.adsData').show();
							parentLi.find('.pred').hide();
							parentLi.find('.da').show();
					}
				});
			}
		});
		
	});
	$(document).on('click','.childswords',function(){
		var id = $(this).attr('data-id');
		var eText = 'Select Ad&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		if(id>0){
			$('#report').val('KEYWORDS_PERFORMANCE_REPORT');
		}
		$('#setkeywords').val(id);
		var parentLi = $(this).parent().parent().parent().parent().parent().parent();
		var aText = $(this).text();
		aText += '&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		$(this).parent().parent().parent().find('button').html(aText);
		$('#setads').val(0);
		$.ajax({
				type : 'POST',
				data : $('#metricform').serialize(),
				url : get_setting_url,
				beforeSend: function( xhr ) {
					parentLi.find('.prep').show();
					parentLi.find('.cv').hide();
					
				  },
				success: function(data){
					data = $.parseJSON(data);
					parentLi.find('.metcpc').html('')
					parentLi.find('.metcpc').html(data.cpc);
					parentLi.find('.bari').html('')
					parentLi.find('.bari').html(data.cpc);
					parentLi.find('.prep').hide();
					parentLi.find('.cv').show();
				}
			});
	});
	$(document).on('click','.childsAds',function(){
		var dText = 'Select Keyword&nbsp;&nbsp;&nbsp;<span class="caret"></span>';
		var id = $(this).attr('data-id');
		if(id>0){
			$('#report').val('AD_PERFORMANCE_REPORT');
		}
		$('#setads').val(id);
		var aText = $(this).text();
		var parentLi = $(this).parent().parent().parent().parent().parent().parent();
		aText += '&nbsp;&nbsp;&nbsp;<span class="caret"></span>'
		$(this).parent().parent().parent().find('button').html(aText);
		$('#setkeywords').val(0);
		$.ajax({
				type : 'POST',
				data : $('#metricform').serialize(),
				url : get_setting_url,
				beforeSend: function( xhr ) {
					parentLi.find('.prep').show();
					parentLi.find('.cv').hide();
					
				  },
				success: function(data){
					data = $.parseJSON(data);
					parentLi.find('.metcpc').html('')
					parentLi.find('.metcpc').html(data.cpc);
					parentLi.find('.bari').html('')
					parentLi.find('.bari').html(data.cpc);
					parentLi.find('.prep').hide();
					parentLi.find('.cv').show();
				}
			});
	});
	$(document).on('click','.dateone',function(){
		var id = $(this).attr('data-val');
		$('#datetype').val('upper');
		$('#datetime').val(id);
		var aText = $(this).text();
		aText += '&nbsp;&nbsp;&nbsp;<span class="caret"></span>'
		$(this).parent().parent().parent().find('button').html(aText);
		 var p = $(this).parent().parent().parent().parent().parent().parent();
		 p.find('.dt').val('');
		 p.find('.days').val('');
		 $.ajax({
				type : 'POST',
				data : $('#metricform').serialize(),
				url : get_setting_url,
				beforeSend: function( xhr ) {
					p.find('.prep').show();
					p.find('.cv').hide();
					
				  },
				success: function(data){
					data = $.parseJSON(data);
					p.find('.metcpc').html('')
					p.find('.metcpc').html(data.cpc);
					p.find('.bari').html('')
					p.find('.bari').html(data.cpc);
					p.find('.prep').hide();
					p.find('.cv').show();
					//$.fancybox.close();
				}
			});
	});
	$(document).on('keyup','.days',function(){
		var id = $(this).val();
		$('#datetype').val('lower');
		if(id.length>0 && id.length<4){
			$('#datetime').val(id);
		}else{
			$('#datetime').val(1);
			$(this).val(1);
		}
		 var p = $(this).parent().parent().parent();
		  p.find('.dt').val('');
		  p.find('button:eq(0)').text('Select Date Range');
		  $.ajax({
				type : 'POST',
				data : $('#metricform').serialize(),
				url : get_setting_url,
				beforeSend: function( xhr ) {
					p.find('.prep').show();
					p.find('.cv').hide();
					
				  },
				success: function(data){
					data = $.parseJSON(data);
					p.find('.metcpc').html('')
					p.find('.metcpc').html(data.cpc);
					p.find('.bari').html('')
					p.find('.bari').html(data.cpc);
					p.find('.prep').hide();
					p.find('.cv').show();
					//$.fancybox.close();
				}
			});
		
	});
	
	/*var requests = [];
		$.each( accounts, function( key, value ) {
			var id = $(this).val();
			$('.preloader'+id).show();
			$('.cant'+id).hide();
			$.ajax({
				type : 'GET',
				url: account_url+"/"+id,
				success: function(data){
					data = $.parseJSON(data);
					//console.log(data);
					var html = '<li style="height:25px !important; min-height:25px;" ><a class="childs" href="javascript:void(0);" data-id="0" data-val="0">Select Account</a></li>';
					$.each(data.accounts, function( index, value ) {
						  html += '<li style="height:25px !important; min-height:25px;" ><a href="javascript:void(0);" data-id="'+value.client_id+'"   data-val="'+value.account_id+'" class="childs">'+value.name+'</a></li>';
						  $('.account-childs'+id).html('');
						  $('.account-childs'+id).append(html);
						  $('.preloader'+id).hide();
						  $('.cant'+id).show();
						});
				}
			});
		});*/
	var accounts = $('.bar');
	var requests = [];
		
		$.each( accounts, function( key, value ) {
			var mid = $(this).attr('data-met');
			var mv = $(this).attr('data-mv');
			var id = $(this).attr('data-aid');
			var c = $(this).attr('data-c');
			var p = $(this).parent().parent().parent();
			var $this = $(this);
			if(c > 0){
				var req ={
					type : 'POST',
					data : {id:mid,_token:token},
					url: progress_url,
					  beforeSend: function( xhr ) {
						p.find('.pre').show();
						p.find('.con').hide();
					  },
					success: function(data){
						data = $.parseJSON(data);
						//$this.html('');
						//if(mv == 1){
							$this.find('.bari').text(data.cpc);
						//}else{
							var lab = [];
							var da = [];
							$.each(data.cpc1,function(i,v){
								da.push(parseFloat(data.cpc1[i].v));
								lab.push(data.cpc1[i].d);
							});
							
							console.log(d);
							//console.log(lab);
							var d = {labels:lab, series:[da]};
							//p.find('.cpcVal').html('');
							//p.find('.cpcVal').html(data.acpc);
							//var g = p.find('.graph');
							new Chartist.Line('.graph'+mid, d);
						//}
						$this.find('.barp').html('');
						if(data.p != 0){
							if(data.p > 0){
								$this.find('.barp').html(data.p+'% <img src="'+base_url+'/images/up.png"/>');
							}else{
								$this.find('.barp').html(data.p+'% <img src="'+base_url+'/images/down.png"/>');
							}
						}
						p.find('.pre').hide();
						p.find('.con').show();
						
					}
				};
				requests.push(req);
			}else{
				p.find('.pre').hide();
				p.find('.con').show();
			}
		});
		/*$.each( accounts, function( key, value ) {
			var mid = $(this).attr('data-met');
			var c = $(this).attr('data-c');
			var mv = $(this).attr('data-mv');
			var id = $(this).attr('data-aid');
			var p = $(this).parent().parent().parent();
			var $this = $(this);
			//if(c > 0 && mv == 1 ){
			if(c > 0 ){
				var req ={
					type : 'POST',
					data : {id:mid,_token:token},
					url: per_url,
					 beforeSend: function( xhr ) {
						$this.find('.barp').html('');
					  },
					success: function(data){
						data = $.parseJSON(data);
						$this.find('.barp').html('');
						if(data.p != 0){
							if(data.p > 0){
								$this.find('.barp').html(data.p+'% <img src="'+base_url+'/images/up.png"/>');
							}else{
								$this.find('.barp').html(data.p+'% <img src="'+base_url+'/images/down.png"/>');
							}
						}
						
					}
				};
				requests.push(req);
			}
		});*/
		//console.log(requests);
		$(requests).each($).wait(function(){ return $.ajax(this[0]) });
		$(".advance-options").fancybox({
			scrolling	: 'no',
			autoSize    : true,
			autoHeight 	: true,
			minWidth : 300,
			width : 400,
			minHeigh : 300,
			beforeShow : function(){
				var id = this.element[0].id;
				var p = $('#metric-setting'+id);
				var v = p.find('.allmetricdata');
				
				$('#metric').val(id);
				$('#setaccount').val(v.val());
				$('#setcampaign').val(v.attr('data-c'));
				$('#setadgroups').val(v.attr('data-ag'));
				$('#setkeywords').val(v.attr('data-k'));
				$('#setads').val(v.attr('data-a'));
				$('#report').val(v.attr('data-r'));
				$('#datetime').val(v.attr('data-d'));
				$('#datetype').val(v.attr('data-t'));
				var sc = p.find('.one');
				var sdt = p.find('.one').attr('f');
				sdt = sdt.split('-');
				sc.find('.dr-date-start').html('');
				sc.find('.dr-date-end').html('');
				sc.find('.dr-date-start').html(sdt[0]);
				sc.find('.dr-date-end').html(sdt[1]);
				console.log(sdt);
				$.ajax({
					type : 'POST',
					data : $('#metricform').serialize(),
					url : get_setting_url,
					beforeSend: function( xhr ) {
						p.find('.prep').show();
						p.find('.cv').hide();
						
					  },
					success: function(data){
						data = $.parseJSON(data);
						p.find('.metcpc').html('')
						p.find('.metcpc').html(data.cpc);
						p.find('.bari').html('')
						p.find('.bari').html(data.cpc);
						p.find('.prep').hide();
						p.find('.cv').show();
						p.find('.barp').html('');
						if(data.p != 0){
							if(data.p > 0){
								p.find('.barp').html(data.p+'% <img src="'+base_url+'/images/up.png"/>');
							}else{
								p.find('.barp').html(data.p+'% <img src="'+base_url+'/images/down.png"/>');
							}
						}
						
					}
				});
			}
		});
		
		$(document).on('click','.alert',function(e){
			$(this).remove();
		});
		$('.dt').daterangepicker(
		{
			locale: {
			  format: 'YYYY/MM/DD'
			},
		});
		$('.dt').on('apply.daterangepicker', function(ev, picker) {
		  var p = $(this).parent().parent().parent();
		  p.find('.days').val('');
		  p.find('button:eq(0)').text('Select Date Range');
		  $('#datetype').val('middle');
		  $('#datetime').val(ev.currentTarget.value);
		  $.ajax({
				type : 'POST',
				data : $('#metricform').serialize(),
				url : get_setting_url,
				beforeSend: function( xhr ) {
					p.find('.prep').show();
					p.find('.cv').hide();
					
				  },
				success: function(data){
					data = $.parseJSON(data);
					p.find('.metcpc').html('')
					p.find('.metcpc').html(data.cpc);
					p.find('.bari').html('')
					p.find('.bari').html(data.cpc);
					p.find('.prep').hide();
					p.find('.cv').show();
					//$.fancybox.close();
				}
			});
		});
		$(document).on('click','.dater',function(e){
			$('.show-calendar').css('z-index',9999);
		});
		 /*$(document).find('.adg').find('li').on('click',function(){
			 alert(1);
		});*/
		$(document).on('click','.setting-save',function(){
			var p = $(this).parent().parent().parent().parent();
			$.ajax({
				type : 'POST',
				data : $('#metricform').serialize(),
				url : setting_url,
				success: function(data){
					data = $.parseJSON(data);
					p.find('.metcpc').html('')
					p.find('.metcpc').html(data.cpc);
					p.find('.bar').text(data.cpc);
					var val = parseInt(p.find('.bar').text());
					var tar = parseInt(p.find('.bar').attr('data-tar'));
					var met = p.find('.bar').attr('data-met');
					var opt = { 
						horBarHeight:30,
						foreColor:color,
						horTitle:'$',
						numType:'absolute',
						numMin:0,
						numMax:1000000
					}; 
					$.fancybox.close();
					var color = '#00FF00';
					if(val<=tar){
						color = '#FF0000';
					}
					//p.find('.bar').barIndicator('destroy');
					//p.find('.bar').barIndicator(opt);
				}
			});
		});
		
		var bars = $('.bar');
		$.each(bars,function(i,v){
			var val = parseInt($(this).text());
			var tar = parseInt($(this).attr('data-tar'));
			var met = $(this).attr('data-met');
			var color = '#00FF00';
			if(val<=tar){
				color = '#FF0000';
			}
			var opt = { 
				horBarHeight:30,
				foreColor:color,
				horTitle:'$',
				numType:'absolute',
			 	numMin:0,
			 	numMax:1000000,
				animation : false
			}; 
			//bar[met] = $(this);
			 //$(this).barIndicator(opt);
		});
		
		$(document).on('click','.dtt',function(){
			var p = $(this).parent().parent().parent();
			p.find('.dis').attr('disabled',true);
			if($(this).val() != 'lower'){
				$(this).parent().next().find('.dis').attr('disabled',false);
			}else{
				$(this).parent().next().next().find('.dis').attr('disabled',false);
			}
		});
  });