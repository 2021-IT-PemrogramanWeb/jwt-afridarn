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
  <title>Create Account</title>
  <style>
    label,
    textarea {
      display: block;
    }

    input,
    textarea {
      margin-bottom: 20px;
    }
  </style>
</head>

<body>

  <h1>CREATE USER</h1>

  <form action="" method="POST">
    <label for="nama">Nama:</label>
    <input type="text" name="nama" id="nama" class="nama" required><br>

    <label for="username">Username:</label>
    <input type="text" name="username" id="username" class="username" required><br>

    <label for="umur">Email:</label>
    <input type="text" name="email" id="email" class="email" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" class="password1" required><br>

    <span class="notsame" hidden style="color: red;">Konfirmasi password tidak sama</span>
    <label for="password2">Konfirmasi password:</label>
    <input type="password" name="password2" id="password2" class="password2" required><br>

    <input type="submit" value="create" name="register" class="register">
  </form>

  <script>
    const registerBtn = document.querySelector(".register");
    const namaInput = document.querySelector(".nama");
    const usernameInput = document.querySelector(".username");
    const emailInput = document.querySelector(".email");
    const password1Input = document.querySelector(".password1");
    const password2Input = document.querySelector(".password2");

    registerBtn.addEventListener("click", (event) => {
      event.preventDefault();

      if (namaInput.value !== "" && usernameInput.value !== "" && emailInput.value !== "" && password1Input.value !== "" && password2Input.value !== "") {
        if (password1Input.value === password2Input.value) {
          var myHeaders = new Headers();
          myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

          var urlencoded = new URLSearchParams();
          urlencoded.append("nama", namaInput.value);
          urlencoded.append("username", usernameInput.value);
          urlencoded.append("email", emailInput.value);
          urlencoded.append("password", password1Input.value);
          urlencoded.append("motivasi", motivasiInput.value);

          var requestOptions = {
            method: 'POST',
            headers: myHeaders,
            body: urlencoded,
            redirect: 'follow'
          };

          fetch("http://localhost/jwt-afridarn/api/users.php", requestOptions)
            .then(response => response.text())
            .then(result => console.log(result))
            .catch(error => console.log('error', error));

          alert("Create user account success");
          window.location.reload(true);
        } else {
          document.querySelector(".notsame").removeAttribute("hidden");
        }
      }
    });
  </script>
</body>

</html>