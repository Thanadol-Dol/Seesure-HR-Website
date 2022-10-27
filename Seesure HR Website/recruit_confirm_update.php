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
<html>
    <meta charset="UTF-8">
<title>Recruit Update</title>
<style>
</style>

<body>
    <?php
include('connection.php');

    $employeeID = $_POST["employeeID"];
	$fName = $_POST["fName"];
	$lName = $_POST["lName"];
	$dateofbirth = $_POST["dateofbirth"];
	$phonenumber = $_POST["phonenumber"];	
	$accountnumber = $_POST["accountnumber"];
	$today = date("Y-m-d H:i:s");
	
	$studentID = $_POST["studentID"];
	$schoolName = $_POST["schoolName"];
	$gpa = $_POST["gpa"];
	$graddate = $_POST["graddate"];
	
	$formercorp = $_POST["formercorp"];
	$latestposition = $_POST["latestposition"];
	$quitdate = $_POST["quitdate"];
	
	$position = $_POST['position'];
	$department = $_POST['department'];
	
	$notice = $_POST['notice'];
	
	$sqlupdate = "UPDATE InterviewRecord SET accept = 1 WHERE fName = '$fName'";
    $resultupdate = mysqli_query($con, $sqlupdate) or die ("Error in query: $sqlupdate" .mysqli_error());
    
    $sbranch = "SELECT toBr FROM relocationalRecord WHERE employeeId = '$_SESSION[username]' ORDER BY relocateDate DESC LIMIT 1";
	$branchquery = $con->query($sbranch);
	$bracnhrow = $branchquery->fetch_assoc();
	$branch = $bracnhrow['toBr'];
	
	$nowpos = "SELECT COUNT(prom.employeeId) AS total FROM (
        SELECT promotionalRecord.* FROM promotionalRecord INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promotedDate FROM promotionalRecord GROUP BY employeeId) pro ON promotionalRecord.employeeId = pro.employeeId AND promotionalRecord.promotedDate = pro.latest_promotedDate) AS prom
        LEFT JOIN (
        SELECT relocationalRecord.* FROM relocationalRecord INNER JOIN (SELECT employeeId, MAX(relocateDate) AS latest_relocateDate FROM relocationalRecord GROUP BY employeeId) rel ON relocationalRecord.employeeId = rel.employeeId AND relocationalRecord.relocateDate = rel.latest_relocateDate) AS relo
        ON relo.employeeId = prom.employeeId WHERE prom.toPosition = '$position'";
    $result = mysqli_query($con,$nowpos);
    $nowposval = mysqli_fetch_assoc($result);
    $nowcount = $nowposval["total"];

    $maxcountsql = "SELECT * FROM enablePosition WHERE positionID = '$position' AND branchID = '$branch'";
    $maxquery = mysqli_query($con,$maxcountsql);
    $maxfetch = mysqli_fetch_assoc($maxquery);
    $maxcount = $maxfetch["maximumAmount"];
    $remain = $maxcount - $nowcount - 1;

    $sql = "INSERT INTO employee (employeeId, fname, lname, DOB, phoneNumber, account_number, absent_quota, status, start_date) VALUES ('$employeeID', '$fName', '$lName', '$dateofbirth', '$phonenumber', '$accountnumber', 5, 1, '$graddate')";
    $result = mysqli_query($con, $sql) or die ("Error in query: $sql " . mysqli_error());
    
    $recid = "SELECT * FROM HRWorkRecord";
    $numcount = $con->query($recid);
    $numrow = $numcount->num_rows;
    $numrow = $numrow + 1;
    $hrwork = "INSERT INTO HRWorkRecord (RecID, HRID, workdate, workType, notice) VALUES ('$numrow', '$_SESSION[username]', '$today', 'RECRU', NULLIF('$notice', ''))";
    $resultHR = mysqli_query($con, $hrwork) or die ("Error in query: $hrwork" .mysqli_error());

    $sqlupdate2 = "INSERT INTO educationHistory (employeeId, studentId, schoolName, GPA, graduateDate) VALUES ('$employeeID', '$studentID', '$schoolName', '$gpa', '$graddate')";
    $resultupdate2 = mysqli_query($con, $sqlupdate2) or die ("Error in query: $sqlupdate2" .mysqli_error());
    
    if(!empty($formercorp))
    {
        $sqlupdate3 = "INSERT INTO workHistory (employeeId, formerCorp, latestPosition, quitTime) VALUES ('$employeeID', '$formercorp', '$latestposition', '$quitdate')";
        $resultupdate3 = mysqli_query($con, $sqlupdate3) or die ("Error in query: $sqlupdate3" .mysqli_error());
    }
    
    $sqlupdate4 = "INSERT INTO promotionalRecord (employeeId, promotedDate, toPosition, departmentID) VALUES ('$employeeID', '$today', '$position', '$department')";
    $resultupdate4 = mysqli_query($con, $sqlupdate4) or die ("Error in query: $sqlupdate4" .mysqli_error());
    
    $sqlupdate5 = "INSERT INTO relocationalRecord (employeeId, fromBr, toBr, relocateDate) VALUES ('$employeeID', '$branch', '$branch', '$today')";
    $resultupdate5 = mysqli_query($con, $sqlupdate5) or die ("Error in query: $sqlupdate5" .mysqli_error());
    
    if($remain == 0)
    {
        $clss = "UPDATE enablePosition SET enable = '0' WHERE positionID = '$position' AND branchID = '$branch' AND departmentID = '$department'";
        $clssupdate = mysqli_query($con, $clss) or die ("Error in query: $clss" .mysqli_error());
    }
    
	if($result){
	echo "<script type='text/javascript'>";
	echo "alert('Update Succesfuly');";
	echo "window.location = 'recruitpage.php'; ";
	echo "</script>";
	}
	else{
	echo "<script type='text/javascript'>";
	echo "alert('Error back to Update again');";
        echo "window.location = 'recruitpage.php'; ";
	echo "</script>";
    }
    
?>
</body>

</html>