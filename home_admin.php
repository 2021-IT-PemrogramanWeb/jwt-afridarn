<?php
if (!$_COOKIE["admin_session"]) {
  header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
</head>

<body>
  </center>
  <h1>WELCOME ADMIN</h1>
  <a href="create_acc.php">Create user</a><br><br>
  <a href="show_all.php">Show all user</a><br><br><br><br>

  <a href="logout.php">LOG OUT</a>
  </center>
</body>

</html>