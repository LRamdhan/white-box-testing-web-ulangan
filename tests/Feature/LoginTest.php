<?php

include "./vendor/autoload.php";

use Tests\Data\DummyUser;
use App\model\UserModel;
use App\utils\CookieUtils;
use App\utils\WebUtils;

beforeAll(function () {
  UserModel::createUser();
});

afterAll(function () {
  UserModel::deleteUser();
  UserModel::deleteLogLogin();
});

describe("1) 1-2-3-11", function() {




  

  it("should display validation error message 'Harap di Isi' with no input filled", function() {
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->click('#submit');

    $page->assertSee("Harap di Isi");
  });

  it("should display validation error message 'Harap di Isi' with space input filled to nis", function() {
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", "  ");
    $page->fill("#password", "a");
    $page->click('#submit');

    $page->assertSee("Harap di Isi");
  });

  it("should display validation error message 'Harap di Isi' with space input filled to password", function() {
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", "a");
    $page->fill("#password", "    ");
    $page->click('#submit');

    $page->assertSee("Harap di Isi");
  });
});

describe("2) 1-2-3-4-5-12-13-11", function() {
  it("should redirect to login page without error with nis length more than 20", function() {
    $longNis = "123456789012345678901234567890";
    $wrongPassword = "abcdefghijkl";

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $longNis);
    $page->fill("#password", $wrongPassword);
    $page->click('#submit');

    $page->assertPathEndsWith("/login.php");
  });
  
  it("should redirect to login page without error with password length more than 20", function() {
    $wrongNis = "1234567890";
    $longPassword = "123456789012345678901234567890";

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $wrongNis);
    $page->fill("#password", $longPassword);
    $page->click('#submit');

    $page->assertPathEndsWith("/login.php");
  });
  
  it("should redirect to login page without error with code filled to nis", function() {
    $codeNis = "p@ss\"word\'<script>";
    $wrongPassword = "abcdefghijkl";

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $codeNis);
    $page->fill("#password", $wrongPassword);
    $page->click('#submit');

    $page->assertPathEndsWith("/login.php");
  });

  it("should redirect to login page without error with code filled to password", function() {
    $wrongNis = "1234567890";
    $codePassword = "p@ss\"word\'<script>";

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $wrongNis);
    $page->fill("#password", $codePassword);
    $page->click('#submit');

    $page->assertPathEndsWith("/login.php");
  });

  it("should redirect to login page with error notification when login with wrong nis", function() {
    $wrongNis = "2937103853";
    $wrongPassword = "dummypass";

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $wrongNis);
    $page->fill("#password", $wrongPassword);
    $page->click('#submit');
    $page->assertPathEndsWith("/login.php");
    $page->assertSee("Kata-sandi atau nis tidak ditemukan");

    $page->assertValue("#nis", $wrongNis);
  });

  it("should redirect to login page with error notification when login with wrong password", function() {
    $correctNis = DummyUser::$nis;
    $wrongPassword = "dummypass";

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $wrongPassword);
    $page->click('#submit');

    $page->assertPathEndsWith("/login.php");
    $page->assertSee("Kata-sandi atau nis tidak ditemukan");
    $page->assertValue("#nis", $correctNis);
  });
});

describe("3) 1-2-3-4-5-6-7-8-9-10-11", function() {
  it("should successfully login with correct nis and password, without user cookie", function() {
    $correctNis = DummyUser::$nis;
    $correctPassword = DummyUser::$password;

    $nama = "Udin";
    $kelas = "XII-RPL-1";
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    $page->assertPathEndsWith("/index.php");
    $page->assertSee($nama);
    $page->assertSee($correctNis);
    $page->assertSee($kelas);

    $cookieNama = CookieUtils::getCookie($page, "cookie_nama");
    $cookieKelas = CookieUtils::getCookie($page, "cookie_kelas");
    $phpsessid = CookieUtils::getCookie($page, "PHPSESSID");

    expect($cookieNama)->toBe($nama);
    expect($cookieKelas)->toBe($kelas);
    expect($phpsessid)->not->toBeNull();
  });

  // intial condition :
  // tidak ada cookie
  // belum login

  // success if : 
  // di beranda data siswa benar
  // footer berisi contact guru yang benar sesuai kelasnya
  // url harus di /
  // harus ada cookie sesuai dengan data



});

describe("4) 1-2-3-4-5-6-7-8-10-11", function() {
  // success condition : value dari cookie_nama dan cookie_kelas harus ada di database

  // input : value cookie_nama tidak ada di database, value cookie_kelas ada di database
  it("value cookie_nama tidak ada di database, value cookie_kelas ada di database", function() {
    $unExistingNama = "Sjieijf";
    $existingNama = DummyUser::$nama;
    $ExistingKelas = DummyUser::$kelas;
    $correctNis = DummyUser::$nis;
    $correctPassword = DummyUser::$password;

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    CookieUtils::setCookie($page, "cookie_nama", $unExistingNama);
    CookieUtils::setCookie($page, "cookie_kelas", $existingKelas);
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    $cookieNamaValue = CookieUtils::getCookie($page, "cookie_nama");
    $cookieKelasValue = CookieUtils::getCookie($page, "cookie_kelas");
    expect($cookieNamaValue)->toBe($existingNama);
    expect($cookieKelasValue)->toBe($existingKelas);
  });

  // input : value cookie_kelas tidak ada di database, value cookie_nama ada di database
  it("value cookie_kelas tidak ada di database, value cookie_nama ada di database", function() {
    $existingNama = DummyUser::$nama;
    $unExistingKelas = "unexist_class";
    $existingKelas = DummyUser::$kelas;
    $correctNis = DummyUser::$nis;
    $correctPassword = DummyUser::$password;

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    CookieUtils::setCookie($page, "cookie_nama", $existingNama);
    CookieUtils::setCookie($page, "cookie_kelas", $unExistingKelas);
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    $cookieNamaValue = CookieUtils::getCookie($page, "cookie_nama");
    $cookieKelasValue = CookieUtils::getCookie($page, "cookie_kelas");
    expect($cookieKelasValue)->toBe($existingKelas);
    expect($cookieNamaValue)->toBe($existingNama);
  });
  
  // input : cookie_nama tidak ada, cookie kelas ada
  it("cookie_nama tidak ada, cookie kelas ada", function() {
    $existingNama = DummyUser::$nama;
    $existingKelas = DummyUser::$kelas;
    $correctNis = DummyUser::$nis;
    $correctPassword = DummyUser::$password;

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    CookieUtils::setCookie($page, "cookie_kelas", $existingKelas);
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    $cookieNamaValue = CookieUtils::getCookie($page, "cookie_nama");
    $cookieKelasValue = CookieUtils::getCookie($page, "cookie_kelas");

    expect($cookieNamaValue)->toBe($existingNama);
    expect($cookieKelasValue)->toBe($existingKelas);
  });
  
  // cookie_kelas tidak ada, cookie_nama ada
  it("cookie_kelas tidak ada, cookie_nama ada", function() {
    $existingNama = DummyUser::$nama;
    $existingKelas = DummyUser::$kelas;
    $correctNis = DummyUser::$nis;
    $correctPassword = DummyUser::$password;

    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    CookieUtils::setCookie($page, "cookie_nama", $existingNama);
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');

    $cookieNamaValue = CookieUtils::getCookie($page, "cookie_nama");
    $cookieKelasValue = CookieUtils::getCookie($page, "cookie_kelas");

    expect($cookieNamaValue)->toBe($existingNama);
    expect($cookieKelasValue)->toBe($existingKelas);
  });

});