<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils;
use App\utils\JsonUtils;

class ListTestModel extends DBConnection {
  public static function deleteListTest() {
    // connect
    self::connect();

    // read data in json
    $soalInfo = JsonUtils::readJson('./tests/Data/soal_info.json');
    $idSoal = $soalInfo['id_info_soal'];

    // sql
    $dummyUser = WebUtils::getDummyUser();
    $keyMember = $dummyUser["keyMember"];
    $sqlDeleteTestListTest = "DELETE FROM list_test WHERE key_member='" . $keyMember . "' OR id_info_soal=" . $idSoal . ";";
    mysqli_query(self::$connection, $sqlDeleteTestListTest);

    // disconnect
    self::disconnect();
  }
}