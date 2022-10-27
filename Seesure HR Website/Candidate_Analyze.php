<!DOCTYPE html>
<html>
<head>
    <?php
        include('include_css.php');
    ?>
    <title>Candidate Analyze</title>
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
        $fName = $_GET['fName'];
        $lName = $_GET['lName'];
        $positionID = $_GET['positionID'];
        $positionName = $_GET['positionName'];
        $start_ITV = $_GET['start_ITV'];
        $end_ITV = $_GET['end_ITV'];
        $note = $_GET['note'];
        $interviwer = $_GET['interviewer'];
        
        $sbranch = "SELECT toBr, branchName FROM relocationalRecord
                    LEFT JOIN branch ON branch.branchId = relocationalRecord.toBr
                    WHERE employeeId = '$interviwer' ORDER BY relocateDate DESC LIMIT 1";
	    $branchquery = $con->query($sbranch);
	    $bracnhrow = $branchquery->fetch_assoc();
	    $branch = $bracnhrow['toBr'];
	    $branchname = $bracnhrow['branchName'];
    ?>
    <?php
    include('navigate_bar.php');
    ?>
    <section style="padding-top: 0px;">
        <?php
            $sql = "SELECT base_salary FROM jobInfo
                    WHERE positionID = '$positionID';";
            $result = $con->query($sql);
            $row = $result->fetch_assoc();
        ?>
        
        <?php
        $avsql = "SELECT e.*, SUM(ot.workhours) as sum_ot, jobInfo.base_salary, p.toPosition, r.toBr FROM employee e
                LEFT JOIN OvertimeRecord ot ON ot.workerID = e.employeeId
                LEFT JOIN (SELECT prom.*
            	FROM promotionalRecord prom
            	INNER JOIN (SELECT employeeId, MAX(promotedDate) AS latest_promotedDate FROM promotionalRecord GROUP BY employeeId) pro
            	ON prom.employeeId = pro.employeeId AND prom.promotedDate = pro.latest_promotedDate) p
                ON p.employeeId = e.employeeId
                LEFT JOIN (SELECT relo.*
            	FROM relocationalRecord relo
            	INNER JOIN (SELECT employeeId, MAX(relocateDate) AS latest_relocateDate FROM relocationalRecord GROUP BY employeeId) rel
            	ON relo.employeeId = rel.employeeId AND relo.relocateDate = rel.latest_relocateDate) r
                ON r.employeeId = e.employeeId
                LEFT JOIN jobInfo ON jobInfo.positionID = p.toPosition
                WHERE r.toBr = '$branch' AND p.toPosition = '$positionID'
                GROUP BY e.employeeId";
        $avresult = $con->query($avsql);
        
        $avsql3 = "SELECT Seller, AVG(customer_rating) as average_rate FROM SellRecord GROUP BY Seller";
        $avresult3 = $con->query($avsql3);
        
        $avsql4 = "SELECT s.Seller, SUM(p.base_price) AS sum_price FROM SellRecord s
                LEFT JOIN productInfo p ON p.productID = s.product
                GROUP BY s.Seller";
        $avresult4 = $con->query($avsql4);
        
        $avsql2 = "SELECT  l2.employeeId, l2.recordTime, l2.paymentCode, SEC_TO_TIME(SUM(TIME_TO_SEC(l2.late_hour))) AS total_time  
                FROM
                (SELECT l.employeeId, l.recordTime, l.paymentCode, SUBTIME(CAST(l.round_Time AS TIME), '8:00:00') AS late_hour
                FROM (SELECT employeeId, recordTime, DATE_FORMAT(DATE_ADD(recordTime, INTERVAL 30 MINUTE),'%Y-%m-%d %H:00:00') AS round_Time, paymentCode FROM LateAbsentRecord) l) l2
                WHERE l2.employeeId = '$avrow[employeeId]'
                GROUP BY l2.employeeId";
                $avresult2 = $con->query($avsql2);
        
        $avg_total = 0;
        
        if($avresult->num_rows > 0) {
            while($avrow = $avresult->fetch_assoc()) {
                $avtotal = $avrow['base_salary'] ;
                
                if(!isset($avrow['sum_ot']))
                {
                    $avrow['sum_ot'] = '0';
                }
                
                $avrow2 = $avresult2->fetch_assoc();
                $avhour = date('H', strtotime($avrow2['total_time']));
                $avhour = ltrim($avhour, "0"); 
                if(empty($avhour))
                {
                    $avhour = '0';
                }
                
                $avrow3 = $avresult3->fetch_assoc();
                if(!isset($avrow3['average_rate']))
                {
                    $avrow3['average_rate'] = '0';
                }
                
                $avrow4 = $avresult4->fetch_assoc();
                if(!isset($avrow4['sum_price']))
                {
                    $avrow4['sum_price'] = '0';
                }
                $avrow4['sum_price'] = $avrow4['sum_price']*0.3;
                
                
                $avlateval = 0;
                $avlatesql = "SELECT  l2.employeeId, l2.recordTime, l2.paymentCode, SEC_TO_TIME(SUM(TIME_TO_SEC(l2.late_hour))) AS total_time  , paymentInfo.paymentValue
                FROM
                (SELECT l.employeeId, l.recordTime, l.paymentCode, SUBTIME(CAST(l.round_Time AS TIME), '8:00:00') AS late_hour
                FROM (SELECT employeeId, recordTime, DATE_FORMAT(DATE_ADD(recordTime, INTERVAL 30 MINUTE),'%Y-%m-%d %H:00:00') AS round_Time, paymentCode FROM LateAbsentRecord) l) l2
                LEFT JOIN paymentInfo ON paymentInfo.paymentCode = l2.paymentCode
                WHERE l2.recordTime > CURDATE() - INTERVAL 30 DAY AND l2.employeeId = '$avrow[employeeId]'
                GROUP BY l2.employeeId
                ";
                $avlateresult = $con->query($avlatesql);  
                $avlatefetch = $avlateresult->fetch_assoc();
                
                if($avlatefetch['employeeId'] == $avrow['employeeId'])
                {
                    $avlateval = $avlatefetch['paymentValue'];
                    $avtotal = $avtotal + $avlateval;
                    
                    $avhourmonth = date('H', strtotime($avlatefetch['total_time']));
                    $avhourmonth = ltrim($avhourmonth, "0"); 
                    
                    $avlatefetch = $avlateresult->fetch_assoc();
                }
                
                
                
                $avotsql = "SELECT o.workerID, SUM(o.workhours) as sum_hours FROM OvertimeRecord o WHERE workdate > CURDATE() - INTERVAL 30 DAY AND o.workerID = '$avrow[employeeId]' GROUP BY o.workerID";
                $avotresult = $con->query($avotsql);  
                $avotfetch = $avotresult->fetch_assoc();
                if(isset($avotfetch) && $avotfetch['workerID'] == $avrow['employeeId'])
                {
                    $avotval = $avotfetch['sum_hours']*($avrow['base_salary']/(30*8))*1.5;
                    $avtotal = $avtotal + $avotval;
                    $avotfetch = $avotresult->fetch_assoc();
                }
                
                
                $avcomsql = "SELECT SellRecord.*, SUM(p.base_price) AS sum_gain FROM SellRecord
                    LEFT JOIN productInfo p ON p.productID = SellRecord.product
                    WHERE selldate > CURDATE() - INTERVAL 30 DAY AND SellRecord.Seller = '$avrow[employeeId]'
                    GROUP BY SellRecord.Seller";
                $avcomresult = $con->query($avcomsql);
                $avcomfetch = $avcomresult->fetch_assoc();
                
                if($avcomfetch['Seller'] == $avrow['employeeId'])
                {
                    $avcomval = $avcomfetch['sum_gain']*0.3;
                    $avtotal = $avtotal + $avcomval;
                    $avcomfetch = $avcomresult->fetch_assoc();
                }
                
                
                if($avg_total == 0)
                {
                    $avg_total = $avtotal;
                    $n = 1;
                }
                else
                {
                    $avg_total = $avg_total + $avtotal;
                    $n = $n+1;
                }
                
                $avlateval = 0;
                $avotval = 0;
                $avcomval = 0;
                
            }
            $avg_total = $avg_total/$n;
        }
        
        ?>
    <section id="about" class="about">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h1 style="text-align:left;padding-left:17%;"><?php echo "$fName $lName" ?></h1>
        </div>

        <div class="row" style="text-align:left;">
          <div class="col-lg-5">
            <img src = '/photo/profile.jpg' width = '300px' height = '300px' class="img-fluid rounded float-end">
          </div>
          <div class="col-lg-7 pt-4 pt-lg-0 content">
            <h3>Interview Info</h3>
            <div class="row">
                <div class="col-lg">
                    <ul>
                      <li><i class="bi bi-chevron-right"></i> <strong>Interviewer:</strong> <span><?php echo "$interviwer" ?></span></li>
                      <li><i class="bi bi-chevron-right"></i> <strong>Interview Start:</strong> <span><?php echo "$start_ITV" ?></span></li>
                    </ul>
                </div>
                <div class="col-lg">
                    <ul>
                      <li><i class="bi bi-chevron-right"></i> <strong>Branch:</strong> <span><?php echo "$branchname" ?></span></li>
                      <li><i class="bi bi-chevron-right"></i> <strong>Interview End:</strong> <span><?php echo "$end_ITV" ?></span></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-lg">
                    <ul>
                      <li style="position: relative;bottom: 15px;"><i class="bi bi-chevron-right"></i> <strong>Interview Note:</strong> <span><?php echo "$note" ?></span></li>
                    </ul>
                </div>
            </div>
            <h3>Job Info</h3>
            <div class="row">
                <div class="col-lg">
                    <ul>
                      <li><i class="bi bi-chevron-right"></i> <strong>Position Prefered:</strong> <span><?php echo "$positionName" ?></span></li>
                      <li><i class="bi bi-chevron-right"></i> <strong>Base Salary:</strong> <span><?php echo $row["base_salary"] ?></span></li>
                    </ul>
                </div>
                <div class="col-lg">
                    <ul>
                      <li><i class="bi bi-chevron-right"></i> <strong>Average Salary:</strong> <span><?php echo "$avg_total" ?></span></li>
                    </ul>
                </div>
            </div>
          </div>
        </div>

      </div>
    </section>
        <button onclick="window.location.href='recruitpage.php'" class = "btn btn-dark">Back</button>
    </section>
    <?php
        include('include_js.php');
    ?>

</body>
</html>