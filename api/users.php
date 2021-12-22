<?php
include("connection.php");
$request_method = $_SERVER["REQUEST_METHOD"];
require __DIR__ . '/vendor/autoload.php';
$secret_key = "afridacantik";

error_reporting(0);
ini_set('display_errors', 0);

function generateToken($jwt)
{
  global $secret_key;
  $payload = \Firebase\JWT\JWT::decode($jwt, $secret_key, ['HS256']);
  return $payload;
}

//////////////GET
if ($request_method === "GET") {

  // for user & admin
  if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $jwt = $_GET["jwt"];
    $payload = generateToken($jwt);

    if (($payload->role === "user" && $payload->id === $id) || $payload->role === "admin") {
      $query = mysqli_query($connect, "SELECT * from user WHERE user_id = $id");

      if ($query) {
        $results = mysqli_fetch_all($query, MYSQLI_ASSOC);

        if ($results) {
          foreach ($results as $result) {
            $item = array(
              "id" => $result["user_id"],
              "nama" => $result["user_nama"],
              "username" => $result["user_username"],
              "email" => $result["user_email"],
              "password" => $result["user_password"]
            );
          }

          $response = [
            "status" => "Ok",
            "msg" => "Succes get data",
            "data" => $item,
          ];
        } else {
          $response = array(
            "status" => "Bad",
            "msg" => "Failed get data"
          );
        }
        echo json_encode($response);
      } else {
        echo "Something wrong";
      }
    } else {
      echo "You cannot access this";
    }
  }

  //access all for admin
  else {
    $jwt = $_GET["jwt"];
    $payload = generateToken($jwt);

    if ($payload->role === "admin") {
      $query = mysqli_query($connect, "SELECT * from user");
      $results = mysqli_fetch_all($query, MYSQLI_ASSOC);

      foreach ($results as $result) {
        $item[] = array(
          "id" => $result["user_id"],
          "nama" => $result["user_nama"],
          "username" => $result["user_username"],
          "email" => $result["user_email"],
          "password" => $result["user_password"]
        );
      }

      if ($query) {
        $response = array(
          "status" => "Ok",
          "msg" => "Success get data",
          "data" => $item
        );
      } else {
        $response = array(
          "status" => "Bad",
          "msg" => "Failed get data"
        );
      }

      echo json_encode($response);
    } else {
      echo "You cannot access this!";
    }
  }
}

////////////POST
else if ($request_method === "POST") {
  $nama = isset($_POST["nama"]) ? $_POST["nama"] : "";
  $username = isset($_POST["username"]) ? $_POST["username"] : "";
  $email = isset($_POST["email"]) ? $_POST["email"] : "";
  $password = isset($_POST["password"]) ? $_POST["password"] : "";

  $password = password_hash($password, PASSWORD_DEFAULT);
  // var_dump($password);
  $query_str = "INSERT INTO user (user_nama, user_username, user_email, user_password) VALUES('$nama', '$username', '$email', '$password')";
  // var_dump($query_str);
  $query = mysqli_query($connect, $query_str, $result);

  // var_dump($result);
  if ($query) {
    $response = array(
      "status" => "Ok",
      "msg" => "Success insert data"
    );
  } else {
    $response = array(
      "status" => "Bad",
      "msg" => "Failed insert data"
    );
  }

  echo json_encode($response);
}

/////////////////PUT
else if ($request_method === "PUT") {
  $id = $_GET["id"];
  $jwt = $_GET["jwt"];
  $payload = generateToken($jwt);

  if (($payload->role === "user" && $payload->id === $id) || $payload->role === "admin") {
    parse_str(file_get_contents('php://input'), $_PUT);

    $nama = isset($_PUT["nama"]) ? $_PUT["nama"] : "";
    $username = isset($_PUT["username"]) ? $_PUT["username"] : "";
    $email = isset($_PUT["email"]) ? $_PUT["email"] : "";
    $password = isset($_PUT["password"]) ? $_PUT["password"] : "";

    $password = password_hash($password, PASSWORD_DEFAULT);

    $query = mysqli_query($connect, "UPDATE user SET user_nama = '$nama', user_username = '$username', user_email = '$email', user_password = '$password' WHERE user_id = $id");

    if ($query) {
      $response = array(
        "status" => "Ok",
        "msg" => "Success update data"
      );
    } else {
      $response = array(
        "status" => "Bad",
        "msg" => "Failed update data"
      );
    }

    echo json_encode($response);
  } else {
    echo "You cannot access this";
  }
}

//////////////DELETE
else if ($request_method === "DELETE") {
  $id = $_GET["id"];
  $jwt = $_GET["jwt"];
  $payload = generateToken($jwt);

  if ($payload->role === "admin") {
    $query = mysqli_query($connect, "DELETE FROM `user` WHERE `user`.`user_id` = $id");
    if ($query) {
      $response = array(
        "status" => "Ok",
        "msg" => "Success delete data"
      );
    } else {
      $response = array(
        "status" => "Bad",
        "msg" => "Failed delete data"
      );
    }
    echo json_encode($response);
  } else {
    echo "You cannot access this!";
  }
}
