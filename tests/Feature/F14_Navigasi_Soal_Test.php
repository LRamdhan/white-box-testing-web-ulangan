<?php

include "./vendor/autoload.php";

use App\model\SoalInfoModel;
use App\model\SoalListModel;
use App\model\SoalListPilModel;
use App\model\SoalJadwalModel;
use App\model\TestSoalListModel;
use App\model\TestSoalListPilModel;
use App\model\MemberTesModel;
use App\model\TestMemberTesModel;
use App\model\LogLoginModel;
use App\model\TokenModel;
use App\model\UserModel;
use App\model\TestListTestModel;
use App\utils\WebUtils;

beforeAll(function() {
  // parse soal_list & soal_list_pil
  $soalPil = WebUtils::parseSoaListSoalListPil();
  $soal = $soalPil["soal"];
  $pil = $soalPil["pil"];

  // soal_info
  $soalInfoExist = SoalInfoModel::checkSoalInfo();
  if($soalInfoExist) {
    SoalInfoModel::deleteSoalInfo();
  }
  SoalInfoModel::createSoalInfo();

  // soal_list
  $soalListExist = SoalListModel::checkSoalList();
  if($soalListExist) {
    SoalListModel::deleteSoalList();
  }
  SoalListModel::createSoalList($soal);

  // soal_list_pil
  $soalListPilExist = SoalListPilModel::checkSoalListPil();
  if($soalListPilExist) {
    SoalListPilModel::deleteSoalListPil();
  }
  SoalListPilModel::createSoalListPil($pil);

  // soal_jadwal
  $soalJadwalExist = SoalJadwalModel::checkSoalJadwal();
  if($soalJadwalExist) {
    SoalJadwalModel::deleteSoalJadwal();
  }
  SoalJadwalModel::createSoalJadwal();

  // test_soal_list
  $testSoalListExist = TestSoalListModel::checkTestSoalList();
  if($testSoalListExist) {
    TestSoalListModel::deleteSoalList();
  }
  TestSoalListModel::createTestSoalList($soal);

  // test_soal_list_pil
  $testSoalListPilExist = TestSoalListPilModel::checkTestSoalListPil();
  if($testSoalListPilExist) {
    TestSoalListPilModel::deleteTestSoalListPil();
  }
  TestSoalListPilModel::createTestSoalListPil($pil);

  // buat dummy user
  UserModel::createUser();

  // buat dummy user 2
  UserModel::createUser2();
});

// afterAll(function() {
//   // delete all soal
//   SoalInfoModel::deleteSoalInfo();
//   SoalListModel::deleteSoalList();
//   SoalListPilModel::deleteSoalListPil();
//   SoalJadwalModel::deleteSoalJadwal();

//   // delete member_tes
//   MemberTesModel::deleteMemberTes();

//   // delete test_member_tes
//   TestMemberTesModel::deleteTestMemberTes();

//   // hapus dummy user
//   UserModel::deleteUser();

//   // hapus dummy user 2
//   UserModel::deleteUser2();

//   // hapus log login
//   LogLoginModel::deleteLogLogin();

//   // hapus log login 2
//   LogLoginModel::deleteLogLogin2();

//   // delete jawaban
//   TestListTestModel::deleteTestListTest();
// });



test("example", function () {
  expect(true)->toBeTrue();
});