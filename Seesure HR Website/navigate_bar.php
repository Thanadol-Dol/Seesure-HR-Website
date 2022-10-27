<?php 
  session_start(); 

  if (!isset($_SESSION['username'])) {
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
?>
<style>
    .nav-link:hover{
        color:white;
    }
    .navigate{
        margin: 0px 100px;
    }
</style>
  <section id="topbar" class="d-flex align-items-center">
    <div class="container d-flex justify-content-center justify-content-md-between">
      <div class="d-flex align-items-center">
      </div>
      <div class="d-none d-md-block">
          <?php
        echo "<p style = 'margin-bottom: 0px;'>Welcome <strong>$_SESSION[username]</strong> : $_SESSION[Name]</p>"
            ?>
      </div>
    </div>
  </section>
  <header id="header" class="d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between navigate">

      <h1 class="logo"><a href="index.php">SEESURE</a></h1>
      <nav id="navbar" class="navbar">
        <ul>
         <li class="dropdown"><a href="#"><span>Manage</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="mentorselection.php">Mentor Selection</a></li>
              <li><a href="delete_employee.php">Delete Employee</a></li>
              <li><a href="relocate_empselect.php">Relocate Employee</a></li>
              <li><a href="manage_position.php">Manage Position</a></li>
            </ul>
          </li>
          <li class="dropdown"><a href="#"><span>Analyze</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="Dep_Branch_Late.php">Top 5 Department&Branch Late Absent</a></li>
              <li><a href="salary_manage_page.php">Salary Management</a></li>
            </ul>
          </li>
          <li><a class="nav-link scrollto" href="employee_list.php">Employee List</a></li>
          <li><a class="nav-link scrollto" href="recruitpage.php">Recruit Employee</a></li>
          <li><a class="nav-link scrollto" href="index.php?logout='1'" style = ""><strong>Log Out</strong></a></li>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>

    </div>
  </header>