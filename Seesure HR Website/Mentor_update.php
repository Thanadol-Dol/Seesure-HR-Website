<?php 
  session_start(); 

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mentor Update</title>
</head>
<body>
    <?php
    include('connection.php');
    
    $notice = $_POST["notice"];
    $mentor = $_POST["mentor"];
    $junior = $_POST["junior"];
    $start_date = date('Y-m-d', strtotime($_POST['start_date']));
    $start_time = date('H:i:s', strtotime($_POST['start_time']));
    $start_date_day = strtotime($_POST['start_date']);
    $start = date('Y-m-d H:i:s', strtotime("$start_date $start_time"));
    $end_date = date('Y-m-d', strtotime($_POST['end_date']));
    $end_time = date('H:i:s', strtotime($_POST['end_time']));
    $end_date_day = strtotime($_POST['end_date']);
    $end = date('Y-m-d H:i:s', strtotime("$end_date $end_time"));
    $date_diff = round(($end_date_day - $start_date_day)/ (60 * 60 * 24)) + 1;
    $Hours = $end_time - $start_time;
    
    if($date_diff <= 0){
    	echo "<script type='text/javascript'>";
    	echo "alert('Wrong Date Input');";
    	echo "window.location = 'Training_Day.php?SeniorId=$mentor&JuniorId=$junior'; ";
    	echo "</script>";
	}
	else if($Hours <= 0){
    	echo "<script type='text/javascript'>";
    	echo "alert('Wrong Time Input');";
    	echo "window.location = 'Training_Day.php?SeniorId=$mentor&JuniorId=$junior'; ";
    	echo "</script>";
	}
	else
	{
    	$updatementor = "INSERT INTO mentorHistory (mentorID, juniorID, start_date, end_date) VALUES ('$mentor', '$junior', '$start', '$end')";
        $resultupdate1 = mysqli_query($con, $updatementor) or die ("Error in query: $updatementor " . mysqli_error());
        
        function getDatesFromRange($start, $end, $format = 'Y-m-d') {
            $array = array();
            $interval = new DateInterval('P1D');
          
            $realEnd = new DateTime($end);
            $realEnd->add($interval);
          
            $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
          
            foreach($period as $date) {                 
                $array[] = $date->format($format);
            }
            return $array;
        }
        $temp_Dates = getDatesFromRange($start_date,$end_date);
        $Dates = array();
        foreach($temp_Dates as $value) {
            $Dates[] = date('Y-m-d H:i:s', strtotime("$value $start_time"));
        }
        foreach($Dates as $day) {                 
            $updateovertime = "INSERT INTO OvertimeRecord (workerID, workdate, workhours) VALUES ('$mentor', '$day', '$Hours')";
            $resultupdate2 = mysqli_query($con, $updateovertime) or die ("Error in query: $updateovertime " . mysqli_error());
        }
        
        $recid = "SELECT COUNT(RecID) AS numrow FROM HRWorkRecord";
        $numcount = mysqli_query($con, $recid);
        $numfetch = mysqli_fetch_assoc($numcount);
        $numrow = $numfetch["numrow"];
        $numrow = $numrow + 1;
        $workdate = date("Y-m-d H:i:s");
        $workType = "MEN";
        $updateHRwork = "INSERT INTO HRWorkRecord (RecID, HRID, workdate, workType, notice) VALUES ('$numrow', '$_SESSION[username]', '$workdate', '$workType', NULLIF('$notice',''))";
        $resultupdate3 = mysqli_query($con, $updateHRwork) or die ("Error in query: $updateHRwork " . mysqli_error());
        
        if($resultupdate1 && $resultupdate2 && $resultupdate3){
            echo "<script type='text/javascript'>";
    	    echo "alert('Update Succesfuly');";
    	    echo "window.location = 'mentorselection.php'; ";
    	    echo "</script>";
        }
        else{
        	echo "<script type='text/javascript'>";
        	echo "alert('Error back to Update again');";
            echo "window.location = 'mentorselection.php'; ";
        	echo "</script>";
        }
	}
    ?>
</body>
</html>