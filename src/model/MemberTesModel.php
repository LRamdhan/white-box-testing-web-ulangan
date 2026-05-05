<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils;

class MemberTesModel extends DBConnection {
  public static function deleteMemberTes() {
    $dummyUser = WebUtils::getDummyUser();
    $keyMember = $dummyUser["keyMember"];

    // connect
    self::connect();

    // query
    $sqlDeleteMemberTes = "DELETE FROM member_tes WHERE key_member = '$keyMember';";
    mysqli_query(self::$connection, $sqlDeleteMemberTes);

    // disconnect
    self::disconnect();
  }

  public static function insertMemberTesTemp() {
    // connect
    self::connect();

    $dummyUser = WebUtils::getDummyUser();
    $id_member = $dummyUser["id"];
    $key_member = $dummyUser["nis"];
    $nis = $dummyUser["nis"];
    $nama = $dummyUser["nama"];
    $kelas = $dummyUser["kelas"];
    $id_info_soal = WebUtils::getSoalInfoProperty("id_info_soal");
    $status_start = 1;
    $status_run = 1;
    $status_end = 1;

    $sql = "INSERT INTO member_tes (id_member, key_member, nis, nama, kelas, id_info_soal, status_start, status_run, status_end) VALUES ($id_member, '$key_member', '$nis', '$nama', '$kelas', $id_info_soal, $status_start, $status_run, $status_end);";
    mysqli_query(self::$connection, $sql);

    // disconnect
    self::disconnect();
  }

}