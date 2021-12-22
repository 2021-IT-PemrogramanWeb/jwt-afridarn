<?php
if (isset($_COOKIE["user_session"])) {
  $jwt = $_COOKIE["user_session"];
  $id = $_COOKIE["id"];
} else {
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
  <h1>User home</h1>
  <h3>Your data:</h3>
  <div class="lists"></div>
  <a href="edit_data.php">Edit data</a><br><br>

  <a href="logout.php">Logout</a>

  <p class="jwt" hidden><?php echo $jwt ?></p>
  <p class="id" hidden><?php echo $id ?></p>

  <script>
    const jwt = document.querySelector(".jwt").innerText;
    const id = document.querySelector(".id").innerText;
    const listsEl = document.querySelector(".lists");

    var requestOptions = {
      method: 'GET',
      redirect: 'follow'
    };

    fetch(`http://localhost/jwt-afridarn/api/users.php?jwt=${jwt}&id=${id}`, requestOptions)
      .then(response => response.json())
      .then(result => {
        listsEl.innerHTML = `
          <ul>
            <li>Nama: ${result.data.nama}</li>
            <li>Username: ${result.data.username}</li>
            <li>Email: ${result.data.email}</li>
            <li>Password: ${result.data.password}</li>
          </ul>
        `;
      })
      .catch(error => console.log('error', error));
  </script>
</body>

</html>