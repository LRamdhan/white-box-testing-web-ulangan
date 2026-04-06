<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use Tests\Data\DummyUser;

class UserModel extends DBConnection {
  public static function createUser() {
    // connect
    self::connect();

    // cek apakah dummy user sudah ada, jika ada hapus
    $querySelect = "SELECT * FROM user WHERE nis='" . DummyUser::$nis . "';";
    $selectUser = mysqli_query(self::$connection, $querySelect);
    if(mysqli_num_rows($selectUser) == 1) {
      $deleteExistUserQuery = "DELETE FROM user WHERE nis='" . DummyUser::$nis . "';";
      $deleteExistUser = mysqli_query(self::$connection, $deleteExistUserQuery);
    }

    // buat dummy user
    $queryInsert = "INSERT INTO user (key_member, nis, nama, id_kelas, kelas, jurusan, username, password) VALUES ('" . DummyUser::$keyMember . "', '" . DummyUser::$nis . "', '" .  DummyUser::$nama . "', " . DummyUser::$idKelas . ", '" . DummyUser::$kelas . "', '" . DummyUser::$jurusan . "', '" . DummyUser::$username . "', '" . DummyUser::$password . "');";
    mysqli_query(self::$connection, $queryInsert);

    // disconnect
    self::disconnect();
  }

  public static function deleteUser() {
    // connect
    self::connect();

    // cek apakah dummy user sudah ada, jika ada hapus
    $querySelect = "SELECT * FROM user WHERE nis='" . DummyUser::$nis . "';";
    $selectUser = mysqli_query(self::$connection, $querySelect);
    if(mysqli_num_rows($selectUser) == 1) {
      $deleteExistUserQuery = "DELETE FROM user WHERE nis='" . DummyUser::$nis . "';";
      $deleteExistUser = mysqli_query(self::$connection, $deleteExistUserQuery);
    }

    // disconnect
    self::disconnect();
  }
}