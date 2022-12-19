<?php
  $hostname = "localhost";
  $username = "root";
  $password = "";
  $dbname = "webchat";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){
    echo "Database không thể kết nối".mysqli_connect_error();
  }
?>
