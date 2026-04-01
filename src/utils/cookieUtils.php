<?php

namespace App\utils;

class CookieUtils {
  public static function getCookie($page, $cookieName) {
    $cookie = $page->script("cookieStore.get('$cookieName');");
    return $cookie ? $cookie["value"] : null;
  }

  public static function setCookie($page, $cookieName, $cookieValue) {
    $page->script("cookieStore.set({ name: '$cookieName', value: '$cookieValue' });");  
  }
}