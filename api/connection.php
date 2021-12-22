<?php
  $servername = "localhost";
  $usernameDB = "root";
  $passwordDB = "";
  $DBname = "misi14";

  $connect = mysqli_connect($servername, $usernameDB, $passwordDB, $DBname);
  if(!$connect) {
    exit("Gagal koneksi database!");
  }
?>
