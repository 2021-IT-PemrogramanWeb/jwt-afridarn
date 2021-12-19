<?php
class Database
{
  private $servername = "localhost";
  private $username = "id17867174_afrida";
  private $password = "-Cis4@]whESoWqCY";
  private $database = "id17867174_pweb";
  public $db_akun = "user";
  public $db_akun_username = "username";
  public $db_akun_email = "email";
  public $db_akun_password = "password";
  public $db_akun_id = "id_akun";
  public $db_akun_role = "status";
  public function connect()
  {
    try {
      $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $conn;
    } catch (\Exception $e) {
      echo "Database can't connect: " . $e->getMessage();
    }
  }
  public function getServername()
  {
    return $this->servername;
  }
}
