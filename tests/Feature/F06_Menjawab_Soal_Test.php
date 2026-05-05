<?php

include "./vendor/autoload.php";

use App\model\TestSoalListModel;
use App\model\TestSoalListPilModel;
use App\model\TestMemberTesModel;
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


// api/savepil.php?keymember=1122334455&numsoal=4&keysoal=gAlFKzUqCt&pilsoal=B&keylist=zbIvWhKJEg&info=999&user=41904


describe("F06-P01 | ", function() {
  afterEach(function() {
    UlanganHelper::cleanupPengerjaan();  
  });

  test("F06-P01-T01 | Merespon dengan status error jika menjawab soal dalam keadaaan belum login", function() {
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

  test("F06-P02-T01 | Merespon dengan status error jika menjawab soal dalam keadaaan sudah selesai mengerjakan ulangan", function() {
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
