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
    <?php
	    include('include_css.php');
    ?>
    <title>Manage Position</title>
    <style>
    body {
        text-align: center;
    }
    table, th, td {
        border: 1px solid;
    }
    .table{
        width: 80%;
    }
</style>
</head>

<script>
    function popup(text)
    {
        alert(text);
    }
</script>

<body>
    <?php
        include('connection.php');
        
        $act = $_GET["act"];
        $employeeid = $_GET["employeeid"];
        $position = $_GET["position"];
        $notice = $_GET["notice"];
        $lockid = $_GET["lockid"];
        $name = $_GET["name"];
        $lockpos = $_GET["lockpos"];
        $lockposname = $_GET["lockposname"];
        
        $nowpossql = "SELECT prom.*
        FROM promotionalRecord prom
        INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promotedDate FROM promotionalRecord GROUP BY employeeId) pro
        ON prom.employeeId = pro.employeeId AND prom.promotedDate = pro.latest_promotedDate
        WHERE prom.employeeId = '$employeeid';";
        $nowposresult = mysqli_query($con,$nowpossql);
        $nowposarray = mysqli_fetch_assoc($nowposresult);
        $nowpos = $nowposarray["toPosition"];
        
        $sbranch = "SELECT toBr FROM relocationalRecord WHERE employeeId = '$employeeid' ORDER BY relocateDate DESC LIMIT 1";
	    $branchquery = $con->query($sbranch);
	    $bracnhrow = $branchquery->fetch_assoc();
	    $branch = $bracnhrow['toBr'];
	    
        if(!empty($employeeid) && $nowpos == $position)
        {
            echo "<script>
            popup('This person is already in this position')
            </script>";
            $act = 0;
        }
        else if($act == 1)
        {
            $depsql = "SELECT positionID, departmentID FROM jobInfo WHERE positionID = '$position'";
            $depresult = mysqli_query($con,$depsql);
            $deparray = mysqli_fetch_assoc($depresult);
            $department = $deparray["departmentID"];
            
            $oldpossql = "SELECT toPosition FROM promotionalRecord WHERE employeeId = '$employeeid' ORDER BY promotedDate DESC LIMIT 1";
	        $oldposquery = $con->query($oldpossql);
	        $oldposfetch = $oldposquery->fetch_assoc();
	        $oldposition = $oldposfetch['toPosition'];
            
            $nowpos2 = "SELECT COUNT(prom.employeeId) AS total FROM (
        SELECT promotionalRecord.* FROM promotionalRecord INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promotedDate FROM promotionalRecord GROUP BY employeeId) pro ON promotionalRecord.employeeId = pro.employeeId AND promotionalRecord.promotedDate = pro.latest_promotedDate) AS prom
        LEFT JOIN (
        SELECT relocationalRecord.* FROM relocationalRecord INNER JOIN (SELECT employeeId, MAX(relocateDate) AS latest_relocateDate FROM relocationalRecord GROUP BY employeeId) rel ON relocationalRecord.employeeId = rel.employeeId AND relocationalRecord.relocateDate = rel.latest_relocateDate) AS relo
        ON relo.employeeId = prom.employeeId WHERE prom.toPosition = '$oldposition' AND relo.toBr = '$branch'";
            $result2 = mysqli_query($con,$nowpos2);
            $nowposval2 = mysqli_fetch_assoc($result2);
            $nowcount = $nowposval2["total"];
            
            $maxcountsql = "SELECT * FROM enablePosition WHERE positionID = '$oldposition' AND branchID = '$branch'";
            $maxquery = mysqli_query($con,$maxcountsql);
            $maxfetch = mysqli_fetch_assoc($maxquery);
            $maxcount = $maxfetch["maximumAmount"];
            $remain = $maxcount - $nowcount + 1;
	        if($remain > 0)
	        {
	            $enasql = "UPDATE enablePosition SET enable = '1' WHERE positionID = '$oldposition' AND branchID = '$branch'";
	            $enaquery = mysqli_query($con,$enasql);
	        }
            
            $today = date("Y-m-d H:i:s");
            $prosql = "INSERT promotionalRecord (employeeId, promotedDate, toPosition, departmentID) VALUES ('$employeeid', '$today', '$position', '$department')";
            $proresult = mysqli_query($con, $prosql);
            
            $recid = "SELECT * FROM HRWorkRecord";
            $numcount = $con->query($recid);
            $numrow = $numcount->num_rows;
            $numrow = $numrow + 1;
            $hrsql = "INSERT INTO HRWorkRecord (RecID, HRID, workdate, workType, notice) VALUES ('$numrow', '$_SESSION[username]', '$today', 'PRO', NULLIF('$notice', ''))";
            $hrresult = $con->query($hrsql);
	        
	        $nowpos3 = "SELECT COUNT(prom.employeeId) AS total FROM (
        SELECT promotionalRecord.* FROM promotionalRecord INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promotedDate FROM promotionalRecord GROUP BY employeeId) pro ON promotionalRecord.employeeId = pro.employeeId AND promotionalRecord.promotedDate = pro.latest_promotedDate) AS prom
        LEFT JOIN (
        SELECT relocationalRecord.* FROM relocationalRecord INNER JOIN (SELECT employeeId, MAX(relocateDate) AS latest_relocateDate FROM relocationalRecord GROUP BY employeeId) rel ON relocationalRecord.employeeId = rel.employeeId AND relocationalRecord.relocateDate = rel.latest_relocateDate) AS relo
        ON relo.employeeId = prom.employeeId WHERE prom.toPosition = '$position' AND relo.toBr = '$branch'";
            $result3 = mysqli_query($con,$nowpos3);
            $nowposval3 = mysqli_fetch_assoc($result3);
            $nowcount2 = $nowposval3["total"];
	        
	        $maxcountsql2 = "SELECT * FROM enablePosition WHERE positionID = '$position' AND branchID = '$branch'";
            $maxquery2 = mysqli_query($con,$maxcountsql2);
            $maxfetch2 = mysqli_fetch_assoc($maxquery2);
            $maxcount2 = $maxfetch2["maximumAmount"];
            $remain2 = $maxcount2 - $nowcount2;
            if($remain2 == 0)
            {
                $clss = "UPDATE enablePosition SET enable = '0' WHERE positionID = '$position' AND branchID = '$branch' AND departmentID = '$department'";
                $clssupdate = mysqli_query($con, $clss) or die ("Error in query: $clss" .mysqli_error());
            }
            
            echo "<script>
            popup('Complete')
            </script>";
    
            $act = 0;
        }
    ?>
    <?php
    include('navigate_bar.php');
    ?>
    <section style="padding-top: 0px;">
        <h1 style = "margin-top: 70px;margin-bottom: 20px;">Manage Employee Position</h1>
        <br>
        
        <form action = "manage_position.php" method = "get">
        <button type = "submit" name = "branch" value="THBNK001" class = "btn btn-primary">THBNK001</button>&nbsp
        <button type = "submit" name = "branch" value="THCGM002" class = "btn btn-primary">THCGM002</button>
        <input type = "hidden" name = "act" value = "2"/>
        </form>
        <?php
        
        $branch = $_GET["branch"];
        ?>
        <br>
        <?php if($act == 2)
        {
        ?>
        <div>
        <form action = "manage_position.php" method = "get">
        <h4>
        <?php echo "EmployeeID : "?>
        <select name = "employeeid">
        <?php
            
            if(isset($lockid))
            {
                ?>
                <option value = "<?php echo $lockid?>"><?php echo $lockid. " - " .$name?></option>
                <?php
            }
            else
            {
            $sqllist = "SELECT e.employeeId, e.fname, e.lname, r.toBr FROM employee e
            LEFT JOIN (SELECT relo.*
            FROM relocationalRecord relo
            INNER JOIN (SELECT employeeId, MAX(relocateDate) AS latest_relocateDate FROM relocationalRecord GROUP BY employeeId) rel
            ON relo.employeeId = rel.employeeId AND relo.relocateDate = rel.latest_relocateDate) r
            ON r.employeeId = e.employeeId
           	WHERE r.toBr = '$branch'";
            $resultlist = $con->query($sqllist);
            while($rowlist = mysqli_fetch_array($resultlist))
            {
                ?>
                
                    <option value = "<?php echo $rowlist['employeeId']; ?>"><?php echo $rowlist["employeeId"]. " - " .$rowlist["fname"];?></option>
                <?php
            }
            }
        ?>
        </select>
        <?php echo "&nbsp To Position : "?>
        <select name = "position">
        <?php
        
            if(isset($lockid))
            {
                ?>
                <option value = "<?php echo $lockpos ?>"><?php echo $lockposname ?></option>
                <?php
            }
            else
            {
            $sqllist2 = "SELECT jobInfo.positionID, positionName, enable, branchID FROM jobInfo LEFT JOIN enablePosition ON enablePosition.positionID = jobInfo.positionID WHERE enablePosition.enable != '0' AND branchID = '$branch';";
            $resultlist2 = $con->query($sqllist2);
            while($rowlist2 = mysqli_fetch_array($resultlist2))
            {
                ?>
                    <option value = "<?php echo $rowlist2['positionID']; ?>"><?php echo $rowlist2['positionName']; ?></option>
                <?php
            }
            }
        ?>
        </select>
        <?php echo "&nbsp&nbsp&nbsp&nbsp"?>
        <input type = "hidden" name = "act" value = "1"/>
        <input type = "submit" class = "btn btn-success" onclick = "return confirm('Are you sure?')"/>
        <br><br><br>
        
        <?php echo "Sign Name : $_SESSION[username]";?>
        
        <br><br>
        Notice : <input type = 'text' name = 'notice'/>
        </h4>
        </form>
        </div>
        <?php
        }
        ?>
    </section>
    <?php
        include('include_js.php');
    ?>

</body>
</html>