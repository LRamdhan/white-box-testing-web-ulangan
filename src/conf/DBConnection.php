<?php

namespace App\conf;

include "./loadenv.php";

class DBConnection {
  public static $connection;
  
  public static function connect() {
    self::$connection = mysqli_connect($_ENV["DB_HOST"], $_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"], $_ENV["DB_NAME"]);
  }

  public static function disconnect() {
    self::$connection->close();
  }
}