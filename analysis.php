<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "excell_db";
	$connect = new mysqli($servername, $username, $password, $dbname);

	$sql = 'SELECT * FROM `log`';
	$res = $connect->query($sql);
	// print_r(json_encode($res));exit();
	if($res->num_rows == 0){
		$result['ctrl'] = "analysis";
		$result['empty'] = true;
		$result['result'] = "success";
		echo json_encode($result);
		exit();
	}

	$sql = 'SELECT min(date) as from_date, max(date)  as to_date, count(*) as customer_num, sum(counter) as total_ticket FROM `log`';
	$res = $connect->query($sql);
	if ($res->num_rows > 0) {
		$row = $res->fetch_assoc();

		$result['from_date'] = $row['from_date'];
		$result['to_date'] = $row['to_date'];
		$result['from_date_format'] = date('M/d/Y',strtotime($row['from_date']));
		$result['to_date_format'] = date('M/d/Y',strtotime($row['to_date']));
		$result['customer_num'] = number_format($row['customer_num']);	
		$result['total_ticket'] = number_format($row['total_ticket']);	
	}

	$sql = 'SELECT count(*) as operator_num FROM 
			(
				SELECT count(*) as operator_num, operator FROM `log` GROUP by operator ORDER BY operator_num ASC
			) as operator';
	$res = $connect->query($sql);
	if ($res->num_rows > 0) {
		$row = $res->fetch_assoc();	
		$result['operator_num'] = number_format($row['operator_num']);	
	}

	/*----get chartData code-----*/

	//customers per day - start
	$customerPerDayArr = [];
	$sql = 'SELECT date, count(*) as customer_num FROM `log` GROUP by date ORDER by date';
	$res = $connect->query($sql);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			$temp = [];
	        // $temp['date'] = $row['date'];
	        // .substr(date('l', strtotime($row['date'])),0,2)
	        $temp['date'] = date('M/d',strtotime($row['date']));
	        $temp['customer_num'] = $row['customer_num'];
	        array_push($customerPerDayArr, $temp);
	    }	
	}
	$result['customerPerDay'] = $customerPerDayArr;
	//customers per day - end

	//customers per week - start
	/*----get getStartAndEndDate function code-----*/
	function getStartAndEndDate($week, $year) {
	  $dto = new DateTime();
	  $dto->setISODate($year, $week);
	  $ret['weekStartDate'] = $dto->format('Y-m-d');
	  $dto->modify('+6 days');
	  $ret['weekEndDate'] = $dto->format('Y-m-d');
	  return $ret;
	}

	/*----get dateRangeOfWeek code-----*/
	$fromDate = $result['from_date'];
	$toDate = $result['to_date'];
	$temp = explode('-', $fromDate);
	$year = $temp[0];
	$fromWeek = date("W", strtotime($fromDate));
	$toWeek = date("W", strtotime($toDate));	
	
	$dateRangeOfWeek = [];
	for ($i=$fromWeek; $i <= $toWeek; $i++) { 
		$temp = getStartAndEndDate($i,$year);
		array_push($dateRangeOfWeek, $temp);
	};

	/*----get customerPerWeekArr code-----*/	 
	$customerPerWeekArr = [];
	for ($i=0; $i < count($dateRangeOfWeek) ; $i++) { 
		$sql = "SELECT count(*) customer_num FROM `log` WHERE date >= '".$dateRangeOfWeek[$i]['weekStartDate']."' AND date <= '".$dateRangeOfWeek[$i]['weekEndDate']."'";
		$res = $connect->query($sql);		
		$row = $res->fetch_assoc();
		$temp = [];
		$date = date('M/d',strtotime($dateRangeOfWeek[$i]['weekStartDate'])).' - '.date('M/d',strtotime($dateRangeOfWeek[$i]['weekEndDate']));
		// $temp['dateRangeOfWeek'] = $dateRangeOfWeek[$i]['weekStartDate']."/".$dateRangeOfWeek[$i]['weekEndDate'];
		$temp['dateRangeOfWeek'] = $date;
		$temp['customer_num'] = $row['customer_num'];
		array_push($customerPerWeekArr, $temp);
	}

	$result['customerPerWeek'] = $customerPerWeekArr;
	//customers per week - end

	//customers per hour - start
	$customerPerHourArr = [];
	$sql = 'SELECT date, SUBSTRING(ticketIssueTime, 1, 2) as hour, COUNT(*) AS customer_num FROM log GROUP BY date, hour ORDER BY date';
	$res = $connect->query($sql);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			$temp = [];
			$date = date('M/d',strtotime($row['date']));
	        $temp['date'] = $date.':'.$row['hour'].'h';
	        $temp['customer_num'] = $row['customer_num'];
	        array_push($customerPerHourArr, $temp);
	    }	
	}
	$result['customerPerHour'] = $customerPerHourArr;
	//customers per hour - end

	//customers per weekend - start	
	/*----get dateRangeOfWeekend code-----*/
	$weekendDateArr = [];
	for ($i=0; $i < count($dateRangeOfWeek) ; $i++) { 
		$Friday = date('Y-m-d', strtotime('-2 day', strtotime($dateRangeOfWeek[$i]['weekEndDate'])));
		$Saturday = date('Y-m-d', strtotime('-1 day', strtotime($dateRangeOfWeek[$i]['weekEndDate'])));
		$Sunday = $dateRangeOfWeek[$i]['weekEndDate'];
		array_push($weekendDateArr, [$Friday, $Saturday, $Sunday]);
	}


	$weekendDataArr = [];
	for ($i=0; $i < count($weekendDateArr) ; $i++) {
		for($j=0; $j < count($weekendDateArr[$i]) ; $j++){
			$temp = [];
			$temp['date'] = date('M/d',strtotime($weekendDateArr[$i][$j]));

			$sql = "SELECT count(*) as customer_am FROM `log` WHERE date = '".$weekendDateArr[$i][$j]."' AND ticketIssueTime >= '00:00:00' AND ticketIssueTime <= '12:00:00'  ORDER BY ticketIssueTime";	
			$res = $connect->query($sql);
			$row = $res->fetch_assoc();	
			$temp['customer_am'] = $row['customer_am'];

			$sql = "SELECT count(*) as customer_pm FROM `log` WHERE date = '".$weekendDateArr[$i][$j]."' AND ticketIssueTime > '12:00:00' AND ticketIssueTime <= '20:00:00'  ORDER BY ticketIssueTime";
			$res = $connect->query($sql);
			$row = $res->fetch_assoc();	
			$temp['customer_pm'] = $row['customer_pm'];

			$sql = "SELECT count(*) as customer_ng FROM `log` WHERE date = '".$weekendDateArr[$i][$j]."' AND ticketIssueTime > '20:00:00'  ORDER BY ticketIssueTime";			
			$res = $connect->query($sql);
			$row = $res->fetch_assoc();	
			$temp['customer_ng'] = $row['customer_ng'];
			array_push($weekendDataArr , $temp);
		} 

	}

	$result['customerPerWeekend'] = $weekendDataArr;	
	//customers per weekend code - end

	// top Performance Emplyee - start
	$topPerformanceEmployeeArr = [];
	$sql = "SELECT count(*) as customer_num, operator FROM `log` GROUP BY operator ORDER by customer_num DESC LIMIT 10";
	$res = $connect->query($sql);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
	        array_push($topPerformanceEmployeeArr, $row['operator']);
	    }	
	}
	$result['topPerformanceEmployee'] = $topPerformanceEmployeeArr;
	// top Performance Emplyee - end

	// top Waiting time - start
	$topWaitingTime = [];
	$sql = "SELECT * FROM `log` ORDER by ticketWaitTime DESC LIMIT 10";
	$res = $connect->query($sql);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			$temp = [];
			$temp['date'] = $row['date'];
			$temp['day'] = date('l', strtotime($row['date']));	
			$temp['operator'] = $row['operator'];	
			$temp['ticketWaitTime'] = $row['ticketWaitTime'];
	        array_push($topWaitingTime, $temp);
	    }	
	}
	$result['topWaitingTime'] = $topWaitingTime;
	// top Waiting time -end


	$employeeAvailablePerHour = [];
	$sql = 'SELECT SUBSTRING(ticketIssueTime, 1, 2) as hour, COUNT(*) AS customer_num FROM log GROUP BY hour ';
	$res = $connect->query($sql);
	if ($res->num_rows > 0) {
		while($row = $res->fetch_assoc()) {
			$temp = [];
	        $temp['hour'] = $row['hour'];
	        $temp['customer_num'] = $row['customer_num'];
	        array_push($employeeAvailablePerHour, $temp);
	    }	
	}
	$result['employeeAvailablePerHour'] = $employeeAvailablePerHour;


	$result['ctrl'] = "analysis";
	$result['empty'] = false;
	$result['result'] = "success";
	echo json_encode($result);
?>