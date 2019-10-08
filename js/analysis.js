var sendRequest = function(controller) {
	Metronic.blockUI();
	$.ajax({     
		type:'post',
		url: controller,
		dataType:'JSON', 
		success: callAfter,
	    error: function (request,status, error) {
	    	Metronic.unblockUI();
	        alert(status);
	    }
	});
}

var callAfter = function(data) {
	Metronic.unblockUI();
	var ctrl = data.ctrl;
	switch(ctrl) {
	  case 'analysis':
	  		if(data.empty){
	  			alert('Please upload your excell file.');
	  		}else{
	    		showResult(data);
	    		customerPerDay(data.customerPerDay);
	    		customerPerWeek(data.customerPerWeek);
	    		customerPerHour(data.customerPerHour);
	    		customerPerWeekend(data.customerPerWeekend);
	    		top10PerformanceEmployee(data.topPerformanceEmployee);
	    		top10WaitingTime(data.topWaitingTime);
	    		employeeAvailablePerHour(data.employeeAvailablePerHour);	    		
	  		}
	    break;
	  case 'format':
	  			alert('Uploaded data is succussfully deleted')
	    	clearData();
	    break;
	}
}

var top10PerformanceEmployee = function (data) {
	var str = '';
	for(var i = 0 ; i < data.length ; i++){
		str = str+"<h2 style='font-size:14px; font-style:italic; font-weight:bold'>"+(i+1)+' .&nbsp;&nbsp;&nbsp;&nbsp;'+data[i]+"</h2>"; 
	}
	$('#top10PerformanceEmployees').html(str);
}

var top10WaitingTime = function (data) {
	var str = '';
	for(var i = 0 ; i < data.length ; i++){
		str = str + "<h2 style='font-size:14px; font-style:italic; font-weight:bold'>"+(i+1)+' .&nbsp;&nbsp;&nbsp;&nbsp;'+data[i]['ticketWaitTime'] + '&nbsp;&nbsp;&nbsp;' + data[i]['date'] +'('+ data[i]['day'] + ")</h2>"; 
	}
	$('#top10WaitingTime').html(str);
}

var showResult = function(data) {
	$('#period').html('<h2 style="font-size:21px; margin-top:0">'+data.from_date_format+' - '+data.to_date_format+'</h2>');
	$('#customer').text(data.customer_num);
	$('#ticket').text(data.total_ticket);
	$('#operator').text(data.operator_num);
}

var clearData = function() {
	$('#multiFiles').val('');
	$('#period').html('Selected');
	$('#customer').text('0');
	$('#ticket').text('0');
	$('#operator').text('0');
	customerPerDay([]);
	customerPerWeek([]);
	customerPerHour([]);
	customerPerWeekend([]);
	top10PerformanceEmployee([]);
	top10WaitingTime([]);
	employeeAvailablePerHour([]);
}

var analysis = function() {
	var controller = 'analysis.php';
	sendRequest(controller);
}

var format = function () {
	var controller = 'format.php';
	sendRequest(controller);
}

$(document).ready(function() {
	$('#analysis').click(analysis);
	$('#format').click(format);
	$('#uploadAction').click(uploadFile);
});

