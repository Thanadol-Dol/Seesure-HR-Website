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
<!DOCTYPE html>
<html>
<title>SEESURE HR</title>
<head>
    <?php
        include('include_css.php');
    ?>
    <style>
        button{
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            -webkit-transition-duration: 0.4s;
            transition-duration: 0.4s;
        }
        button:hover{
            background-color: red;
        }
        .bt1 {
            background-color: red;
        }
        .bt2 {
            background-color: #4CAF50;
        }
        .bt3 {
            display: block;
            background-color: #295EDA;
            float: right;
        }
        .center{
            margin: 0;
            position: absolute;
            top: 50%;
            left: 50%;
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
        }
        .Seesure{
            margin-top: 200px;
            text-align: center;
        }
        .team-image{
            border-radius: 100%;
            height: 300px;
            width: 300px;
            margin-bottom: 50px;
        }
        #hero {
            width: 100%;
            height: calc(100vh - 110px);
            background: url("photo/background_2.png") top center;
            background-size: cover;
            position: relative;
        }
        h2{
            font-family: 'Poppins';
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
    <section id="hero" class="d-flex align-items-center">
    <div class="container position-relative" data-aos="fade-up" data-aos-delay="500">
      <h1>SEESURE CORP.</h1>
      <h2>Human Resource</h2>
    </div>
    </section>
    <section id="team" class="team" style = "text-align: center;" data-aos="zoom-in">
        <h1 style = "margin-bottom:50px;">Seesure Team</h1>
        <div id="carouselControls" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
            <img class="team-image" src="photo/Kunanon.png" alt="Kunanon">
              <h2>63070501011 KUNANON SUPMAMUL</h2>
            </div>
            <div class="carousel-item">
              <img class="team-image" src="photo/Napattharak.png" alt="Napattharak">
              <h2>63070501018 NAPATTHARAK MUANTOEY</h2>
            </div>
            <div class="carousel-item">
              <img class="team-image" src="photo/Tawan.png" alt="Tawan">
              <h2>63070501026 TAWAN THAEPPRASIT</h2>
            </div>
            <div class="carousel-item">
              <img class="team-image" src="photo/Thanadol.jpg" alt="Thanadol">
              <h2>63070501029 THANADOL THONGRIT</h2>
            </div>
            <div class="carousel-item">
              <img class="team-image" src="photo/Napat.png" alt="Napat">
              <h2>63070501038 NAPAT WAREEDEE</h2>
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselControls" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselControls" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
    </section>
    <?php
        include('include_js.php');
    ?>
    
</body>

</html>