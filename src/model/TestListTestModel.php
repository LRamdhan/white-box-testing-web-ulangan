<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils;
use App\utils\JsonUtils;

class TestListTestModel extends DBConnection {
  public static function insertOneTestListTest($data) {
    // connect
    self::connect();

    // insert query
    $sqlInsert = "
      INSERT INTO test_list_test (
        key_list_test,
        id_member,
        key_member,
        number,
        id_info_soal,
        key_list_soal,
        jawaban,
        jawabant,
        status_test,
        nilai_test
      ) VALUES (
        '".$data['key_list_test']."',
        ".(int)$data['id_member'].",
        '".$data['key_member']."',
        ".(int)$data['number'].",
        ".(int)$data['id_info_soal'].",
        '".$data['key_list_soal']."',
        '".$data['jawaban']."',
        '".$data['jawabant']."',
        '".$data['status_test']."',
        ".(int)$data['nilai_test']."
      );
    ";
    mysqli_query(self::$connection, $sqlInsert);
 
    // disconnect
    self::disconnect();
  }

  public static function deleteTestListTest() {
    // connect
    self::connect();

    // read data in json
    $soalInfo = JsonUtils::readJson('./tests/Data/soal_info.json');
    $idSoal = $soalInfo['id_info_soal'];

    // sql
    $dummyUser = WebUtils::getDummyUser();
    $keyMember = $dummyUser["keyMember"];
    $sqlDeleteTestListTest = "DELETE FROM test_list_test WHERE key_member='" . $keyMember . "' OR id_info_soal=" . $idSoal . ";";
    mysqli_query(self::$connection, $sqlDeleteTestListTest);

    // disconnect
    self::disconnect();
  }
}