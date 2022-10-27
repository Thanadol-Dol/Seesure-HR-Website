<?php
    include('connection.php');
    
    $fName = mysqli_real_escape_string($con,$_GET['fName']);
    
    $sql = "UPDATE InterviewRecord SET accept = 0 WHERE fName = '$fName' ";
    $result = mysqli_query($con, $sql) or die ("Error in query: $sql" .mysqli_error());
    
    mysqli_close($con);
    if($result){
	echo "<script type='text/javascript'>";
	echo "alert('Success');";
	echo "window.location = 'recruitpage.php'; ";
	echo "</script>";
	}
	else{
	echo "<script type='text/javascript'>";
	echo "alert('Error');";
        echo "window.location = 'recruitpage.php'; ";
	echo "</script>";
    }
?>