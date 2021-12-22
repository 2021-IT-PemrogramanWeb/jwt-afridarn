<?php
include("api/function.php");

if ($_COOKIE["admin_session"]) {
  $jwt = $_COOKIE["admin_session"];
} else {
  header("Location: index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Show All User</title>
</head>

<body>

  <h1>SHOW ALL USER</h1>
  <div class="lists"></div>

  <a href="logout.php">Logout</a>

  <p hidden class="jwt"><?php echo $jwt; ?></p>

  <script>
    const jwt = document.querySelector(".jwt").innerText;
    const listsEl = document.querySelector(".lists");

    var requestOptions = {
      method: 'GET',
      redirect: 'follow'
    };

    fetch(`http://localhost/jwt-afridarn/api/users.php?jwt=${jwt}`, requestOptions)
      .then(response => response.json())
      .then(results => {
        results.data.forEach(item => {
          const data = document.createElement("div");
          data.innerHTML = `
            <ul>
              <li>Nama: ${item.nama}</li>
              <li>Username: ${item.username}</li>
              <li>Email: ${item.email}</li>
              <li>Password: ${item.password}</li>
              <a href="edit_data.php?id=${item.id}">Edit this data</a><br>
              <button class="delete" onclick="deleteData(${item.id})">Delete this data</button>
            </ul><br>
          `;
          listsEl.appendChild(data);
        });
      })
      .catch(error => console.log('error', error));

    function deleteData(id) {
      if (confirm("Press a button!") === true) {
        var requestOptions = {
          method: 'DELETE',
          redirect: 'follow'
        };

        fetch(`http://localhost/jwt-afridarn/api/users.php?id=${id}&jwt=${jwt}`, requestOptions)
          .then(response => response.text())
          .then(result => console.log(result))
          .catch(error => console.log('error', error));

        alert("Delete user account success");
        window.location.reload(true);
      }
    }
  </script>
</body>

</html>