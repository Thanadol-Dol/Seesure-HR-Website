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
<title>Recruit Accept Information</title>
<?php
	include('include_css.php');
?>

<style>
* {
  margin: 0px;
  padding: 0px;
}
body {
  font-size: 110%;
  background: #F8F8FF;
}

table, .content {
  width: 30%;
  margin: 0px auto;
  padding: 20px;
  border: 1px solid #B0C4DE;
  background: white;
  border-radius: 0px 0px 10px 10px;
  border-spacing: 5px 5px;
  border-collapse: separate;
}
</style>

<body>
    
    <?php
    include('connection.php');
    $fName = mysqli_real_escape_string($con,$_GET['fName']);
    $lName = mysqli_real_escape_string($con,$_GET['lName']);
    $position = mysqli_real_escape_string($con,$_GET['position']);
    $department = mysqli_real_escape_string($con,$_GET['department']);
    ?>
    
    <?php
    include('navigate_bar.php');
    ?>
    <br>
    <h2 align = "center"><strong>Information Form</strong></h2>
    <section style="padding-top: 0px;">
        <br>
        <table width="500" border="0" cellpadding="3" cellspacing="1" align = "center">
        <tr>
            <form method="post" action="recruit_confirm_update.php">
                <div data-role="fieldcontain">
                <td width="200"><strong>Firstname :</strong></td>
                <td width="450"> <input type="hidden" name="fName" value="<?php echo $fName?>" /> <?php echo "$fName";?> </td>
        </tr>
        <tr>
            <td width="200"><strong>Lastname :</strong></td>
            <td width="450"> <input type="hidden" name="lName" value="<?php echo $lName?>" /> <?php echo "$lName";?> </td>
        </tr>
        <tr>
            <td width="200"><strong>EmployeeID :</strong></td>
            <td width="450"><input type="text" required name="employeeID" value="" /></td>
        </tr>
        <tr>
            <td width="200"><strong>Phone Number :</strong></td>
            <td width="450"><input type="text" required name="phonenumber" value="" /></td>
        </tr>
        <tr>
            <td width="200"><strong>Account Number :</strong></td>
            <td width="450"><input type="text" required name="accountnumber" value="" /></td>
        </tr>
        <tr>
            <td width="200"><strong>Date of Birth :</strong></td>
            <td width="450"><input type="date" required name="dateofbirth" value="" /></td>
        </tr>
        <td width="800"><br><strong>Education History</strong></td>
        <tr>
            <td width="200"><strong>StudentID :</strong></td>
            <td width="450"><input type="text" required name="studentID" value="" /></td>
        </tr>
        <tr>
            <td width="200"><strong>schoolName :</strong></td>
            <td width="450"><input type="text" required name="schoolName" value="" /></td>
        </tr>
        <tr>
            <td width="200"><strong>GPA :</strong></td>
            <td width="450"><input type="text" required name="gpa" value="" /></td>
        </tr>
        <tr>
            <td width="200"><strong>Graduation Date :</strong></td>
            <td width="450"><input type="date" required name="graddate" value="" /></td>
        </tr>
        <td width="200"><br><strong>Work History</strong></td>
        <td width="300" style = "font-size: 90%;"><br>(Optional)</td>
        <tr>
            <td width="200"><strong>Former Corp. :</strong></td>
            <td width="450"><input type="text" name="formercorp" value="" /></td>
        </tr>
        <tr>
            <td width="200"><strong>Latest Position :</strong></td>
            <td width="450"><input type="text" name="latestposition" value="" /></td>
        </tr>
        <tr>
            <td width="200"><strong>Quit Date :</strong></td>
            <td width="450"><input type="date" name="quitdate" value="" /></td>
        </tr>
        <td width="200"><br><strong>Sign Name : </strong></td>
        <td width="450" style="padding-top: 30px;"><?php echo "$_SESSION[username]"?></td>
        </tr>
        <tr>
            <td width="200"><strong>Notice : </strong></td>
            <td width="450"><input type="text" name="notice" value="" /></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="hidden" name="position" value="<?php echo $position?>" />
                <input type="hidden" name="department" value="<?php echo $department?>" />
                <br>
                <button data-theme="b" id="submit" type="submit" class = "btn btn-success" style = "margin: 0 auto; display: block;">Submit</button>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h3 id="notification"></h3>
            </td>
            </div>
            </form>
        </tr>
        
    </table>
    <br>
        <button onclick="window.location.href='recruitpage.php'" class = "btn btn-dark" style = "margin: 0 auto; display: block;">Back</button>
    </section>
    <?php
	    include('include_js.php');
    ?>
    <br>
</body>
</html>