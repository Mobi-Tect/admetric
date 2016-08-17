var bar = [];
var ss = new Calendar({
  element: $('.daterange--single'),
  current_date: 'June 15, 2015',
  format: {input: 'M/D/YYYY'},
  placeholder: 'Select a Date',
  required: false
});
var dd = new Calendar({
  element: $('.oneone'),
  earliest_date: 'January 1, 2000',
  latest_date: moment(),
  start_date: moment().subtract(29, 'days'),
  end_date: moment(),
  presets: [{
    label: 'Today',
    start: moment(),
    end: moment()
  },{
    label: 'Yesterday',
    start: moment().subtract(1, 'days'),
    end: moment().subtract(1, 'days')
  },{
    label: 'Last 7 Days',
    start: moment().subtract(7, 'days'),
    end: moment().subtract(1, 'days')
  },{
    label: 'Last 14 Days',
    start: moment().subtract(14, 'days'),
    end: moment().subtract(1, 'days')
  },{
    label: 'Last 30 Days',
    start: moment().subtract(30, 'days'),
    end: moment().subtract(1, 'days')
  },{
    label: 'Last 3 month',
    start: moment().subtract(3, 'month').startOf('month'),
    end: moment().subtract(1, 'month').endOf('month')
  },{
    label: 'Last 6 month',
    start: moment().subtract(6, 'month').startOf('month'),
    end: moment().subtract(1, 'month').endOf('month')
  },{
    label: 'Last year',
    start: moment().subtract(12, 'months').startOf('month'),
    end: moment().subtract(1, 'month').endOf('month')
  },{
    label: 'All years',
    start: 'Jan 1, 2000',
    end: moment().subtract(1, 'month').endOf('month')
  }],
  callback: function(e) {
    var start = moment(this.start_date).format('YYYYMMDD'),
        end = moment(this.end_date).format('YYYYMMDD');
		//var cc = this.element.parent().attr('class');
		var val = start+','+end; 
		var boardId = $('#boardid').val();
		 $.ajax({
				type : 'POST',
				data : {d:val,_token:token},
				url : msetting_url,
				beforeSend: function( xhr ) { },
				success: function(data){
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
									url: progress_url2,
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
						$(requests).each($).wait(function(){ return $.ajax(this[0]) });
				}
			});
		
  }
});
var dd = new Calendar({
  element: $('.one'),
  earliest_date: 'January 1, 2000',
  latest_date: moment(),
  start_date: moment().subtract(29, 'days'),
  end_date: moment(),
  presets: [{
    label: 'Today',
    start: moment(),
    end: moment()
  },{
    label: 'Yesterday',
    start: moment().subtract(1, 'days'),
    end: moment().subtract(1, 'days')
  },{
    label: 'Last 7 Days',
    start: moment().subtract(7, 'days'),
    end: moment().subtract(1, 'days')
  },{
    label: 'Last 14 Days',
    start: moment().subtract(14, 'days'),
    end: moment().subtract(1, 'days')
  },{
    label: 'Last 30 Days',
    start: moment().subtract(30, 'days'),
    end: moment().subtract(1, 'days')
  },{
    label: 'Last 3 month',
    start: moment().subtract(3, 'month').startOf('month'),
    end: moment().subtract(1, 'month').endOf('month')
  },{
    label: 'Last 6 month',
    start: moment().subtract(6, 'month').startOf('month'),
    end: moment().subtract(1, 'month').endOf('month')
  },{
    label: 'Last year',
    start: moment().subtract(12, 'months').startOf('month'),
    end: moment().subtract(1, 'month').endOf('month')
  },{
    label: 'All years',
    start: 'Jan 1, 2000',
    end: moment().subtract(1, 'month').endOf('month')
  }],
  callback: function(e) {
    var start = moment(this.start_date).format('YYYYMMDD'),
        end = moment(this.end_date).format('YYYYMMDD');
		//var cc = this.element.parent().attr('class');
		var val = start+','+end; 
		$('#datetype').val('lower');
		$('#datetime').val(val);
		var id = $('#metric').val();
		var p = $('#metric-setting'+id);
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
		//console.log(this);
		//console.log(this.element.parent().attr('class'));
		//console.debug('Start Date: '+ start +'\nEnd Date: '+ end);
  }
});

new Calendar({
  element: $('.two'),
  earliest_date: 'January 1, 2000',
  latest_date: moment(),
  start_date: moment().subtract(29, 'days'),
  end_date: moment(),
  presets: false,
  callback: function() {
    var start = moment(this.start_date).format('ll'),
        end = moment(this.end_date).format('ll');

    console.debug('Start Date: '+ start +'\nEnd Date: '+ end);
  }
});

new Calendar({
  element: $('.three'),
  earliest_date: 'January 1, 2000',
  latest_date: moment(),
  start_date: moment().subtract(29, 'days'),
  end_date: moment(),
  presets: [{
    label: 'Last 30 days',
    start: moment().subtract(29, 'days'),
    end: moment()
  },{
    label: 'Last month',
    start: moment().subtract(1, 'month').startOf('month'),
    end: moment().subtract(1, 'month').endOf('month')
  },{
    label: 'Last year',
    start: moment().subtract(12, 'months').startOf('month'),
    end: moment().subtract(1, 'month').endOf('month')
  }],
  callback: function() {
    var start = moment(this.start_date).format('ll'),
        end = moment(this.end_date).format('ll');

    console.debug('Start Date: '+ start +'\nEnd Date: '+ end);
  }
});
