<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "excell_db";
	$connect = new mysqli($servername, $username, $password, $dbname);

	$sql = 'TRUNCATE TABLE `log`;';
	$res = $connect->query($sql);

	$result['ctrl'] = "format";
	$result['result'] = "success";
	echo json_encode($result);
?>