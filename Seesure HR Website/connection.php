<?php
 $con=mysqli_connect("localhost","a8_seesure_hr","z#o1tEnI8DBHvPf5","a8_SEESURE");
 mysqli_set_charset($con, "utf8");
 if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " .mysqli_connect_error();
}
?>