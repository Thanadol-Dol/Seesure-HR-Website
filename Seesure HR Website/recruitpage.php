<!DOCTYPE html>
<html>
<title>Recruit Employee</title>
<head>
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
    .table{
        width: 80%;
    }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    include('connection.php');
    include('navigate_bar.php');
    ?>
    <section style="padding-top: 0px;">
        <h1 style = "margin-top: 70px;margin-bottom: 20px;">Recruit Employee</h1>
        <br>
        <?php
        $sql = "SELECT interviewer, fName, lName, prefer_position, interview_start, interview_end, interview_note, positionName, enablePosition.enable, toBr, jobInfo.departmentID, branch.branchName FROM InterviewRecord
            LEFT JOIN jobInfo ON InterviewRecord.prefer_position = jobInfo.positionID
            LEFT JOIN (SELECT toBr, employeeId FROM relocationalRecord WHERE relocateDate IN (SELECT MAX(relocateDate) FROM relocationalRecord GROUP BY employeeId)) AS rel ON rel.employeeId = InterviewRecord.interviewer
            LEFT JOIN enablePosition ON enablePosition.positionID = InterviewRecord.prefer_position AND enablePosition.branchID = rel.toBr
            LEFT JOIN branch ON branch.branchId = rel.toBr
            WHERE accept IS NULL AND enablePosition.enable = 1";
        $result = $con->query($sql);
        ?>
        
        <table align = "center" class="table align-middle">
        <thead class="table-dark">
        <tr>
        <th>Profile</th>
        <th>Name</th>
        <th width = "200">position prefered</th>
        <th>interview_start</th>
        <th>interview_end</th>
        <th width = "175">result</th>
        </tr>
        </thead>
        <?php
        if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) { 
            echo "<tr>";
            echo "<td><a href = 'Candidate_Analyze.php?fName=$row[fName]&lName=$row[lName]&positionName=$row[positionName]&positionID=$row[prefer_position]&start_ITV=$row[interview_start]&end_ITV=$row[interview_end]&note=$row[interview_note]&interviewer=$row[interviewer]'><img src = 'photo/profile.jpg' width = '50px' height = '50px'/></a></td>";
            echo "<td width = '125'>$row[fName] $row[lName]</td>";
            echo "<td>".$row['positionName']."</td>";
            echo "<td>".$row['interview_start']."</td>";
            echo "<td>".$row['interview_end']."</td>";
            echo "<td width = '200px'>
            <a href = 'recruit_accept_information.php?fName=$row[fName]&lName=$row[lName]&position=$row[prefer_position]&department=$row[departmentID]' class='btn btn-success' onclick = 'return confirm(\"Are you sure?\")'>Accept</a>
            <a href = 'recruitupdate.php?fName=$row[fName]' class='btn btn-danger' onclick = 'return confirm(\"Are you sure?\")'>Decline</a>
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