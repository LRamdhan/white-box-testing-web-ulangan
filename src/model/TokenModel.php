<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;

class TokenModel extends DBConnection {
  public static function getToken() {
    // connect
    self::connect();

    // query
    $sqlGetToken = "SELECT * FROM token WHERE id_token = 1;";
    $queryGetToken = mysqli_query(self::$connection, $sqlGetToken);

    // result
    $resultGetToken = mysqli_fetch_array($queryGetToken);

    // disconnect
    self::disconnect();

    // return
    return $resultGetToken;
  }
}