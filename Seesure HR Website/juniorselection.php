<!DOCTYPE html>
<html>
<head>
  	<?php
	    include('include_css.php');
    ?>
    <title>Junior Selection</title>
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
<body>
    <?php
        include('connection.php');
        $mentor = $_GET["SeniorId"];
    ?>
    <?php
    include('navigate_bar.php');
    ?>
    <section style="padding-top: 0px;">
        <h1 style = "margin-top: 70px;margin-bottom: 20px;">Junior Selection</h1>
        <br>
        <?php
            $sql = "SELECT e.employeeId,e.fname,e.lname FROM employee e
            INNER JOIN promotionalRecord prom
            ON e.employeeId = prom.employeeId
    		INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promotedDate FROM promotionalRecord GROUP BY employeeId) pro
            ON prom.employeeId = pro.employeeId AND prom.promotedDate = pro.latest_promotedDate
            WHERE toPosition LIKE '%SJ%';";
            $result = $con->query($sql);
        ?>
        <table align = "center" class="table align-middle">
        <thead class="table-dark">
        <tr>
        <th>Profile</th>
        <th>EmployeeID</th>
        <th>Name</th>
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
            echo "<td><img src = '$file' width = '50px' height = '50px'/></td>";
            echo "<td>".$row["employeeId"]."</td>";
            echo "<td>$row[fname] $row[lname]</td>";
          	echo "<td>
            <a href = 'Training_Day.php?JuniorId=$row[employeeId]&SeniorId=$mentor' class = 'btn btn-success' onclick = 'return confirm(\"Are you sure?\")'>Select</a>
            </td>";
            echo "</tr>";
        }
        }
        else {
        echo "empty";
        }
        ?>
        </table>
        
        <br>
        <button onclick="window.location.href='mentorselection.php'" class = "btn btn-dark" style = "margin-bottom: 20px;">Back</button>
    </section>
    <?php
	    include('include_js.php');
    ?>
</body>
</html>