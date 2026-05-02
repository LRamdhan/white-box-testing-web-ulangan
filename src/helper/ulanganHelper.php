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