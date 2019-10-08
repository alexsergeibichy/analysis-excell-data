<?php

require_once 'Classes/PHPExcel.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "excell_db";
$connect = new mysqli('localhost', 'root', '');
//db check

if (!mysqli_select_db($connect,$dbname)){
    $sql = "CREATE DATABASE ".$dbname;
    $connect->query($sql);
}

$connect = new mysqli($servername, $username, $password, $dbname);
if (!$connect->query("DESCRIBE log")) {
    $sql = "CREATE TABLE `log` (
          `id` int(225) NOT NULL AUTO_INCREMENT,
          `date` date DEFAULT NULL,
          `branch` varchar(50) DEFAULT NULL,
          `ticketNumber` varchar(10) DEFAULT NULL,
          `ticketIssueTime` time DEFAULT NULL,
          `ticketCallTime` time DEFAULT NULL,
          `ticketWaitTime` time DEFAULT NULL,
          `TicketEndTime` time DEFAULT NULL,
          `TotalServingTime` time DEFAULT NULL,
          `counter` int(10) DEFAULT NULL,
          `category` varchar(20) DEFAULT NULL,
          `operator` varchar(20) DEFAULT NULL,
          `serviceTime` time DEFAULT NULL,
          `totalApplicants` int(10) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    mysqli_query($connect, $sql);   
}

function insertData($fileName){
    $uploadDir = 'uploads/';
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "excell_db";
    $connect = new mysqli($servername, $username, $password, $dbname);


    $excel = PHPExcel_IOFactory::load($uploadDir.$fileName);
    // echo $uploadDir.$fileName;exit();
    $excel->setActiveSheetIndex(0);

    $start = 3;
    $count = 0;
    while ( $excel->getActiveSheet()->getCell('A'.($count+$start))->getValue() != '' ) {
        $count++;
    }

    // $connect->query('TRUNCATE TABLE log;');

    $rowUnit = 2000;
    $roopNum = floor($count/$rowUnit);
    $extraNum = $count%$rowUnit;

    for ($i=0; $i < $roopNum; $i++) { 
        $sql = "INSERT INTO `log`(`date`, `branch`, `ticketNumber`, `ticketIssueTime`, `ticketCallTime`, `ticketWaitTime`, `TicketEndTime`, `TotalServingTime`, `counter`, `category`, `operator`, `serviceTime`, `totalApplicants`) VALUES ";
        for($j=0; $j < ($rowUnit); $j++){
            $no = $i*$rowUnit + $j + $start;
            $origDate = $excel->getActiveSheet()->getCell('A'.$no)->getFormattedValue();
            $newDate = date("Y-m-d", strtotime($origDate));     

            $Branch = $excel->getActiveSheet()->getCell('B'.$no)->getValue();
            $Branch = $Branch?$Branch:'';
            $TicketNumber = $excel->getActiveSheet()->getCell('C'.$no)->getValue();
            $TicketIssueTime = $excel->getActiveSheet()->getCell('D'.$no)->getFormattedValue();
            $TicketCallTime = $excel->getActiveSheet()->getCell('E'.$no)->getFormattedValue();
            $TicketWaitTime = $excel->getActiveSheet()->getCell('F'.$no)->getFormattedValue();
            $TicketEndTime = $excel->getActiveSheet()->getCell('G'.$no)->getFormattedValue();
            $TotalServingTime = $excel->getActiveSheet()->getCell('H'.$no)->getFormattedValue();
            $Counter = $excel->getActiveSheet()->getCell('I'.$no)->getValue();
            $Category = $excel->getActiveSheet()->getCell('J'.$no)->getValue();
            $Operator = $excel->getActiveSheet()->getCell('K'.$no)->getValue();
            $ServiceTime = $excel->getActiveSheet()->getCell('L'.$no)->getFormattedValue();
            $TotalApplicants = $excel->getActiveSheet()->getCell('M'.$no)->getValue();
            if($j == $rowUnit-1){
                $sql .= "('".$newDate."','".$Branch."','".$TicketNumber."','".$TicketIssueTime."','".$TicketCallTime."','".$TicketWaitTime."','".$TicketEndTime."','".$TotalServingTime."','".$Counter."','".$Category."','".$Operator."','".$ServiceTime."','".$TotalApplicants."');" ;
            }else{
                $sql .= "('".$newDate."','".$Branch."','".$TicketNumber."','".$TicketIssueTime."','".$TicketCallTime."','".$TicketWaitTime."','".$TicketEndTime."','".$TotalServingTime."','".$Counter."','".$Category."','".$Operator."','".$ServiceTime."','".$TotalApplicants."')," ;
            }           
        }
        $connect->query($sql);
    }


    $sql = "INSERT INTO `log`(`date`, `branch`, `ticketNumber`, `ticketIssueTime`, `ticketCallTime`, `ticketWaitTime`, `TicketEndTime`, `TotalServingTime`, `counter`, `category`, `operator`, `serviceTime`, `totalApplicants`) VALUES ";
    for ($j=0; $j < $extraNum; $j++) {
        $no = $i*$rowUnit+$j+ $start;
        $origDate = $excel->getActiveSheet()->getCell('A'.$no)->getFormattedValue();
        $newDate = date("Y-m-d", strtotime($origDate));
        $Branch = $excel->getActiveSheet()->getCell('B'.$no)->getValue();
        $Branch = $Branch?$Branch:'';
        $TicketNumber = $excel->getActiveSheet()->getCell('C'.$no)->getValue();
        $TicketIssueTime = $excel->getActiveSheet()->getCell('D'.$no)->getFormattedValue();
        $TicketCallTime = $excel->getActiveSheet()->getCell('E'.$no)->getFormattedValue();
        $TicketWaitTime = $excel->getActiveSheet()->getCell('F'.$no)->getFormattedValue();
        $TicketEndTime = $excel->getActiveSheet()->getCell('G'.$no)->getFormattedValue();
        $TotalServingTime = $excel->getActiveSheet()->getCell('H'.$no)->getFormattedValue();
        $Counter = $excel->getActiveSheet()->getCell('I'.$no)->getValue();
        $Category = $excel->getActiveSheet()->getCell('J'.$no)->getValue();
        $Operator = $excel->getActiveSheet()->getCell('K'.$no)->getValue();
        $ServiceTime = $excel->getActiveSheet()->getCell('L'.$no)->getFormattedValue();
        $TotalApplicants = $excel->getActiveSheet()->getCell('M'.$no)->getValue();
        if($j == $extraNum-1){
            $sql .= "('".$newDate."','".$Branch."','".$TicketNumber."','".$TicketIssueTime."','".$TicketCallTime."','".$TicketWaitTime."','".$TicketEndTime."','".$TotalServingTime."','".$Counter."','".$Category."','".$Operator."','".$ServiceTime."','".$TotalApplicants."');" ;
        }else{
            $sql .= "('".$newDate."','".$Branch."','".$TicketNumber."','".$TicketIssueTime."','".$TicketCallTime."','".$TicketWaitTime."','".$TicketEndTime."','".$TotalServingTime."','".$Counter."','".$Category."','".$Operator."','".$ServiceTime."','".$TotalApplicants."')," ;
        }       
    }
    $connect->query($sql);
}

$whitelist = array('xls','xlsx');

if (isset($_FILES['files']) && !empty($_FILES['files'])) {
    $no_files = count($_FILES["files"]['name']);
    for ($i = 0; $i < $no_files; $i++) {
        // $name = basename($_FILES['file']['name'][$i]);
        $extension = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);
        // echo $_FILES['files']['name'][$i];
        if (!in_array($extension, $whitelist)) {
            $error = 'Invalid file type uploaded.';
            echo $name;exit();
        }

        if ($_FILES["files"]["error"][$i] > 0) {
            echo "Error: " . $_FILES["files"]["error"][$i] . "<br>";
        } else {
            // if (file_exists('uploads/' . $_FILES["files"]["name"][$i])) {
            //     echo 'File already exists : uploads/' . $_FILES["files"]["name"][$i];
            // } else {
            //     move_uploaded_file($_FILES["files"]["tmp_name"][$i], 'uploads/' . $_FILES["files"]["name"][$i]);
            //     echo 'File successfully uploaded : uploads/' . $_FILES["files"]["name"][$i] . ' ';
            // }
            move_uploaded_file($_FILES["files"]["tmp_name"][$i], 'uploads/' . $_FILES["files"]["name"][$i]);
            insertData($_FILES["files"]["name"][$i]);
            echo 'File successfully uploaded : uploads/' . $_FILES["files"]["name"][$i] . ' ';
        }
    }
} else {
    echo 'Please choose at least one file';
}