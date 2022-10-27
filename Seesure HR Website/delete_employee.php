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
    <title>Delete Employee</title>
    <?php
	    include('include_css.php');
    ?>
    <style>
    body {
        text-align: center;
    }
    table, th, td {
        border: 1px solid;
    }
    .acp {
        background-color: #4CAF50;
        border-style: solid;
        border-color: black;
    }
    .dec {
        background-color: #f44336;
        border-style: solid;
        border-color: black;
    }
    .table{
        width: 80%;
    }
</style>
</head>
<script>
    function popup()
    {
        alert("Sign and Certified can not be the same person");
    }
</script>

<body>
    <?php
        include('connection.php');
        
        $id = $_GET["id"];
        
        $act = $_GET["act"];
        
        $name = $_GET["name"];
        if(!isset($act))
        {
            $act = $_POST["act"];
            $id = $_POST["id"];
            $notice = $_POST["notice"];
        }
    
        
        if($act == 1)
        {
            $sql1 = "DELETE FROM workHistory WHERE employeeId = '$id'";
            $result1 = $con->query($sql1);
            $sql2 = "DELETE FROM SellRecord WHERE Seller = '$id'";
            $result2 = $con->query($sql2);
            $sql3 = "DELETE FROM relocationalRecord WHERE employeeId = '$id'";
            $result3 = $con->query($sql3);
            $sql4 = "DELETE FROM promotionalRecord WHERE employeeId = '$id'";
            $result4 = $con->query($sql4);
            $sql5 = "DELETE FROM OvertimeRecord WHERE workerID = '$id'";
            $result5 = $con->query($sql5);
            $sql6 = "DELETE FROM mentorHistory WHERE mentorID = '$id' OR juniorID = '$id'";
            $result6 = $con->query($sql6);
            $sql7 = "DELETE FROM LateAbsentRecord WHERE employeeId = '$id'";
            $result7 = $con->query($sql7);
            $sql8 = "DELETE FROM educationHistory WHERE employeeId = '$id'";
            $result8 = $con->query($sql8);
            $sql9 = "DELETE FROM employee WHERE employeeId = '$id'";
            $result9 = $con->query($sql9);
            
            $recid = "SELECT * FROM HRWorkRecord";
            $numcount = $con->query($recid);
            $numrow = $numcount->num_rows;
            $numrow = $numrow + 1;
            
            $today = date("Y-m-d H:i:s");
            
            $sbranch = "SELECT toBr FROM relocationalRecord WHERE employeeId = '$id' ORDER BY relocateDate DESC LIMIT 1";
	        $branchquery = $con->query($sbranch);
	        $bracnhrow = $branchquery->fetch_assoc();
	        $branch = $bracnhrow['toBr'];
	        
	        $possql = "SELECT toPosition FROM promotionalRecord WHERE employeeId = '$id' ORDER BY promotedDate DESC LIMIT 1";
	        $posquery = $con->query($possql);
	        $posfetch = $posquery->fetch_assoc();
	        $position = $posfetch['toPosition'];
	        
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
            $remain = $maxcount - $nowcount + 1;
	        
	        if($remain > 0)
	        {
	            $enasql = "UPDATE enablePosition SET enable = '1' WHERE positionID = '$position' AND branchID = '$branch'";
	            $enaquery = mysqli_query($con,$enasql);
	        }
	        
	        $sql10 = "INSERT INTO HRWorkRecord (RecID, HRID, workdate, workType, notice) VALUES ('$numrow', '$_SESSION[username]', '$today', 'DEL', NULLIF('$notice', ''))";
            $result10 = $con->query($sql10);

            $act = 0;
        }
    ?>
    <?php
    include('navigate_bar.php');
    ?>
    <section style="padding-top: 0px;">
        <h1 style = "margin-top: 70px;margin-bottom: 20px;">Delete Employee Data</h1>
        <br>
        <?php
            $sql = "SELECT * FROM employee WHERE status = '0'";
            $result = $con->query($sql);
        ?>
        <table align = "center" class="table align-middle">
        <thead class="table-dark">
            <tr>
            <th>EmployeeID</th>
            <th>Name</th>
            <th></th>
            </tr>
        </thead>
        
        <?php
            if($act == 2)
            {
                ?>
                <form action = "delete_employee.php" method = "post">
                <?php
                echo "<tr>";
                echo "<td>$id</td>";
                echo "<td>$name</td>";
                echo "<td>
                <input type = 'submit' class = 'btn btn-danger' onclick = 'return confirm(\"Are you sure?\")'/>
                <a href = 'delete_employee.php' class = 'btn btn-primary'>Cancel</a>
                </td>";
                echo "</tr>";
                ?>
                </table>
                <br>
                <h5>

                <?php echo "Sign Name : $_SESSION[username]";?>
                <br><br>
                Notice : <input type = 'text' name = 'notice'/>
                <input type = 'hidden' name = 'act' value = '1'/>
                <input type = 'hidden' name = 'id' value = "<?php echo $id?>"/>
                
                </h5>
                </form>
                
                <?php
            }
            else
            {
                
        ?>
        
        <?php
        if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) { 
            echo "<tr>";
            echo "<td>".$row["employeeId"]."</td>";
            echo "<td>$row[fname] $row[lname]</td>";
            echo "<td>
            <a href = 'delete_employee.php?id=$row[employeeId]&act=2&name=$row[fname] $row[lname]' class = 'btn btn-success' onclick = 'return confirm(\"Are you sure?\")'>Delete</a>
            </td>";
            echo "</tr>";
        }
        }
        else {
        echo "empty";
        }
        ?>
        </table>
        
        <?php } ?>
    </section>
    <?php
	    include('include_js.php');
    ?>

</body>
</html>