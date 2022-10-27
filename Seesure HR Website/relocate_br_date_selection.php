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
    <title>Relocate Branch Selection</title>
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
        * {
  margin: 0px;
  padding: 0px;
}
body {
  font-size: 110%;
  background: #F8F8FF;
}

form, .content {
  width: 35%;
  margin: 0px auto;
  padding: 20px;
  border: 1px solid #B0C4DE;
  background: white;
  border-radius: 0px 0px 10px 10px;
  border-spacing: 5px 5px;
  border-collapse: separate;
}
</style>
</head>
<body>
    <?php
        include('connection.php');
        $employeeId = $_GET["employeeId"];
  		$Br = $_GET["Br"];
  		$position = $_GET["position"];
    ?>
    <?php
    include('navigate_bar.php');
    ?>
    <section style="padding-top: 0px;">
        <h1 style = "margin-top: 70px;margin-bottom: 20px;">Branch Selection</h1>
        <form action="relocate_update.php" method = "post">
            <div>
                <div style = "margin-bottom:30px">
                    <h4>To Branch</h4>
                    <select name = "new_br">
                        <?php
                            $sqllist1 = "SELECT branchId, branchName FROM branch
                            WHERE branchId != '$Br'";
                            $resultlist1 = $con->query($sqllist1);
                            
                            $nowpos = "SELECT COUNT(prom.employeeId) AS total FROM (
                            SELECT promotionalRecord.* FROM promotionalRecord INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promotedDate FROM promotionalRecord GROUP BY employeeId) pro ON promotionalRecord.employeeId = pro.employeeId AND promotionalRecord.promotedDate = pro.latest_promotedDate) AS prom
                            LEFT JOIN (
                            SELECT relocationalRecord.* FROM relocationalRecord INNER JOIN (SELECT employeeId, MAX(relocateDate) AS latest_relocateDate FROM relocationalRecord GROUP BY employeeId) rel ON relocationalRecord.employeeId = rel.employeeId AND relocationalRecord.relocateDate = rel.latest_relocateDate) AS relo
                            ON relo.employeeId = prom.employeeId WHERE prom.toPosition = '$position' AND relo.toBr = '$Br'";
                            $posresult = mysqli_query($con,$nowpos);
                            $nowposfetch = mysqli_fetch_assoc($posresult);
                            $nowcount = $nowposfetch["total"];
                            
                            $maxcountsql = "SELECT * FROM enablePosition WHERE positionID = '$position' AND branchID = '$Br'";
                            $maxquery = mysqli_query($con,$maxcountsql);
                            $maxfetch = mysqli_fetch_assoc($maxquery);
                            $maxcount = $maxfetch["maximumAmount"];
                            $remain = $maxcount - $nowcount + 1;
                            
                            while($rowlist1 = mysqli_fetch_array($resultlist1))
                            {
                                ?>
                                <option value = "<?php echo $rowlist1['branchId']; ?>"><?php echo $rowlist1['branchName']; ?></option>
                                <?php
                            }
                        ?>
                    </select>
                </div>
                <h4>Relocate Date</h4>
                <input type="date" name="Relocate_date" required><br>
                <br><br>
                <div class = "HR" style="margin-bottom: 30px;">
                    <h4>Sign Name : <?php echo "$_SESSION[username]"?></h4>
                    <h4>Notice : 
                    <input type="text" required name="notice"">
                    </h4>
                </div>
                <input type="hidden" name="employeeId" value="<?php echo $employeeId?>">
                <input type="hidden" name="position" value="<?php echo $position?>">
                <input type="hidden" name="remain" value="<?php echo $remain?>">
                <input type="hidden" name="Br" value="<?php echo $Br?>">
                <br><button type="submit" value="" class="btn btn-success sub">Submit</button>
                &nbsp&nbsp
                <button onclick="window.location.href='relocate_empselect.php'" class = "btn btn-dark">Back</button>
            </div>
        </form>
        <br>
    </section>
    <?php
	    include('include_js.php');
    ?>
</body>
</html>