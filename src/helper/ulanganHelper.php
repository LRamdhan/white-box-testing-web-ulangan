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
use App\utils\WebUtils;

class ulanganHelper {
  public static function setupUlangan() {
    // parse soal_list & soal_list_pil
    $soalPil = WebUtils::parseSoaListSoalListPil();
    $soal = $soalPil["soal"];
    $pil = $soalPil["pil"];

    // soal_info
    // $soalInfoExist = SoalInfoModel::checkSoalInfo();
    // if($soalInfoExist) {
    //   SoalInfoModel::deleteSoalInfo();
    // }
    SoalInfoModel::createSoalInfo();

    // soal_list
    // $soalListExist = SoalListModel::checkSoalList();
    // if($soalListExist) {
    //   SoalListModel::deleteSoalList();
    // }
    SoalListModel::createSoalList($soal);

    // soal_list_pil
    // $soalListPilExist = SoalListPilModel::checkSoalListPil();
    // if($soalListPilExist) {
    //   SoalListPilModel::deleteSoalListPil();
    // }
    SoalListPilModel::createSoalListPil($pil);

    // soal_jadwal
    // $soalJadwalExist = SoalJadwalModel::checkSoalJadwal();
    // if($soalJadwalExist) {
    //   SoalJadwalModel::deleteSoalJadwal();
    // }
    SoalJadwalModel::createSoalJadwal();

    // test_soal_list
    // $testSoalListExist = TestSoalListModel::checkTestSoalList();
    // if($testSoalListExist) {
    //   TestSoalListModel::deleteTestSoalList();
    // }
    TestSoalListModel::createTestSoalList($soal);

    // test_soal_list_pil
    // $testSoalListPilExist = TestSoalListPilModel::checkTestSoalListPil();
    // if($testSoalListPilExist) {
    //   TestSoalListPilModel::deleteTestSoalListPil();
    // }
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

    // delete jawaban
    TestListTestModel::deleteTestListTest();
  }

  public static function cleanupPengerjaan() {
    // delete member_tes
    MemberTesModel::deleteMemberTes();

    // delete test_member_tes
    TestMemberTesModel::deleteTestMemberTes();

    // delete jawaban
    TestListTestModel::deleteTestListTest();
  }
}