<!DOCTYPE html>
<html>
<head>
    <?php
        include('include_css.php');
    ?>
    <title>Mentor Selection</title>
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
    include('navigate_bar.php');
    ?>
    <section style="padding-top: 0px;">
        <h1 style = "margin-top: 70px;margin-bottom: 20px;">Mentor Selection</h1>
        <br>
        <?php
            $sql = "SELECT e.fname, e.lname, e.employeeId,CEILING(DATEDIFF(CURRENT_DATE(),e.start_date)/365) as experience, COUNT(DISTINCT s.RecID) as SellCount, COUNT(DISTINCT l.recordTime) as LateCount, AVG(s.customer_rating) as Avg_rating,COUNT(DISTINCT m.juniorID) as Junior_count FROM employee e 
            LEFT JOIN (SELECT sel.* FROM SellRecord sel WHERE sel.selldate >= NOW() - INTERVAL 12 month) s
            ON e.employeeId = s.Seller
            LEFT JOIN (SELECT p.* FROM promotionalRecord p INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promotedDate FROM promotionalRecord GROUP BY employeeId) pro
            ON p.employeeId = pro.employeeId AND p.promotedDate = pro.latest_promotedDate) prom ON e.employeeId = prom.employeeId
            LEFT JOIN (SELECT late.* FROM LateAbsentRecord late WHERE late.recordTime >= NOW() - INTERVAL 12 month) l
            ON e.employeeId = l.employeeId
            LEFT JOIN (SELECT ment.* FROM mentorHistory ment WHERE end_date > NOW()) m
            ON e.employeeId = m.mentorID
            WHERE toPosition LIKE 'SS%'
    		GROUP BY e.employeeId;";
            $result = $con->query($sql);
        ?>
        <table align = "center" class="table align-middle">
        <thead class="table-dark">
            <tr>
            <th>Profile</th>
            <th>EmployeeID</th>
            <th>Name</th>
            <th>Junior In Care</th>
            <th></th>
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
            echo "<td><a href = 'Mentor_Analyze.php?fname=$row[fname]&lname=$row[lname]&employeeId=$row[employeeId]&experience=$row[experience]&SellCount=$row[SellCount]&LateCount=$row[LateCount]&Avg_rating=$row[Avg_rating]&Junior_count=$row[Junior_count]&file=$file'><img src = '$file' width = '50px' height = '50px'/></a></td>";
            echo "<td>".$row["employeeId"]."</td>";
            echo "<td>$row[fname] $row[lname]</td>";
            echo "<td>".$row["Junior_count"]."</td>";
            echo "<td>
            <a href = 'juniorselection.php?SeniorId=$row[employeeId]' class='btn btn-success' onclick = 'return confirm(\"Are you sure?\")'>Select</a>
            </td>";
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