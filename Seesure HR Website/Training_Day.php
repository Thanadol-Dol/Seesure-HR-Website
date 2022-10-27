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
    <title>Training Day</title>
    <style>
    body {
        text-align: center;
    }
    table, th, td {
        border: 1px solid;
    }
    h1{
        margin-bottom: 30px;
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
    h4{
        margin-right: 20px;
        display:inline-block;
    }
    .End{
        margin-right: 30px;
    }
    .back{
        position: relative;
        bottom: 59px;
        left: 50px;
    }
    .sub{
        position: relative;
        right: 50px;
    }
    .info{
        margin-top: 50px;
        margin-bottom: 35px;
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
        $mentor = $_GET["SeniorId"];
        $junior = $_GET["JuniorId"];
    ?>
    <?php
    include('navigate_bar.php');
    ?>
    <section style="padding-top: 0px;">
        <div>
            <h1 style = "margin-top: 70px;margin-bottom: 20px;">Training Day Selection</h1>
            <form action="Mentor_update.php" method = "post">
                <div class = "info">
                    <h4>Start Date</h4>
                    <input type="date" required name="start_date"><input type = "time" required name="start_time"><br>
                    <h4 class = "End">End Date</h4><input type = "date" required name="end_date"><input type = "time" required name="end_time"><br>
                </div>
                <input type="hidden" name="mentor" value="<?php echo $mentor?>">
                <input type="hidden" name="junior" value="<?php echo $junior?>">
                <h1>HR</h1>
                <div class = "HR" style="margin-bottom: 50px;">
                    <h4>Sign Name : <?php echo $_SESSION['username']?></h4>
                    <br>
                    <h4 style="position: relative;left: 33px;">Notice : </h4>
                    <input type="text" name="notice" style="position: relative;left: 33px;">
                </div>
                <button type="submit" value="" class="btn btn-success sub">Submit</button>
            </form>
                <button onclick="window.location.href='juniorselection.php?SeniorId=<?php echo $mentor?>'" class = "btn btn-dark back" name="SeniorId" value="<?php echo $mentor?>">Back</button>
        </div>
    </section>
    <?php
        include('include_js.php');
    ?>
</body>
</html>