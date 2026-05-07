<?php

namespace App\helper;

include "./vendor/autoload.php";

use App\model\SoalInfoModel;
use App\model\SoalListModel;
use App\model\SoalListPilModel;
use App\model\SoalJadwalModel;
use App\model\TestSoalListModel;
use App\model\TestSoalListPilModel;
use App\model\MemberTesModel;
use App\model\TestMemberTesModel;
use App\model\TestListTestModel;
use App\model\ListTestModel;
use App\utils\WebUtils;
use App\utils\JsonUtils;

class ulanganHelper {
  public static function setupUlangan() {
    // parse soal_list & soal_list_pil
    $soalPil = WebUtils::parseSoaListSoalListPil();
    $soal = $soalPil["soal"];
    $pil = $soalPil["pil"];

    // soal_info
    SoalInfoModel::createSoalInfo();

    // soal_list
    SoalListModel::createSoalList($soal);

    // soal_list_pil
    SoalListPilModel::createSoalListPil($pil);

    // soal_jadwal
    SoalJadwalModel::createSoalJadwal();

    // test_soal_list
    TestSoalListModel::createTestSoalList($soal);

    // test_soal_list_pil
    TestSoalListPilModel::createTestSoalListPil($pil);
  }

  public static function clenupUlangan() {
    // delete all soal
    SoalInfoModel::deleteSoalInfo();
    SoalListModel::deleteSoalList();
    SoalListPilModel::deleteSoalListPil();
    SoalJadwalModel::deleteSoalJadwal();
    TestSoalListModel::deleteTestSoalList();
    TestSoalListPilModel::deleteTestSoalListPil();

    // delete member_tes
    MemberTesModel::deleteMemberTes();

    // delete test_member_tes
    TestMemberTesModel::deleteTestMemberTes();

    // delete test_list_test
    TestListTestModel::deleteTestListTest();

    // delete list_test
    ListTestModel::deleteListTest();
  }

  public static function cleanupPengerjaan() {
    // delete member_tes
    MemberTesModel::deleteMemberTes();

    // delete test_member_tes
    TestMemberTesModel::deleteTestMemberTes();

    // delete list_test
    TestListTestModel::deleteTestListTest();

    // delete list_test
    ListTestModel::deleteListTest();
  }

  public static function insertSebagianJawaban() {
    $dataSoal = JsonUtils::readJson('./tests/Data/soal_list_soal_list_pil.json');
    $insertData = [];

    $user = WebUtils::getDummyUser();
    $idUser = $user["id"];
    $keyMember = $user["keyMember"];
    $idSoal = WebUtils::getSoalInfoProperty("id_info_soal");

    // parse
    for($i = 0; $i < count($dataSoal) - 5; $i++) {
      $keyListTest = "";
      for($z = 0; $z < count($dataSoal[$i]["pilihan_ganda"]); $z++) {
        if($dataSoal[$i]["pilihan_ganda"][$z]["list_pil"] == $dataSoal[$i]["jawaban"]) {
          $keyListTest = $dataSoal[$i]["pilihan_ganda"][$z]["key_list_pil"];
        }
      }
      $number = $dataSoal[$i]["number"];
      $keyListSoal = $dataSoal[$i]["key_list"];
      $jawabanTrue = $dataSoal[$i]["jawaban"];
      $jawaban = $jawabanTrue;
      $statusTest = 1;
      $nilaiTest = $jawaban === $jawabanTrue ? 1 : 0;
      $inserData[] = [
        "key_list_test" => $keyListTest,
        "id_member" => $idUser,
        "key_member" => $keyMember,
        "number" => $number,
        "id_info_soal" => $idSoal,
        "key_list_soal" => $keyListSoal,
        "jawaban" => $jawaban,
        "jawabant" => $jawabanTrue,
        "status_test" => $statusTest,
        "nilai_test" => $nilaiTest
      ];
    }

    // insert
    for($a = 0; $a < count($inserData); $a++) {
      TestListTestModel::insertOneTestListTest($inserData[$a]);
    }
  }

  public static function insertSemuaJawaban() {
    $dataSoal = JsonUtils::readJson('./tests/Data/soal_list_soal_list_pil.json');
    $insertData = [];

    $user = WebUtils::getDummyUser();
    $idUser = $user["id"];
    $keyMember = $user["keyMember"];
    $idSoal = WebUtils::getSoalInfoProperty("id_info_soal");

    // parse
    for($i = 0; $i < count($dataSoal); $i++) {
      $keyListTest = "";
      for($z = 0; $z < count($dataSoal[$i]["pilihan_ganda"]); $z++) {
        if($dataSoal[$i]["pilihan_ganda"][$z]["list_pil"] == $dataSoal[$i]["jawaban"]) {
          $keyListTest = $dataSoal[$i]["pilihan_ganda"][$z]["key_list_pil"];
        }
      }
      $number = $dataSoal[$i]["number"];
      $keyListSoal = $dataSoal[$i]["key_list"];
      $jawabanTrue = $dataSoal[$i]["jawaban"];
      $jawaban = $jawabanTrue;
      $statusTest = 1;
      $nilaiTest = $jawaban === $jawabanTrue ? 1 : 0;
      $inserData[] = [
        "key_list_test" => $keyListTest,
        "id_member" => $idUser,
        "key_member" => $keyMember,
        "number" => $number,
        "id_info_soal" => $idSoal,
        "key_list_soal" => $keyListSoal,
        "jawaban" => $jawaban,
        "jawabant" => $jawabanTrue,
        "status_test" => $statusTest,
        "nilai_test" => $nilaiTest
      ];
    }

    // insert
    for($a = 0; $a < count($inserData); $a++) {
      TestListTestModel::insertOneTestListTest($inserData[$a]);
    }
  }

  public static function setUlanganTime() {
    // data pada test_member_tes harus ada terlebih dahulu

    $testMemberTest = TestMemberTesModel::getTestMemberTes();
    $dateStart = $testMemberTest["date_start"];
    $timeStart = $testMemberTest["time_start"];

    $epoch = strtotime("$dateStart $timeStart");
    $newEpoch = $epoch + (15 * 60);
    $finalString = date("Y-m-d H:i:s", $newEpoch);
    $arrFinalString = explode(" ", $finalString);
    $newDateStart = $arrFinalString[0];
    $newTimeStart = $arrFinalString[1];

    TestMemberTesModel::updateDateTimeTestMemberTes($newDateStart, $newTimeStart);
  }
}