<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils;

class UserModel extends DBConnection {
  public static function createUser() {
    $dummyUser = WebUtils::getDummyUser();

    // connect
    self::connect();

    // cek apakah dummy user sudah ada, jika ada hapus
    $querySelect = "SELECT * FROM user WHERE nis='" . $dummyUser["nis"] . "';";
    $selectUser = mysqli_query(self::$connection, $querySelect);
    if(mysqli_num_rows($selectUser) == 1) {
      $deleteExistUserQuery = "DELETE FROM user WHERE nis='" . $dummyUser["nis"] . "';";
      $deleteExistUser = mysqli_query(self::$connection, $deleteExistUserQuery);
    }

    // buat dummy user
    $queryInsert = "INSERT INTO user (key_member, nis, nama, id_kelas, kelas, jurusan, username, password) VALUES ('" . $dummyUser["keyMember"] . "', '" . $dummyUser["nis"] . "', '" .  $dummyUser["nama"] . "', " . $dummyUser["idKelas"] . ", '" . $dummyUser["kelas"] . "', '" . $dummyUser["jurusan"] . "', '" . $dummyUser["username"] . "', '" . $dummyUser["password"] . "');";
    mysqli_query(self::$connection, $queryInsert);

    // disconnect
    self::disconnect();
  }

  public static function deleteUser() {
    $dummyUser = WebUtils::getDummyUser();

    // connect
    self::connect();

    // cek apakah dummy user sudah ada, jika ada hapus
    $querySelect = "SELECT * FROM user WHERE nis='" . $dummyUser["nis"] . "';";
    $selectUser = mysqli_query(self::$connection, $querySelect);
    if(mysqli_num_rows($selectUser) == 1) {
      $deleteExistUserQuery = "DELETE FROM user WHERE nis='" . $dummyUser["nis"] . "';";
      $deleteExistUser = mysqli_query(self::$connection, $deleteExistUserQuery);
    }

    // disconnect
    self::disconnect();
  }

  public static function createUser2() {
    $dummyUser = WebUtils::getDummyUser2();

    // connect
    self::connect();

    // cek apakah dummy user sudah ada, jika ada hapus
    $querySelect = "SELECT * FROM user WHERE nis='" . $dummyUser["nis"] . "';";
    $selectUser = mysqli_query(self::$connection, $querySelect);
    if(mysqli_num_rows($selectUser) == 1) {
      $deleteExistUserQuery = "DELETE FROM user WHERE nis='" . $dummyUser["nis"] . "';";
      $deleteExistUser = mysqli_query(self::$connection, $deleteExistUserQuery);
    }

    // buat dummy user
    $queryInsert = "INSERT INTO user (key_member, nis, nama, id_kelas, kelas, jurusan, username, password) VALUES ('" . $dummyUser["keyMember"] . "', '" . $dummyUser["nis"] . "', '" .  $dummyUser["nama"] . "', " . $dummyUser["idKelas"] . ", '" . $dummyUser["kelas"] . "', '" . $dummyUser["jurusan"] . "', '" . $dummyUser["username"] . "', '" . $dummyUser["password"] . "');";
    mysqli_query(self::$connection, $queryInsert);

    // disconnect
    self::disconnect();
  }

  public static function deleteUser2() {
    $dummyUser = WebUtils::getDummyUser2();

    // connect
    self::connect();

    // cek apakah dummy user sudah ada, jika ada hapus
    $querySelect = "SELECT * FROM user WHERE nis='" . $dummyUser["nis"] . "';";
    $selectUser = mysqli_query(self::$connection, $querySelect);
    if(mysqli_num_rows($selectUser) == 1) {
      $deleteExistUserQuery = "DELETE FROM user WHERE nis='" . $dummyUser["nis"] . "';";
      $deleteExistUser = mysqli_query(self::$connection, $deleteExistUserQuery);
    }

    // disconnect
    self::disconnect();
  }

}