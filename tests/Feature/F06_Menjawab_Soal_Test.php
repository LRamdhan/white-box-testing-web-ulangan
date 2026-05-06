<?php

include "./vendor/autoload.php";

use App\model\TestSoalListModel;
use App\model\TestSoalListPilModel;
use App\model\MemberTesModel;
use App\model\TokenModel;
use App\utils\WebUtils;
use App\helper\UserHelper;
use App\helper\UlanganHelper;
use App\conf\GuzzleClient;
use App\utils\CookieUtils;

beforeAll(function() {
  UserHelper::cleanupUser(); // delete user
  UlanganHelper::clenupUlangan(); // delete ulangan
  
  // create
  UserHelper::setupUser();
  UlanganHelper::setupUlangan();
});

afterAll(function() {
  UserHelper::cleanupUser(); // delete user
  UlanganHelper::clenupUlangan(); // delete ulangan
});

describe("F06-P01 | ", function() {
  afterEach(function() {
    UlanganHelper::cleanupPengerjaan();  
  });

  test("F06-P01-T01 | Mengembalikan status error jika menjawab soal dalam keadaaan belum login", function() {
    // prakondisi :
    // - belum login
    // - sudah mulai mengerjakan soal sebelumnya

    // ambil data
    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login dan mulai ulangan
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $page->navigate(WebUtils::url("/start.php?kode=" . $ulanganCode));
    $correctToken = TokenModel::getToken()["token"];
    $page->fill("input[name=\"token\"]", $correctToken);
    $page->click("input[value=\"Mulai Kerjakan\"]");

    // kasus uji :
    // - melakukan request ke api/savepil.php dengan param yang benar tanpa melampirkan header cookie PHPSESSID (belum login)

    // ambil data soal dan pilihan ganda
    $soalList = TestSoalListModel::getAllTestSoalList();
    $keymember = $correctNis;
    $numsoal = $soalList[6]["number"];
    $keysoal = $soalList[6]["key_list"];
    $soalListPil = TestSoalListPilModel::getTestSoalListPil($keysoal);
    $pilsoal = $soalListPil[2]["list_pil"];
    $keylist = $soalListPil[2]["key_list_pil"];
    $info = $ulanganCode;

    // request
    $client = GuzzleClient::createClient();
    $response = $client->request('GET', "api/savepil.php?keymember=$keymember&numsoal=$numsoal&keysoal=$keysoal&pilsoal=$pilsoal&keylist=$keylist&info=$info");
    $responseCode = $response->getStatusCode();
    $responseBody = $response->getBody()->getContents();

    // hasil yang diharapkan :
    // - merespon dengan status error

    expect($responseCode)->toBe(200);
    expect($responseBody)->toContain("status:\"error\"");
  });
});

describe("F06-P02 | ", function() {
  afterEach(function() {
    UlanganHelper::cleanupPengerjaan();  
  });

  test("F06-P02-T01 | Mengembalikan status error jika menjawab soal dalam keadaaan sudah selesai mengerjakan ulangan", function() {
    // prakondisi :
    // - sudah login
    // - sudah selesai mengerjakan soal ulangan

    $dummyUser = WebUtils::getDummyUser();
    $correctNis = $dummyUser["nis"];
    $correctPassword = $dummyUser["password"];
    $ulanganCode = WebUtils::getSoalInfoProperty("id_info_soal");

    // login
    $page = visit(WebUtils::url("/login.php"), $this->browserContextOptions());
    $page->fill("#nis", $correctNis);
    $page->fill("#password", $correctPassword);
    $page->click('#submit');
    $sessionidCookie = CookieUtils::getCookie($page, "PHPSESSID");    

    // insert data selesai pengerjaan ulangan
    MemberTesModel::insertMemberTesTemp();

    // kasus uji :
    // - melakukan request ke api/savepil.php dengan param yang benar

    // ambil data soal dan pilihan ganda
    $soalList = TestSoalListModel::getAllTestSoalList();
    $keymember = $correctNis;
    $numsoal = $soalList[6]["number"];
    $keysoal = $soalList[6]["key_list"];
    $soalListPil = TestSoalListPilModel::getTestSoalListPil($keysoal);
    $pilsoal = $soalListPil[2]["list_pil"];
    $keylist = $soalListPil[2]["key_list_pil"];
    $info = $ulanganCode;

    // request
    $client = GuzzleClient::createClient();
    $response = $client->request('GET', "api/savepil.php?keymember=$keymember&numsoal=$numsoal&keysoal=$keysoal&pilsoal=$pilsoal&keylist=$keylist&info=$info", [
      "headers" => [
        "Cookie" => "PHPSESSID=$sessionidCookie"
      ]
    ]);
    $responseCode = $response->getStatusCode();
    $responseBody = $response->getBody()->getContents();

    // hasil yang diharapkan :
    // - merespon dengan status error

    expect($responseCode)->toBe(200);
    expect($responseBody)->toContain("status:\"error\"");
  });
});

describe("F06-P03 | ", function() {
  afterEach(function() {
    UlanganHelper::cleanupPengerjaan();  
  });

  test("F06-P03-T01 | Mengembalikan status error jika menjawab soal dengan param keymember yang salah", function() {
    // prakondisi :
    // - sudah login
    // - sudah mulai mengerjakan soal ulangan

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

    // kasus uji :
    // - melakukan request ke api/savepil.php dengan param keymember yang salah

    // ambil data soal dan pilihan ganda
    $soalList = TestSoalListModel::getAllTestSoalList();
    $Wrongkeymember = "random235";
    $numsoal = $soalList[6]["number"];
    $keysoal = $soalList[6]["key_list"];
    $soalListPil = TestSoalListPilModel::getTestSoalListPil($keysoal);
    $pilsoal = $soalListPil[2]["list_pil"];
    $keylist = $soalListPil[2]["key_list_pil"];
    $info = $ulanganCode;

    // request
    $client = GuzzleClient::createClient();
    $response = $client->request('GET', "api/savepil.php?keymember=$Wrongkeymember&numsoal=$numsoal&keysoal=$keysoal&pilsoal=$pilsoal&keylist=$keylist&info=$info", [
      "headers" => [
        "Cookie" => "PHPSESSID=$sessionidCookie"
      ]
    ]);
    $responseCode = $response->getStatusCode();
    $responseBody = $response->getBody()->getContents();

    // hasil yang diharapkan :
    // - merespon dengan status error

    expect($responseCode)->toBe(200);
    expect($responseBody)->toContain("status:\"error\"");
    expect($responseBody)->not->toContain("status:\"success\"");
  });

  test("F06-P03-T02 | Mengembalikan status error jika menjawab soal dengan param info yang salah", function() {
    // prakondisi :
    // - sudah login
    // - sudah mulai mengerjakan soal ulangan

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

    // kasus uji :
    // - melakukan request ke api/savepil.php dengan param info yang salah

    // ambil data soal dan pilihan ganda
    $soalList = TestSoalListModel::getAllTestSoalList();
    $keymember = $correctNis;
    $numsoal = $soalList[6]["number"];
    $keysoal = $soalList[6]["key_list"];
    $soalListPil = TestSoalListPilModel::getTestSoalListPil($keysoal);
    $pilsoal = $soalListPil[2]["list_pil"];
    $keylist = $soalListPil[2]["key_list_pil"];
    $Wronginfo = "random235";

    // request
    $client = GuzzleClient::createClient();
    $response = $client->request('GET', "api/savepil.php?keymember=$keymember&numsoal=$numsoal&keysoal=$keysoal&pilsoal=$pilsoal&keylist=$keylist&info=$Wronginfo", [
      "headers" => [
        "Cookie" => "PHPSESSID=$sessionidCookie"
      ]
    ]);
    $responseCode = $response->getStatusCode();
    $responseBody = $response->getBody()->getContents();

    // hasil yang diharapkan :
    // - merespon dengan status error

    expect($responseCode)->toBe(200);
    expect($responseBody)->toContain("status:\"error\"");
    expect($responseBody)->not->toContain("status:\"success\"");
  });

  test("F06-P03-T03 | Mengembalikan status error jika menjawab soal dengan param keysoal yang salah", function() {
    // prakondisi :
    // - sudah login
    // - sudah mulai mengerjakan soal ulangan

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

    // kasus uji :
    // - melakukan request ke api/savepil.php dengan param keysoal yang salah

    // ambil data soal dan pilihan ganda
    $soalList = TestSoalListModel::getAllTestSoalList();
    $keymember = $correctNis;
    $numsoal = $soalList[6]["number"];
    $keysoal = $soalList[6]["key_list"];
    $wrongKeysoal = "random235";
    $soalListPil = TestSoalListPilModel::getTestSoalListPil($keysoal);
    $pilsoal = $soalListPil[2]["list_pil"];
    $keylist = $soalListPil[2]["key_list_pil"];
    $info = $ulanganCode;

    // request
    $client = GuzzleClient::createClient();
    $response = $client->request('GET', "api/savepil.php?keymember=$keymember&numsoal=$numsoal&keysoal=$wrongKeysoal&pilsoal=$pilsoal&keylist=$keylist&info=$info", [
      "headers" => [
        "Cookie" => "PHPSESSID=$sessionidCookie"
      ]
    ]);
    $responseCode = $response->getStatusCode();
    $responseBody = $response->getBody()->getContents();

    // hasil yang diharapkan :
    // - merespon dengan status error

    expect($responseCode)->toBe(200);
    expect($responseBody)->toContain("status:\"error\"");
    expect($responseBody)->not->toContain("status:\"success\"");
  });

  test("F06-P03-T04 | Mengembalikan status error jika menjawab soal dengan param pilsoal yang salah", function() {
    // prakondisi :
    // - sudah login
    // - sudah mulai mengerjakan soal ulangan

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

    // kasus uji :
    // - melakukan request ke api/savepil.php dengan param pilsoal yang salah

    // ambil data soal dan pilihan ganda
    $soalList = TestSoalListModel::getAllTestSoalList();
    $keymember = $correctNis;
    $numsoal = $soalList[6]["number"];
    $keysoal = $soalList[6]["key_list"];
    $soalListPil = TestSoalListPilModel::getTestSoalListPil($keysoal);
    $wrongPilsoal = "random235";
    $keylist = $soalListPil[2]["key_list_pil"];
    $info = $ulanganCode;

    // request
    $client = GuzzleClient::createClient();
    $response = $client->request('GET', "api/savepil.php?keymember=$keymember&numsoal=$numsoal&keysoal=$keysoal&pilsoal=$wrongPilsoal&keylist=$keylist&info=$info", [
      "headers" => [
        "Cookie" => "PHPSESSID=$sessionidCookie"
      ]
    ]);
    $responseCode = $response->getStatusCode();
    $responseBody = $response->getBody()->getContents();

    // hasil yang diharapkan :
    // - merespon dengan status error

    expect($responseCode)->toBe(200);
    expect($responseBody)->toContain("status:\"error\"");
    expect($responseBody)->not->toContain("status:\"success\"");
  });

  test("F06-P03-T05 | Mengembalikan status error jika menjawab soal dengan param numsoal yang salah", function() {
    // prakondisi :
    // - sudah login
    // - sudah mulai mengerjakan soal ulangan

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

    // kasus uji :
    // - melakukan request ke api/savepil.php dengan param numsoal yang salah

    // ambil data soal dan pilihan ganda
    $soalList = TestSoalListModel::getAllTestSoalList();
    $keymember = $correctNis;
    $wrongNumsoal = "random235";
    $keysoal = $soalList[6]["key_list"];
    $soalListPil = TestSoalListPilModel::getTestSoalListPil($keysoal);
    $pilsoal = $soalListPil[2]["list_pil"];
    $keylist = $soalListPil[2]["key_list_pil"];
    $info = $ulanganCode;

    // request
    $client = GuzzleClient::createClient();
    $response = $client->request('GET', "api/savepil.php?keymember=$keymember&numsoal=$wrongNumsoal&keysoal=$keysoal&pilsoal=$pilsoal&keylist=$keylist&info=$info", [
      "headers" => [
        "Cookie" => "PHPSESSID=$sessionidCookie"
      ]
    ]);
    $responseCode = $response->getStatusCode();
    $responseBody = $response->getBody()->getContents();

    // hasil yang diharapkan :
    // - merespon dengan status error

    expect($responseCode)->toBe(200);
    expect($responseBody)->toContain("status:\"error\"");
    expect($responseBody)->not->toContain("status:\"success\"");
  });

  test("F06-P03-T06 | Mengembalikan status error jika menjawab soal dengan param keylist yang salah", function() {
    // prakondisi :
    // - sudah login
    // - sudah mulai mengerjakan soal ulangan

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

    // kasus uji :
    // - melakukan request ke api/savepil.php dengan param keylist yang salah

    // ambil data soal dan pilihan ganda
    $soalList = TestSoalListModel::getAllTestSoalList();
    $keymember = $correctNis;
    $numsoal = $soalList[6]["number"];
    $keysoal = $soalList[6]["key_list"];
    $soalListPil = TestSoalListPilModel::getTestSoalListPil($keysoal);
    $pilsoal = $soalListPil[2]["list_pil"];
    $wrongKeylist = "random235";
    $info = $ulanganCode;

    // request
    $client = GuzzleClient::createClient();
    $response = $client->request('GET', "api/savepil.php?keymember=$keymember&numsoal=$numsoal&keysoal=$keysoal&pilsoal=$pilsoal&keylist=$wrongKeylist&info=$info", [
      "headers" => [
        "Cookie" => "PHPSESSID=$sessionidCookie"
      ]
    ]);
    $responseCode = $response->getStatusCode();
    $responseBody = $response->getBody()->getContents();

    // hasil yang diharapkan :
    // - merespon dengan status error

    expect($responseCode)->toBe(200);
    expect($responseBody)->toContain("status:\"error\"");
    expect($responseBody)->not->toContain("status:\"success\"");
  });
});

describe("F06-P04 | ", function() {
  afterEach(function() {
    UlanganHelper::cleanupPengerjaan();  
  });

  test("F06-P04-T01 | Mengembalikan status success jika menjawab soal dengan param yang salah", function() {
    // prakondisi :
    // - sudah login
    // - sudah mulai mengerjakan soal ulangan

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

    // kasus uji :
    // - melakukan request ke api/savepil.php dengan param yang benar

    // ambil data soal dan pilihan ganda
    $soalList = TestSoalListModel::getAllTestSoalList();
    $keymember = $correctNis;
    $numsoal = $soalList[6]["number"];
    $keysoal = $soalList[6]["key_list"];
    $soalListPil = TestSoalListPilModel::getTestSoalListPil($keysoal);
    $pilsoal = $soalListPil[2]["list_pil"];
    $keylist = $soalListPil[2]["key_list_pil"];
    $info = $ulanganCode;

    // request
    $client = GuzzleClient::createClient();
    $response = $client->request('GET', "api/savepil.php?keymember=$keymember&numsoal=$numsoal&keysoal=$keysoal&pilsoal=$pilsoal&keylist=$keylist&info=$info", [
      "headers" => [
        "Cookie" => "PHPSESSID=$sessionidCookie"
      ]
    ]);
    $responseCode = $response->getStatusCode();
    $responseBody = $response->getBody()->getContents();

    // hasil yang diharapkan :
    // - merespon dengan status success

    expect($responseCode)->toBe(200);
    expect($responseBody)->toContain("status:\"success\"");
  });
});