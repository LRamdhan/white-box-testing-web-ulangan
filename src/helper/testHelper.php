<?php

namespace App\helper;

class TestHelper {
  public static function assertNoErrorMessage($page) {
    $page->assertDontSee("Warning:");
    $page->assertDontSee("Error:");
    $page->assertDontSee("Fatal error:");
    $page->assertDontSee("Parse error:");
    $page->assertDontSee("Notice:");
    $page->assertDontSee("Deprecated:");
  }
}