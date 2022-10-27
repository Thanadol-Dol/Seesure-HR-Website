<!DOCTYPE html>
<html>
<title>Employee List</title>
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
        <h1 style = "margin-top: 70px;margin-bottom: 20px;">Employee List</h1>
        <br>
        <?php
            $sql = "SELECT e.employeeId, e.fname, e.lname, r.toBr, p.toPosition, jobInfo.positionName, p.toPosition, branch.branchName FROM employee e
            LEFT JOIN (SELECT relo.*
            FROM relocationalRecord relo
            INNER JOIN (SELECT employeeId, MAX(relocateDate) AS latest_relocateDate FROM relocationalRecord GROUP BY employeeId) rel
            ON relo.employeeId = rel.employeeId AND relo.relocateDate = rel.latest_relocateDate) r ON r.employeeId = e.employeeId
            LEFT JOIN (SELECT prom.*
            FROM promotionalRecord prom
            INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promoteDate FROM promotionalRecord GROUP BY employeeId) pro
            ON prom.employeeId = pro.employeeId AND prom.promotedDate = pro.latest_promoteDate) p ON p.employeeId = e.employeeId
            LEFT JOIN jobInfo ON jobInfo.positionID = p.toPosition
            LEFT JOIN branch ON branch.branchId = r.toBr";
            $result = $con->query($sql);
        ?>
        <table align = "center" class="table align-middle">
        <thead class="table-dark">
        <tr>
        <th>Profile</th>
        <th>Employee</th>
        <th>Name</th>
        <th>Position</th>
        <th>Branch</th>
        </tr>
        </thead>
        <?php
        if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) { 
            $file = "photo/$row[employeeId].png";
            if (file_exists($file)) {
                $file = "photo/$row[employeeId].png";
            }
            else {
                $file = "photo/profile.jpg";
            }
                
            echo "<tr>";
            echo "<td><a href = 'performance_eval.php?fname=$row[fname]&lname=$row[lname]&employeeId=$row[employeeId]&Br=$row[toBr]&position=$row[positionName]&posid=$row[toPosition]&file=$file' '><img src = '$file' width = '50px' height = '50px'/></a></td>";
            echo "<td>".$row["employeeId"]."</td>";
            echo "<td>$row[fname] $row[lname]</td>";
            echo "<td>".$row["positionName"]."</td>";
            echo "<td>".$row["branchName"]."</td>";
            echo "</tr>";
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