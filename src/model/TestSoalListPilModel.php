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

  // check test_soal_list_pil
  public static function checkTestSoalListPil() {
    // connect
    self::connect();

    // query read
    $idSoalInfo = (int)WebUtils::getSoalInfoProperty("id_info_soal");
    $sqlSelectTestSoalListPil = "SELECT * FROM test_soal_list_pil WHERE id_info_soal = $idSoalInfo;";
    $querySelectTestSoalListPil = mysqli_query(self::$connection, $sqlSelectTestSoalListPil);

    // disconnect
    self::disconnect();

    // return existance
    if (mysqli_num_rows($querySelectTestSoalListPil) > 0) {
      return true;
    } else {
      return false;
    }
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

}