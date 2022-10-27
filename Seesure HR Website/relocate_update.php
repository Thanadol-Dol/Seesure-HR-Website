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
<head>
    <title>Relocate Update</title>
</head>
<body>
    <?php
        include('connection.php');
        $Relocate_date = date('Y-m-d', strtotime($_POST['Relocate_date']));
        $employeeId = $_POST["employeeId"];
  		$Old_Br = $_POST["Br"];
  		$New_Br = $_POST["new_br"];
  		$position = $_POST["position"];
  		$remain = $_POST["remain"];
        $notice = $_POST["notice"];
  		
	    if($remain > 0)
	   {
	       $enasql = "UPDATE enablePosition SET enable = '1' WHERE positionID = '$position' AND branchID = '$Old_Br'";
	       $enaquery = mysqli_query($con,$enasql);
	   }
	        
	   $nowpos3 = "SELECT COUNT(prom.employeeId) AS total FROM (
        SELECT promotionalRecord.* FROM promotionalRecord INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promotedDate FROM promotionalRecord GROUP BY employeeId) pro ON promotionalRecord.employeeId = pro.employeeId AND promotionalRecord.promotedDate = pro.latest_promotedDate) AS prom
        LEFT JOIN (
        SELECT relocationalRecord.* FROM relocationalRecord INNER JOIN (SELECT employeeId, MAX(relocateDate) AS latest_relocateDate FROM relocationalRecord GROUP BY employeeId) rel ON relocationalRecord.employeeId = rel.employeeId AND relocationalRecord.relocateDate = rel.latest_relocateDate) AS relo
        ON relo.employeeId = prom.employeeId WHERE prom.toPosition = '$position' AND relo.toBr = '$New_Br'";
            $result3 = mysqli_query($con,$nowpos3);
            $nowposval3 = mysqli_fetch_assoc($result3);
            $nowcount2 = $nowposval3["total"];
	        
	        $maxcountsql2 = "SELECT * FROM enablePosition WHERE positionID = '$position' AND branchID = '$New_Br'";
            $maxquery2 = mysqli_query($con,$maxcountsql2);
            $maxfetch2 = mysqli_fetch_assoc($maxquery2);
            $maxcount2 = $maxfetch2["maximumAmount"];
            $remain2 = $maxcount2 - $nowcount2 - 1;
            if($remain2 <= 0)
            {
                $clss = "UPDATE enablePosition SET enable = '0' WHERE positionID = '$position' AND branchID = '$New_Br'";
                $clssupdate = mysqli_query($con, $clss) or die ("Error in query: $clss" .mysqli_error());
            }
	        
	        
	        /*echo "Relocate_date = $Relocate_date <br>
	        employeeId = $employeeId <br>
	        Old_Br = $Old_Br <br>
	        New_Br = $New_Br <br>
	        hrid = $hrid <br>
	        certified = $certified <br>
	        position = $position <br>
	        remain = $remain <br>";*/
	        
	        
            $update_relocation = "INSERT INTO relocationalRecord (employeeId, fromBr, toBr, relocateDate) VALUES ('$employeeId', '$Old_Br', '$New_Br', '$Relocate_date')";
            $resultupdate1 = mysqli_query($con, $update_relocation) or die ("Error in query: $update_relocation " . mysqli_error());
            
            $recid = "SELECT COUNT(RecID) AS numrow FROM HRWorkRecord";
            $numcount = mysqli_query($con, $recid);
            $numfetch = mysqli_fetch_assoc($numcount);
            $numrow = $numfetch["numrow"];
            $numrow = $numrow + 1;
            $workdate = date("Y-m-d H:i:s");
            $workType = "RELOC";
            $updateHRwork = "INSERT INTO HRWorkRecord (RecID, HRID, workdate, workType, notice) VALUES ('$numrow', '$_SESSION[username]', '$workdate', '$workType', NULLIF('$notice',''))";
            $resultupdate2 = mysqli_query($con, $updateHRwork) or die ("Error in query: $updateHRwork " . mysqli_error());
            
            if($resultupdate1 && $resultupdate2){
                echo "<script type='text/javascript'>";
        	    echo "alert('Update Succesfuly');";
        	    echo "window.location = 'relocate_empselect.php'; ";
        	    echo "</script>";
            }
            else{
            	echo "<script type='text/javascript'>";
            	echo "alert('Error back to Update again');";
                echo "window.location = 'relocate_empselect.php'; ";
            	echo "</script>";
            }
    ?>
</body>
</html>