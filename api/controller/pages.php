<?php
require_once './config/db.php';
require_once './config/config.php';
require_once './vendor/autoload.php';

use Firebase\JWT\JWT;

class User
{
  private $conn;
  private $db;
  private $username;
  private $email;
  private $password;
  private $role;
  public function __construct()
  {
    $this->db = new Database;
    $this->conn = $this->db->connect();
  }
  public function initFunction($func, $data)
  {
    if (method_exists($this, $func)) {
      $this->$func($data);
    } else {
      $this->printError(404, "Function $func tidak ditemukan");
    }
  }


  private function login($data)
  {
    if (!empty($data['email']) && !empty($data['password'])) {
      $this->email = $data['email'];
      $statement = $this->conn->prepare("SELECT * FROM akun WHERE email = :email");
      $statement->bindParam(":email", $this->email);
      $statement->execute();
      $result = $statement->fetchAll();
      $result = $result[0];
      if (!empty($result)) {
        $this->password = $result[$this->db->db_akun_password];
        $checkpass = password_verify($data['password'], $this->password);
        if ($checkpass) {
          $this->role = $result[$this->db->db_akun_role];
          $this->generateJWT($result[$this->db->db_akun_id]);
        } else {
          $this->printError(404, 'Password salah');
        }
      }

      echo json_encode($data);
      exit;
    } else {
      $this->printError(400, 'Email/Password kosong');
    }
  }


  private function generateJWT($id)
  {
    $token = [
      'iat' => time(),
      'exp' => time() + 7200,
      'iss' => $this->db->getServername(),
      'payload' => [
        'id' => $id,
        'email' => $this->email,
        'role' => $this->role,
      ]
    ];
    $jwt = JWT::encode($token, secretkey, ['ES384']);
    echo json_encode($jwt);
    exit;
  }


  private function decodeJWT($token)
  {
    try {
      $jwt = JWT::decode($token, secretkey, ['ES384']);
      if ($jwt->exp < time()) {
        $this->printError(400, 'Expired token');
      }
      return $jwt->data;
    } catch (\Exception $th) {
      $this->printError(400, 'Invalid token');
    }
  }


  public function getUser($id = null, $data)
  {
    if (!empty($data['token'])) {
      $token = $this->decodeJWT($data['token']);
      if ($token->role == 'admin' || $token->id == $id) {
        if ($id == null) {
          $statement = $this->conn->query("SELECT * FROM akun");
          $data = $statement->fetchAll();
          if (!empty($data)) {
            $this->response($data);
          } else {
            $this->printError(400, "Akun empty");
          }
        }
        $statement = $this->conn->prepare("SELECT * FROM akun WHERE id_akun = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();
        $data = $statement->fetchAll();
        if (!empty($data)) {
          $this->response($data);
        } else {
          $this->printError(404, "Akun dengan ID tersebut tidak ditemukan");
        }
      } else {
        $this->printError(403, 'Tidak dapat mengakses Function');
      }
    } else {
      $this->printError(400, 'Token kosong');
    }
  }


  public function getAllUser($data)
  {
    if (!empty($data['token'])) {
      $token = $this->decodeJWT($data['token']);
      if ($token->role == 'admin') {
        $statement = $this->conn->query("SELECT * FROM akun");
        $result = $statement->fetchAll();
        echo json_encode($result);
      } else {
        $this->printError(403, 'Tidak dapat mengakses Function');
      }
    } else {
      $this->printError(400, 'Token kosong');
    }
  }


  public function createUser($data)
  {
    if (!empty($data['token'])) {
      $token = $this->decodeJWT($data['token']);

      if ($token->role == 'admin') {
        if (!empty($data['email']) && !empty($data['password']) && !empty($data['role'])) {
          $statement = $this->conn->prepare("SELECT * FROM akun WHERE email = :email");
          $statement->bindParam(":email", $data['email']);
          $statement->execute();
          $result = $statement->fetchAll();
          if (empty($result)) {
            $this->email = $data['email'];
            $this->role = $data['role'];
            $this->password = $data['password'];
            $statement = $this->conn->prepare("INSERT INTO akun SET email = :email, password = :pass, status = :role");
            $statement->bindParam(":email", $this->email);
            $statement->bindParam(":pass", $this->password);
            $statement->bindParam(":role", $this->role);
            $statement->execute();
            if (empty($statement)) {
              $this->printError(400, "Gagal membuat akun");
            } else {
              $response = [
                'message' => 'Buat akun sukses',

              ];
              $this->response($response);
            }
          } else {
            $this->printError(400, "Email sudah digunakan");
          }
        } else {
          $this->printError(400, "Email/password error");
        }
      } else {
        $this->printError(403, 'Tidak dapat mengakses Function');
      }
    } else {
      $this->printError(400, 'Token kosong');
    }
  }


  public function updateUser($id, $data)
  {
    if (!empty($data['token'])) {
      $token = $this->decodeJWT($data['token']);
      if ($token->role == 'admin' || $token->id == $id) {
        if (!empty($data['email']) && !empty($data['password']) && !empty($id)) {
          $this->email = $data['email'];
          $this->password = $data['password'];
          $statement = $this->conn->prepare("SELECT * FROM akun WHERE id_akun = :id");
          $statement->bindParam(":id", $id);
          $statement->execute();
          $result = $statement->fetchAll();
          if (!empty($result)) {
            $statement = $this->conn->prepare("UPDATE akun SET email = :email, password = :pass WHERE id_akun = :id");
            $statement->bindParam(":email", $this->email);
            $statement->bindParam(":pass", $this->password);
            $statement->bindParam(":id", $id);
            if ($statement->execute()) {
              $response = [
                'message' => "Updated user success.",
              ];
              $this->response($response);
            } else {
              $this->printError(400, "Update akun gagal");
            }
          } else {
            $this->printError(404, "ID Akun tidak ditemukan");
          }
        } else {
          $this->printError(400, "Parameter ID tidak ditemukan");
        }
      } else {
        $this->printError(403, 'Tidak dapat mengakses Function');
      }
    } else {
      $this->printError(400, 'Token kosong');
    }
  }


  public function deleteUser($id = null, $data)
  {
    if (!empty($data['token'])) {
      $token = $this->decodeJWT($data['token']);
      if ($token->role == 'admin') {
        if (!empty($id)) {
          $statement = $this->conn->prepare("DELETE FROM akun WHERE id_akun = :id");
          $statement->bindParam(":id", $id);
          if ($statement->execute()) {
            $response = [
              'message' => "User Deleted",
            ];
            $this->response($response);
          } else {
            $this->printError(400, "Hapus akun gagal");
          }
        } else {
          $this->printError(400, "Parameter ID tidak ditemukan");
        }
      } else {
        $this->printError(403, 'Tidak dapat mengakses Function');
      }
    } else {
      $this->printError(400, 'Token kosong');
    }
  }


  public function printError($code, $msg)
  {
    http_response_code($code);
    $message = [
      'status' => $code,
      'message' => $msg
    ];
    echo json_encode($message);
    exit;
  }


  public function response($data)
  {
    http_response_code(200);
    $message = [
      'status' => 200,
      'data' => $data
    ];
    echo json_encode($message);
    exit;
  }
}
