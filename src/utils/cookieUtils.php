<?php

namespace App\utils;

class CookieUtils {
  public static function getCookie($page, $cookieName) {
    $cookie = $page->script("cookieStore.get('$cookieName');");
    return $cookie ? urldecode($cookie["value"]) : null;
  }

  public static function setCookie($page, $cookieName, $cookieValue) {
    $page->script("cookieStore.set({ name: '$cookieName', value: '$cookieValue' });");  
  }
}