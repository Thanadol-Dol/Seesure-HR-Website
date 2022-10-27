<!DOCTYPE html>
<html>
<title>Performance Evaluation</title>
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
    h3{
        font-family: 'Poppins';
    }
</style>

<body>
    <?php
    include('connection.php');
    
    $employeeid = $_GET["employeeId"];
    $position = $_GET["position"];
    $fname = $_GET["fname"];
    $lname = $_GET["lname"];
    $branch = $_GET["Br"];
    $posid = $_GET["posid"];
    $file = $_GET["file"];
    ?>
    
    <?php
    include('navigate_bar.php');
    ?>
    <section style="padding-top: 0px;">
        <?php
            $sql = "SELECT COUNT(RecID) AS salecount FROM SellRecord WHERE Seller = '$employeeid'";
            $result = $con->query($sql);
            $sql2 = "SELECT COUNT(employeeId) AS latecount FROM LateAbsentRecord WHERE employeeId = '$employeeid'";
            $result2 = $con->query($sql2);
            
            $id_order = preg_replace('~\D~', '', $posid);
            $id_order = $id_order + 1;
            $order_fill = str_pad("$id_order", 3, '0', STR_PAD_LEFT);;
            
            $sqlfind = "SELECT * FROM jobInfo WHERE positionID LIKE '%$order_fill%'";
            $resultfind = $con->query($sqlfind);
            
            $rowfind = mysqli_fetch_array($resultfind);
            $poscheck = $rowfind['positionName'];
            $nextid = $rowfind['positionID'];;
            
            $color = "green";
            $nextstat = "Enable";
            $linktopos = "manage_position.php?act=2&branch=$branch&lockid=$employeeid&name=$fname&lockpos=$nextid&lockposname=$poscheck";
            if(!isset($poscheck))
            {
                $poscheck = "None";
                $nextstat = "Disable";
                $linktopos = '';
                $color = "red";
            }
            else
            {
                $ensql = "SELECT * FROM enablePosition WHERE positionID = '$nextid' AND branchID = '$branch'";
                $enquery = $con->query($ensql);
                $enfetch = mysqli_fetch_array($enquery);
                $encheck = $enfetch['enable'];
                
                if($encheck == 0)
                {
                    $nextstat = "Disable";
                    $linktopos = '';
                    $color = "red";
                }
            }
            
            $sqlscore = "SELECT * FROM employee WHERE employeeId = '$employeeid'";
            $queryscore = $con->query($sqlscore);
            $rowscore = $queryscore->fetch_assoc();
            
            $profsql = "SELECT s.Seller, SUM(p.base_price) AS sum_gain FROM SellRecord s
                        LEFT JOIN productInfo p ON s.product = p.productID
                        WHERE Seller = '$employeeid'
                        GROUP BY s.Seller";
            $queryprof = $con->query($profsql);
            $rowprofit = $queryprof->fetch_assoc();
            $totalprofits = 0.3*$rowprofit["sum_gain"];
            
             $latesql = "SELECT  l2.employeeId, l2.recordTime, l2.paymentCode, SEC_TO_TIME(SUM(TIME_TO_SEC(l2.late_hour))) AS total_time  , paymentInfo.paymentValue
                FROM
                (SELECT l.employeeId, l.recordTime, l.paymentCode, SUBTIME(CAST(l.round_Time AS TIME), '8:00:00') AS late_hour
                FROM (SELECT employeeId, recordTime, DATE_FORMAT(DATE_ADD(recordTime, INTERVAL 30 MINUTE),'%Y-%m-%d %H:00:00') AS round_Time, paymentCode FROM LateAbsentRecord) l) l2
                LEFT JOIN paymentInfo ON paymentInfo.paymentCode = l2.paymentCode
                WHERE l2.recordTime > CURDATE() - INTERVAL 30 DAY AND l2.employeeId = '$employeeid' AND l2.paymentCode = 'LTE001'
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
                FROM
                (SELECT l.employeeId, l.recordTime, l.paymentCode, SUBTIME(CAST(l.round_Time AS TIME), '8:00:00') AS late_hour
                FROM (SELECT employeeId, recordTime, DATE_FORMAT(DATE_ADD(recordTime, INTERVAL 30 MINUTE),'%Y-%m-%d %H:00:00') AS round_Time, paymentCode FROM LateAbsentRecord) l) l2
                LEFT JOIN paymentInfo ON paymentInfo.paymentCode = l2.paymentCode
                WHERE l2.recordTime > CURDATE() - INTERVAL 30 DAY AND l2.employeeId = '$employeeid' AND l2.paymentCode = 'ABS002'
                GROUP BY l2.employeeId
                ";
                $absresult = $con->query($abssql);  
                $absfetch = $absresult->fetch_assoc();
            
            $row = $result->fetch_assoc();
            $row2 = $result2->fetch_assoc();
            
        ?>
    <section id="about" class="about">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h1 style="text-align:left;padding-left:25%;"><?php echo "$fname $lname" ?></h1>
        </div>

        <div class="row" style="text-align:left;">
          <div class="col-lg">
            <img src = '<?php echo $file?>' width = '300px' height = '300px' class="img-fluid rounded float-end">
          </div>
          <div class="col-lg pt-4 pt-lg-0 content">
            <h3><?php echo "$employeeid" ?></h3>
            <div class="row">
              <div class="col-lg">
                <ul>
                  <li><i class="bi bi-chevron-right"></i> <strong>Sales Count (Cumulative):</strong> <span><?php echo $row["salecount"] ?></span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Late Count (Cumulative):</strong> <span><?php echo $row2["latecount"] ?></span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Score (1-Month Back):</strong> <span><?php echo 100 - ($hourmonth*2) - ($absfetch["ab_count"]*5)?></span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Total Profit (Cumulative):</strong> <span><?php echo $totalprofits ?></span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Current Position:</strong> <span><?php echo "$position" ?></span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Next Position/Status:</strong> <span><?php echo "$poscheck/<a type = 'submit' href = '$linktopos' style = 'color: $color;'>$nextstat</a>" ?></span></li>
                </ul>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
    <br>
    <button onclick="window.location.href='employee_list.php'" class = "btn btn-dark">Back</button>
        
        </div>
    </section>
    <?php
	    include('include_js.php');
    ?>
</body>

</html>