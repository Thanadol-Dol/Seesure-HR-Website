<!DOCTYPE html>
<html>
<head>
    <?php
        include('include_css.php');
    ?>
    <title>Mentor Analyze</title>
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
    h3{
        font-family: 'Poppins';
    }
</style>
</head>
<body>
    <?php
        include('connection.php');
        $employeeId = $_GET['employeeId'];
  		$fname = $_GET['fname'];
  		$lname = $_GET['lname'];
        $experience = $_GET['experience'];
  		$SellCount = $_GET['SellCount'];
        $LateCount = $_GET['LateCount'];
        $Avg_rating = $_GET['Avg_rating'];
        $Junior_count = $_GET['Junior_count'];
        $file = $_GET['file'];;
        
        $sql = "SELECT e.employeeId, e.fname, e.lname, r.toBr, branch.branchName, e.phoneNumber FROM employee e
            LEFT JOIN (SELECT relo.*
            FROM relocationalRecord relo
            INNER JOIN (SELECT employeeId, MAX(relocateDate) AS latest_relocateDate FROM relocationalRecord GROUP BY employeeId) rel
            ON relo.employeeId = rel.employeeId AND relo.relocateDate = rel.latest_relocateDate) r ON r.employeeId = e.employeeId
            LEFT JOIN branch ON branch.branchId = r.toBr
            WHERE e.employeeId = '$employeeId'";
        $result = mysqli_query($con, $sql);
        $row = $result->fetch_assoc();
        
        $latesql = "SELECT  l2.employeeId, l2.recordTime, l2.paymentCode, SEC_TO_TIME(SUM(TIME_TO_SEC(l2.late_hour))) AS total_time  , paymentInfo.paymentValue
                FROM
                (SELECT l.employeeId, l.recordTime, l.paymentCode, SUBTIME(CAST(l.round_Time AS TIME), '8:00:00') AS late_hour
                FROM (SELECT employeeId, recordTime, DATE_FORMAT(DATE_ADD(recordTime, INTERVAL 30 MINUTE),'%Y-%m-%d %H:00:00') AS round_Time, paymentCode FROM LateAbsentRecord) l) l2
                LEFT JOIN paymentInfo ON paymentInfo.paymentCode = l2.paymentCode
                WHERE l2.recordTime > CURDATE() - INTERVAL 30 DAY AND l2.employeeId = '$employeeId' AND l2.paymentCode = 'LTE001'
                GROUP BY l2.employeeId
                ";
            $lateresult = $con->query($latesql);  
            if($lateresult->num_rows <= 0)
            {
                $hourmonth = 0;
            }
            else
            {
                $latefetch = $lateresult->fetch_assoc();
                $hourmonth = date('H', strtotime($latefetch['total_time']));
                $hourmonth = ltrim($hourmonth, "0");
            }
            
            $abssql = "SELECT  l2.employeeId, l2.recordTime, l2.paymentCode, COUNT(l2.paymentCode) AS ab_count  , paymentInfo.paymentValue
                FROM (SELECT l.employeeId, l.recordTime, l.paymentCode, SUBTIME(CAST(l.round_Time AS TIME), '8:00:00') AS late_hour
                FROM (SELECT employeeId, recordTime, DATE_FORMAT(DATE_ADD(recordTime, INTERVAL 30 MINUTE),'%Y-%m-%d %H:00:00') AS round_Time, paymentCode FROM LateAbsentRecord) l) l2
                LEFT JOIN paymentInfo ON paymentInfo.paymentCode = l2.paymentCode
                WHERE l2.recordTime > CURDATE() - INTERVAL 30 DAY AND l2.employeeId = '$employeeId' AND l2.paymentCode = 'ABS002'
                GROUP BY l2.employeeId
                ";
                $absresult = $con->query($abssql);  
                $absfetch = $absresult->fetch_assoc();
            
    ?>
    <?php
        include('navigate_bar.php');
    ?>
    <section id="about" class="about">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h1 style="text-align:left;padding-left:17%;"><?php echo "$fname $lname" ?></h1>
        </div>

        <div class="row" style="text-align:left;">
          <div class="col-lg-5">
            <img src = '<?php echo $file?>' class="img-fluid rounded float-end" style="height:300px;width:300px">
          </div>
          <div class="col-lg-7 pt-4 pt-lg-0 content">
            <h3><?php echo "$employeeId" ?></h3>
            <div class="row">
              <div class="col-lg-5">
                <ul>
                  <li><i class="bi bi-chevron-right"></i> <strong>Experience:</strong> <span><?php echo "$experience" ?> Years</span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Sold Product (1-Year Back):</strong> <span><?php echo "$SellCount" ?></span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Score (1-Month Back):</strong> <span><?php echo 100 - ($hourmonth*2) - ($absfetch['ab_count']*5)?></span></li>
                </ul>
              </div>
              <div class="col-lg-7">
                <ul>
                  <li><i class="bi bi-chevron-right"></i> <strong>Late Count (1-Year Back):</strong> <span><?php echo "$LateCount" ?></span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Customer Rating:</strong> <span><?php echo number_format((float)$Avg_rating,2,'.',''); ?></span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Junior In Care</strong> <span><?php echo "$Junior_count" ?></span></li>
                </ul>
              </div>
            </div>
            <h3>Info</h3>
            <div class="row">
                <div class="col-lg">
                     <ul>
                      <li><i class="bi bi-chevron-right"></i> <strong>Branch:</strong> <span><?php echo "$row[branchName]" ?></span></li>
                      <li><i class="bi bi-chevron-right"></i> <strong>Phone Number:</strong> <span><?php echo "$row[phoneNumber]" ?></span></li>
                    </ul>
                </div>
            </div>
          </div>
        </div>

      </div>
    </section>
    <button onclick="window.location.href='mentorselection.php'" class = "btn btn-dark">Back</button>
    <?php
        include('include_js.php');
    ?>

</body>
</html>