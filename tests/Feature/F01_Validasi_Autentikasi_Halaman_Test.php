<?php

include "./vendor/autoload.php";

use App\model\UserModel;
use App\model\LogLoginModel;
use App\model\TokenModel;
use App\utils\CookieUtils;
use App\utils\WebUtils;
use App\helper\UlanganHelper;

beforeAll(function () {
  UserModel::deleteUser(); // hapus dummy user
  LogLoginModel::deleteLogLogin();  // hapus log login
  UlanganHelper::clenupUlangan();  // hapus ulangan
  UlanganHelper::cleanupPengerjaan();  // cleanup pengerjaan

  // buat dummy user
  UserModel::createUser();

  // buat ulangan
  UlanganHelper::setupUlangan();
});

afterAll(function () {
  UserModel::deleteUser(); // hapus dummy user
  LogLoginModel::deleteLogLogin();  // hapus log login
  UlanganHelper::clenupUlangan();  // hapus ulangan
  UlanganHelper::cleanupPengerjaan();  // cleanup pengerjaan
});

describe("F01-P01 | 1-2-3-4", function() {
  test("F01-P01-T01 | Menampilkan halaman beranda jika mengakses halaman dalam keadaan sudah login", function() {
    // Prakondisi :
    // - sudah login

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $nama = $dummyUser["nama"];
    $kelas = $dummyUser["kelas"];

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    // Kasus uji : 
    // - mengakses halaman beranda melalui url

    $page->navigate(WebUtils::url("/"));

    // Hasil yang diharapkan :
    // - url harus tetap berada di halaman beranda

    $page->assertPathEndsWith("/");
  });

  test("F01-P01-T02 | Menampilkan halaman mulai ulangan jika mengakses halaman dalam keadaan sudah login", function() {
    // Prakondisi :
    // - sudah login

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $nama = $dummyUser["nama"];
    $kelas = $dummyUser["kelas"];

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    // Kasus uji : 
    // - mengakses halaman mulai ulangan melalui url

    $idSoal = WebUtils::getSoalInfoProperty("id_info_soal");
    $urlMulaiUlangan = "/start.php";
    $page->navigate(WebUtils::url($urlMulaiUlangan . "?kode=" . $idSoal));

    // Hasil yang diharapkan :
    // - url harus tetap berada di halaman mulai ulangan
    // - param keysoal harus ada dalam url

    $param = WebUtils::getParam($page, "kode");
    $page->assertPathEndsWith($urlMulaiUlangan);
    expect($param)->toBe($idSoal);
  });

  test("F01-P01-T03 | Menampilkan halaman pengerjaan ulangan jika mengakses halaman dalam keadaan sudah login", function() {
    // Prakondisi :
    // - sudah login

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $nama = $dummyUser["nama"];
    $kelas = $dummyUser["kelas"];

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    $idSoal = WebUtils::getSoalInfoProperty("id_info_soal");
    $page->navigate(WebUtils::url("/start.php?kode=" . $idSoal));

    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // Kasus uji : 
    // - mengakses halaman pengerjaan ulangan melalui url
    $urlPengerjaanUlangan = "/test.php";
    $page->navigate(WebUtils::url($urlPengerjaanUlangan));
    

    // Hasil yang diharapkan :
    // - url harus tetap berada di halaman pengerjaan ulangan

    $page->assertPathEndsWith($urlPengerjaanUlangan);
  });
});

describe("F01-P02 | 1-2-5-4", function() {
  test("F01-P02-T01 | Menampilkan halaman login jika mengakses halaman beranda dalam keadaan belum login", function() {
    // Prakondisi :
    // - belum login

    // Kasus uji : 
    // - mengakses halaman beranda melalui url

    $page = visit(WebUtils::url("/"), $this->browserContextOptions());

    // Hasil yang diharapkan :
    // - url harus berada di halaman login

    $page->assertPathEndsWith("/login.php");
  });

  test("F01-P02-T02 | Menampilkan halaman login jika mengakses halaman mulai ulangan dalam keadaan belum login", function() {
    // Prakondisi :
    // - belum login

    // Kasus uji : 
    // - mengakses halaman mulai ulangan melalui url

    $idSoal = WebUtils::getSoalInfoProperty("id_info_soal");
    $urlMulaiUlangan = "/start.php";
    $page = visit(WebUtils::url($urlMulaiUlangan . "?kode=" . $idSoal));

    // Hasil yang diharapkan :
    // - url harus berada di halaman login

    $page->assertPathEndsWith("/login.php");
  });

  test("F01-P02-T03 | Menampilkan halaman login jika mengakses halaman pengerjaan ulangan dalam keadaan belum login", function() {
    // Prakondisi :
    // - belum login

    // Kasus uji : 
    // - mengakses halaman pengerjaan ulangan melalui url

    $idSoal = WebUtils::getSoalInfoProperty("id_info_soal");
    $urlPengerjaanUlangan = "/test.php";
    $page = visit(WebUtils::url($urlPengerjaanUlangan));

    // Hasil yang diharapkan :
    // - url harus berada di halaman login

    $page->assertPathEndsWith("/login.php");
  });
});