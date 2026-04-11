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
    TestSoalListModel::deleteTestSoalList();
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

afterAll(function() {
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

  // hapus dummy user
  UserModel::deleteUser();

  // hapus dummy user 2
  UserModel::deleteUser2();

  // hapus log login
  LogLoginModel::deleteLogLogin();

  // hapus log login 2
  LogLoginModel::deleteLogLogin2();

  // delete jawaban
  TestListTestModel::deleteTestListTest();
});

describe("F09-P01 | 1-2-3-4-5-9-10", function() {
  it("F09-P01-T01 | Tetap di halaman mulai ulangan, jika memasukan token yang salah", function() {
    // Prakondisi
    // - sudah login
    // - berada di halaman mulai ulangan
    // - soal sudah aktif
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));

    // Kasus uji : 
    // 1. memasukan token yang salah
    // 2. klik tombol mulai kerjakan
    $wrongToken = "123456";
    $page->fill("input[name=\"token\"]", $wrongToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // Hasil yang diharapkan :
    // - masih berada di halaman mulai ulangan
    // - menampilkan data ulangan yang sesuai
    $ulanganName = WebUtils::getSoalInfoProperty("nama_pelajaran");
    $page->assertPathEndsWith("/start.php");
    $page->assertSee($ulanganName);
    $page->assertDontSee("Warning:");
  });
});

describe("F09-P02 | 1-2-3-4-5-6-7-8", function() {
  it("F09-P02-T01 | Berpindah ke halaman kerjakan soal, jika memasukan token yang benar", function() {
    // Prakondisi
    // - sudah login
    // - berada di halaman mulai ulangan
    // - soal sudah aktif
    $dummyUser2 = WebUtils::getDummyUser();
    $correctNis = $dummyUser2["nis"];
    $correctPassword = $dummyUser2["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));

    // Kasus uji : 
    // 1. memasukan token yang benar
    // 2. klik tombol mulai kerjakan
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // Hasil yang diharapkan :
    // - redirect ke halaman pengerjaan ulangan
    // - menampilkan data ulangan yang sesuai
    $ulanganName = WebUtils::getSoalInfoProperty("nama_pelajaran");
    $page->assertPathEndsWith("/test.php");
    $page->assertSee($ulanganName);
    $page->assertDontSee("Warning:");
  });
});