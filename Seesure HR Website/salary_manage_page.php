<!DOCTYPE html>
<html>
<title>Salary Management</title>
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

<body>
    <?php
    include('connection.php');
    ?>
    
    <?php
    include('navigate_bar.php');
    ?>
    <section style="padding-top: 0px;">
        <h1 style = "margin-top: 70px;margin-bottom: 20px;">Salary Management<br>(1-Month Back)</h1>
        <br>
        <?php
        ?>
        <table align = "center" class="table align-middle">
        <thead class="table-dark">
        <tr>
        <th>Profile</th>
        <th>EmployeeID</th>
        <th>Name</th>
        <th>Overtime Hours</th>
        <th>Late/Absent (Hours/Count)</th>
        <th>Average Rating</th>
        <th>Base Salary</th>
        <th>Commission</th>
        <th>Total Salary</th>
        <th>Account Number</th>
        </tr>
        </thead>
        <?php
        $sql = "SELECT e.*, SUM(ot.workhours) as sum_ot, jobInfo.base_salary FROM employee e
                LEFT JOIN OvertimeRecord ot ON ot.workerID = e.employeeId
                LEFT JOIN (SELECT prom.*
            	FROM promotionalRecord prom
            	INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promotedDate FROM promotionalRecord GROUP BY employeeId) pro
            	ON prom.employeeId = pro.employeeId AND prom.promotedDate = pro.latest_promotedDate) p
                ON p.employeeId = e.employeeId
                LEFT JOIN jobInfo ON jobInfo.positionID = p.toPosition
                GROUP BY e.employeeId";
        $result = $con->query($sql);
        
        $sql2 = "SELECT  l2.employeeId, l2.recordTime, l2.paymentCode, SEC_TO_TIME(SUM(TIME_TO_SEC(l2.late_hour))) AS total_time  
                FROM
                (SELECT l.employeeId, l.recordTime, l.paymentCode, SUBTIME(CAST(l.round_Time AS TIME), '8:00:00') AS late_hour
                FROM (SELECT employeeId, recordTime, DATE_FORMAT(DATE_ADD(recordTime, INTERVAL 30 MINUTE),'%Y-%m-%d %H:00:00') AS round_Time, paymentCode FROM LateAbsentRecord) l) l2
                GROUP BY l2.employeeId";
        $result2 = $con->query($sql2);
        
        $sql3 = "SELECT Seller, AVG(customer_rating) as average_rate FROM SellRecord GROUP BY Seller";
        $result3 = $con->query($sql3);
        
        $sql4 = "SELECT s.Seller, SUM(p.base_price) AS sum_price FROM SellRecord s
                LEFT JOIN productInfo p ON p.productID = s.product
                GROUP BY s.Seller";
        $result4 = $con->query($sql4);
        
        $otsql = "SELECT o.workerID, SUM(o.workhours) as sum_hours FROM OvertimeRecord o WHERE workdate > CURDATE() - INTERVAL 30 DAY GROUP BY o.workerID;";
        $otresult = $con->query($otsql);  
        $otfetch = $otresult->fetch_assoc();
        
        $comsql = "SELECT SellRecord.*, SUM(p.base_price) AS sum_gain FROM SellRecord
                    LEFT JOIN productInfo p ON p.productID = SellRecord.product
                    WHERE selldate > CURDATE() - INTERVAL 30 DAY
                    GROUP BY SellRecord.Seller;";
        $comresult = $con->query($comsql);
        $comfetch = $comresult->fetch_assoc();
        
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $latesql = "SELECT  l2.employeeId, l2.recordTime, l2.paymentCode, SEC_TO_TIME(SUM(TIME_TO_SEC(l2.late_hour))) AS total_time  , paymentInfo.paymentValue
                FROM
                (SELECT l.employeeId, l.recordTime, l.paymentCode, SUBTIME(CAST(l.round_Time AS TIME), '8:00:00') AS late_hour
                FROM (SELECT employeeId, recordTime, DATE_FORMAT(DATE_ADD(recordTime, INTERVAL 30 MINUTE),'%Y-%m-%d %H:00:00') AS round_Time, paymentCode FROM LateAbsentRecord) l) l2
                LEFT JOIN paymentInfo ON paymentInfo.paymentCode = l2.paymentCode
                WHERE l2.recordTime > CURDATE() - INTERVAL 30 DAY AND l2.employeeId = '$row[employeeId]' AND l2.paymentCode = 'LTE001'
                GROUP BY l2.employeeId
                ";
                $lateresult = $con->query($latesql);  
                $latefetch = $lateresult->fetch_assoc();
                $lateval = 0;
                
                $abssql = "SELECT  l2.employeeId, l2.recordTime, l2.paymentCode, COUNT(l2.paymentCode) AS ab_count  , paymentInfo.paymentValue
                FROM
                (SELECT l.employeeId, l.recordTime, l.paymentCode, SUBTIME(CAST(l.round_Time AS TIME), '8:00:00') AS late_hour
                FROM (SELECT employeeId, recordTime, DATE_FORMAT(DATE_ADD(recordTime, INTERVAL 30 MINUTE),'%Y-%m-%d %H:00:00') AS round_Time, paymentCode FROM LateAbsentRecord) l) l2
                LEFT JOIN paymentInfo ON paymentInfo.paymentCode = l2.paymentCode
                WHERE l2.recordTime > CURDATE() - INTERVAL 30 DAY AND l2.employeeId = '$row[employeeId]' AND l2.paymentCode = 'ABS002'
                GROUP BY l2.employeeId
                ";
                $absresult = $con->query($abssql);  
                $absfetch = $absresult->fetch_assoc();
                if(!isset($absfetch['ab_count']))
                {
                    $absfetch['ab_count'] = 0;
                }
                
                $total = $row['base_salary'] ;
                
                if(!isset($row['sum_ot']))
                {
                    $row['sum_ot'] = '0';
                }
                
                $row2 = $result2->fetch_assoc();
                $hour = date('H', strtotime($row2['total_time']));
                $hour = ltrim($hour, "0"); 
                if(empty($hour))
                {
                    $hour = '0';
                }
                
                $row3 = $result3->fetch_assoc();
                if(!isset($row3['average_rate']))
                {
                    $row3['average_rate'] = '0';
                }
                
                $row4 = $result4->fetch_assoc();
                if(!isset($row4['sum_price']))
                {
                    $row4['sum_price'] = '0';
                }
                $row4['sum_price'] = $row4['sum_price']*0.3;
                
                if($latefetch['employeeId'] == $row['employeeId'])
                {
                    $hourmonth = date('H', strtotime($latefetch['total_time']));
                    $hourmonth = ltrim($hourmonth, "0"); 
                    
                    $lateval = $latefetch['paymentValue'];
                    $total = $total + ($hourmonth*$lateval) - (500*$absfetch['ab_count']);
                    
                }
                else
                {
                    $hourmonth = 0;
                }
                
                if($otfetch['workerID'] == $row['employeeId'])
                {
                    $otval = $otfetch['sum_hours']*($row['base_salary']/(30*8))*1.5;
                    $total = $total + $otval;
                    $otcurhr = $otfetch['sum_hours'];
                }
                else
                {
                    $otcurhr = 0;
                }
                
                if($comfetch['Seller'] == $row['employeeId'])
                {
                    $comval = $comfetch['sum_gain']*0.3;
                    $total = $total + $comval;
                }
                
                $file = "photo/$row[employeeId].png";
                if (file_exists($file)) {
                    $file = "photo/$row[employeeId].png";
                }
                else {
                    $file = "photo/profile.jpg";
                }
                
                echo "<tr>";
                echo "<td><img src = '$file' width = '50px' height = '50px'/></td>";
                echo "<td>$row[employeeId]</td>";
                echo "<td>$row[fname] $row[lname]</td>";
                echo "<td>$otcurhr</td>";
                echo "<td>$hourmonth / $absfetch[ab_count]</td>";
                echo "<td>".number_format((float)$row3['average_rate'], 2, '.', '')."</td>";
                echo "<td>$row[base_salary]</td>";
                echo "<td>$comval</td>";
                echo "<td>$total</td>";
                echo "<td>$row[account_number]</td>";
                echo "</tr>";
                
                $lateval = 0;
                $otval = 0;
                $comval = 0;
                
                if($otfetch['workerID'] == $row['employeeId'])
                {
                    $otfetch = $otresult->fetch_assoc();
                }
                if($comfetch['Seller'] == $row['employeeId'])
                {
                    $comfetch = $comresult->fetch_assoc();
                }
                if($latefetch['employeeId'] == $row['employeeId'])
                {
                    $latefetch = $lateresult->fetch_assoc();
                }
            }
        }
        else {
        echo "empty";
        }
        ?>
        </table>
        </div>
    </section>
    <?php
	    include('include_js.php');
    ?>
</body>

</html>