<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils; 
use App\utils\JsonUtils;

class TestMemberTesModel extends DBConnection {
  public static function deleteTestMemberTes() {
    $dummyUser = WebUtils::getDummyUser();
    $keyMember = $dummyUser["keyMember"];

    // read data in json
    $soalInfo = JsonUtils::readJson('./tests/Data/soal_info.json');
    $idSoal = $soalInfo['id_info_soal'];

    // connect
    self::connect();

    // query
    $sqlDeleteTestMemberTes = "DELETE FROM test_member_tes WHERE key_member = '$keyMember' OR id_info_soal = $idSoal;";
    mysqli_query(self::$connection, $sqlDeleteTestMemberTes);

    // disconnect
    self::disconnect();
  }
  
  public static function getTestMemberTes() {
    $dummyUser = WebUtils::getDummyUser();
    $keyMember = $dummyUser["keyMember"];

    // connect
    self::connect();

    // query
    $sqlSelectTestMemberTes = "SELECT * FROM test_member_tes WHERE key_member = '$keyMember';";
    $selectTestMemberTesQuery = mysqli_query(self::$connection, $sqlSelectTestMemberTes);
    $testMembertes = mysqli_fetch_assoc($selectTestMemberTesQuery);

    // disconnect
    self::disconnect();

    return $testMembertes;
  }

  public static function updateDateTimeTestMemberTes($date, $time) {
    $dummyUser = WebUtils::getDummyUser();
    $keyMember = $dummyUser["keyMember"];

    // connect
    self::connect();

    // query
    $sqlUpdate = "UPDATE test_member_tes SET date_start='$date', time_start='$time' WHERE key_member = '$keyMember';";
    mysqli_query(self::$connection, $sqlUpdate);

    // disconnect
    self::disconnect();
  }
}