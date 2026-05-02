<?php

namespace App\model;

include "./vendor/autoload.php";

use App\conf\DBConnection;
use App\utils\WebUtils;

class TestSoalListPilModel extends DBConnection {
  // create test_soal_list_pil
  public static function createTestSoalListPil($pil) {
    // create sql insert
    $valueList = "";
    for($i = 0; $i < count($pil); $i++) {
      $coma = ($i == (count($pil) - 1)) ? "" : ",";
      $valueList .= "
        (
          ".(int)$pil[$i]["id_info_soal"].",
          '".$pil[$i]["key_list"]."',
          '".$pil[$i]["key_list_pil"]."',
          '".$pil[$i]["list_pil"]."',
          '".$pil[$i]["pilihan"]."',
          '".$pil[$i]["typepg"]."'
        )$coma
      ";
    }    
    $sqlInserTestSoalListPil = "
      INSERT INTO test_soal_list_pil (
        id_info_soal,
        key_list,
        key_list_pil,
        list_pil,
        pilihan,
        typepg
      ) VALUES $valueList;
    ";

    // connect
    self::connect();

    // execute
    mysqli_query(self::$connection, $sqlInserTestSoalListPil);

    // disconnect
    self::disconnect();
  }

  // delete test_soal_list_pil
  public static function deleteTestSoalListPil() {
    // connect
    self::connect();

    // query delete
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlDeleteTestSoalListPil = "DELETE FROM test_soal_list_pil WHERE id_info_soal = $idSoalInfo;";
    mysqli_query(self::$connection, $sqlDeleteTestSoalListPil);

    // disconnect
    self::disconnect();
  }

  public static function getTestSoalListPil($keysoal) {
    // connect
    self::connect();

    // query read
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlSelectTestSoalListPil = "SELECT * FROM test_soal_list_pil WHERE id_info_soal = $idSoalInfo AND key_list = '$keysoal';";
    $querySelectTestSoalListPil = mysqli_query(self::$connection, $sqlSelectTestSoalListPil);
    $testSoalListPil = [];
    while($raw = mysqli_fetch_assoc($querySelectTestSoalListPil)) {
      $testSoalListPil[] = $raw;
    }

    // disconnect
    self::disconnect();

    // return
    return $testSoalListPil;
  }
}