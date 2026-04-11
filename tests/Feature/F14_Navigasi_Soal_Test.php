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


describe("F14_P01 | lurus", function() {

  test("P14-P01-T01 | Menampilkan soal yang sesuai, jika men-klik tombol navigasi berikutnya", function() {
    // Prakondisi
    // - sudah login
    // - berada di halaman pengerjaan ulangan
    // - beberapa soal sudah dijawab
    // - salah satu soal sudah terjawab

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));

    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");
    
    $memberTest = TestMemberTesModel::getTestMemberTes();
    $keysoal = explode(",", $memberTest["value_random"]);
    $page->click("#A");

    // Kasus Uji :
    // - tombol navigasi berikutnya diklik

    $keysoal2 = $keysoal[1];
    $page->click("#$keysoal2");

    // Assert :
    // - kotak berwarna hijau untuk soal sudah dijawab
    // - kotak berwarna biru untuk soal saat ini
    // - nomor soal di tampilan dan di database harus berbeda
    // - menampilkan soal yang sesuai
    // - menampilkan pilihan ganda yang sesuai
    // - soal belum dijawab
    
    $keysoal1 = $keysoal[0];

    // - kotak berwarna hijau untuk soal sudah dijawab
    $page->assertAttributeContains("#box-number-$keysoal1", "class", "box-number-sel-g");

    // - kotak berwarna biru untuk soal saat ini
    $page->assertAttributeContains("#box-number-$keysoal2", "class", "box-number-sel-b");

    $soalNo2 = TestSoalListModel::getTestSoalList($keysoal2);
    
    // - nomor soal di tampilan dan di database harus berbeda
    $noSoalContent = html_entity_decode($page->script("document.getElementsByClassName(\"soal-no\")[0].children[0].innerHTML;"));
    $noSoalContentRaw = explode(" ", $noSoalContent);
    $noSoalContent = $noSoalContentRaw[count($noSoalContentRaw) - 1];
    expect($soalNo2["number"])->not->tobe($noSoalContent);

    // - menampilkan soal yang sesuai
    $soalNo2Content = html_entity_decode($soalNo2["soal"]);
    $innerHtml = html_entity_decode($page->script("document.getElementsByClassName(\"soal\")[0].innerHTML;"));
    expect($innerHtml)->tobe($soalNo2Content);

    // - menampilkan pilihan ganda yang sesuai
    $pilihanGanda = TestSoalListPilModel::getTestSoalListPil($keysoal2);
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $pilihanGandaContent = html_entity_decode($pilihanGanda[$i]["pilihan"]);
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $option = html_entity_decode($page->script("document.getElementById(\"$alphabet\").parentElement.nextElementSibling.innerHTML;"));
      if($pilihanGanda[$i]["typepg"] == "png") {
        $expected = '<img src='.html_entity_decode($pilihanGanda[$i]['pilihan']).' alt='.$pilihanGanda[$i]['pilihan'].'/>';
        expect($option)->tobe($expected);
      } else {
        expect($option)->tobe($pilihanGandaContent);
      }
    }

    // - soal belum dijawab
    for($i = 0; $i < count($pilihanGanda); $i++) {
      $alphabet = $pilihanGanda[$i]["list_pil"];
      $checkStatus = $page->script("document.getElementById(\"$alphabet\").checked;");
      expect($checkStatus)->tobe(false);
    }
  });

});