<?php

include "./vendor/autoload.php";

use App\model\TokenModel;
use App\utils\WebUtils;
use App\helper\UserHelper;
use App\helper\UlanganHelper;
use App\utils\CookieUtils;

beforeAll(function() {
  UserHelper::cleanupUser(); // clean up user
  UlanganHelper::clenupUlangan(); // clean up ulangan
  
  // create user
  UserHelper::setupUser();

  // create ulangan
  UlanganHelper::setupUlangan();
});

afterAll(function() {
  UserHelper::cleanupUser(); // clean up user
  UlanganHelper::clenupUlangan(); // clean up ulangan
});

describe("F07-P01 |", function () {
  afterEach(function() {
    UlanganHelper::cleanupPengerjaan();  
  });

  test("F07-P01-T01 | Menampilkan halaman login jika menyelesaikan ulangan dalam keadaan belum login", function () {
    // prakondisi :
    // - belum login

    // kasus uji :
    // - melakukan request ke end.php melalui URL

    $page = visit(WebUtils::url("/end.php"), $this->browserContextOptions());

    // hasil yang diharapkan :
    // - redirect ke halaman login

    $page->assertPathEndsWith("/login.php");
  });
});

describe("F07-P02 |", function () {
  afterEach(function() {
    UlanganHelper::cleanupPengerjaan();  
  });

  test("F07-P02-T01 | Berpindah ke halaman pengerjaan ulangan jika menyelesaikan ulangan dalam keadaan tidak semua soal terjawab", function () {
    // prakondisi :
    // - sudah login
    // - sudah mulai mengerjakan soal ulangan
    // - sudah menjawab sebagian soal
    // - waktu pengerjaan sudah lebih dari 10 menit

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai pengerjaan ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $sessionidCookie = CookieUtils::getCookie($page, "PHPSESSID");   
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // jawab soal
    UlanganHelper::insertSebagianJawaban();

    // set jam
    UlanganHelper::setUlanganTime();

    // kasus uji :
    // - mengklik tombol selesai

    $page->click("Selesai Tes"); 
    $page->click("input[value=\"Selesai\"]");

    // hasil yang diharapkan :
    // - redirect ke halaman pengerjaan soal ulangan

    $page->assertPathEndsWith("/test.php");
  });
});

describe("F07-P03 |", function () {
  afterEach(function() {
    UlanganHelper::cleanupPengerjaan();  
  });

  test("F07-P03-T01 | Berpindah ke halaman pengerjaan ulangan jika menyelesaikan ulangan dengan waktu pengerjaan kurang dari 10 menit", function () {
    // prakondisi :
    // - sudah login
    // - sudah mulai mengerjakan soal ulangan
    // - sudah menjawab semua soal
    // - waktu pengerjaan kurang dari dari 10 menit

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai pengerjaan ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $sessionidCookie = CookieUtils::getCookie($page, "PHPSESSID");   
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // jawab soal
    UlanganHelper::insertSemuaJawaban();

    // kasus uji :
    // - mengklik tombol selesai

    $page->click("Selesai Tes"); 
    $page->click("input[value=\"Selesai\"]");

    // hasil yang diharapkan :
    // - redirect ke halaman pengerjaan soal ulangan

    $page->assertPathEndsWith("/test.php");
  });
});

describe("F07-P04 |", function () {
  afterEach(function() {
    UlanganHelper::cleanupPengerjaan();  
  });

  test("F07-P04-T01 | Berpindah ke halaman beranda jika menyelesaikan ulangan dalam keadaan sudah menyelesaikan ulangan sebelumnya", function () {
    // prakondisi :
    // - sudah login
    // - sudah selesai mengerjakan soal ulangan

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai pengerjaan ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $sessionidCookie = CookieUtils::getCookie($page, "PHPSESSID");   
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // jawab soal
    UlanganHelper::insertSemuaJawaban();

    // set jam
    UlanganHelper::setUlanganTime();

    // kasus uji :
    // - menyelesaikan ulangan dengan mengaksesnya melalui url

    $page->navigate(WebUtils::url("/end.php"));

    // hasil yang diharapkan :
    // - redirect ke halaman pengerjaan soal ulangan

    $page->assertPathEndsWith("/index.php");
  });
});

describe("F07-P05 |", function () {
  afterEach(function() {
    UlanganHelper::cleanupPengerjaan();  
  });

  test("F07-P05-T01 | Berpindah ke halaman beranda jika menyelesaikan ulangan dalam keadaan semua soal terjawab dan waktu pengerjaan lebih dari 10 menit", function () {
    // prakondisi :
    // - sudah login
    // - sudah mulai mengerjakan soal ulangan
    // - sudah menjawab semua soal
    // - waktu pengerjaan lebih dari 10 menit

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai pengerjaan ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $sessionidCookie = CookieUtils::getCookie($page, "PHPSESSID");   
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // jawab soal
    UlanganHelper::insertSemuaJawaban();

    // set jam
    UlanganHelper::setUlanganTime();

    // kasus uji :
    // - mengklik tombol selesai

    $page->click("Selesai Tes"); 
    $page->click("input[value=\"Selesai\"]");

    // hasil yang diharapkan :
    // - redirect ke halaman pengerjaan soal ulangan

    $page->assertPathEndsWith("/index.php");
  });
});