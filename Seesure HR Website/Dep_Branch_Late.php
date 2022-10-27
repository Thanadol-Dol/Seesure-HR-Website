<!DOCTYPE html>
<html>
<head>
    <?php
        include('include_css.php');
    ?>
    <title>Top 5 Dep & Branch Late</title>
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
    .table {
        width: 80%;
    }
</style>
</head>
<body>
    <?php
        include('connection.php');
    ?>
    <?php
        $present = date("Y");
        $sql = "SELECT b.branchName, d.departmentName,HOUR(DATE_FORMAT(DATE_ADD(SEC_TO_TIME(SUM(TIME_TO_SEC(l2.late_hour))), INTERVAL 30 MINUTE),'%H:00:00')) AS Sum_Late,HOUR(DATE_FORMAT(DATE_ADD(SEC_TO_TIME(AVG(TIME_TO_SEC(l2.late_hour))), INTERVAL 30 MINUTE),'%H:00:00')) AS Avg_Late,COUNT(l2.recordTime) AS Count_Late, COUNT(DISTINCT e.employeeId) AS Count_Emp FROM employee e JOIN (SELECT r.* FROM relocationalRecord r INNER JOIN (SELECT employeeId, MAX(relocateDate) AS latest_relocateDate FROM relocationalRecord GROUP BY employeeId) rel
        ON r.employeeId = rel.employeeId AND r.relocateDate = rel.latest_relocateDate) relo ON e.employeeId = relo.employeeId
        JOIN (SELECT p.* FROM promotionalRecord p INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promotedDate FROM promotionalRecord GROUP BY employeeId) pro
        ON p.employeeId = pro.employeeId AND p.promotedDate = pro.latest_promotedDate) prom ON e.employeeId = prom.employeeId
        JOIN (SELECT l.employeeId, l.recordTime, l.paymentCode, SUBTIME(CAST(l.recordTime AS TIME), '8:00:00') AS late_hour FROM LateAbsentRecord l) l2
        ON e.employeeId = l2.employeeId
        JOIN branch b
        ON relo.toBr = b.branchId
        JOIN department d
        ON prom.departmentID = d.departmentID AND relo.toBr = d.branchID
        WHERE l2.recordTime >= NOW() - INTERVAL 12 month
        GROUP BY relo.toBr, prom.departmentID
        ORDER BY Count_Emp DESC
        LIMIT 5;";
        $result = $con->query($sql);
    ?>
    <?php
    include('navigate_bar.php');
    ?>
    <section style="padding-top: 0px;">
        <h1 style = "margin-top: 70px;">Top 5 Dep & Branch Late</h1>
        <h1 style = "margin-bottom: 20px;">(1-Year Back)</h1>
        <br>
        <table align = "center" class="table align-middle">
        <thead class="table-dark">
        <tr>
        <th>Branch Name</th>
        <th>Department Name</th>
        <th>Sum Late Hours</th>
        <th>Average Late Hours</th>
        <th>Late Count</th>
        <th>Late Employee</th>
        </tr>
        </thead>
        <?php
        if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) { 
            echo "<tr>";
            echo "<td>".$row["branchName"]."</td>";
            echo "<td>".$row["departmentName"]."</td>";
            echo "<td>".$row["Sum_Late"]."</td>";
            echo "<td>".$row["Avg_Late"]."</td>";
            echo "<td>".$row["Count_Late"]."</td>";
            echo "<td>".$row["Count_Emp"]."</td>";
            echo "</tr>";
        }
        }
        else {
        echo "empty";
        }
        ?>
        </table>
    </section>
    <?php
        include('include_js.php');
    ?>

</body>
</html>