<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

abstract class MpdtUtilsFactory {

  public static function load($class) {
    self::fetch($class);
  }

  public static function fetch($class) {
    global $mpdt_inflector;

    static $obj;

    if(!isset($obj)) {
      $obj = array();
    }

    if(!isset($obj[$class])) {
      $class = $mpdt_inflector->camelize($class);
      $classname = 'Mpdt'.ucwords($class).'Utils';
      $obj[$class] = new $classname;
    }

    return $obj[$class];
  }

  public static function fetch_for_api($api_class) {
    global $mpdt_inflector;

    preg_match('/^Mpdt(.*)Api$/', $api_class, $m);

    if(!isset($m[1])) {
      return false;
    }

    return self::fetch($mpdt_inflector->singularize($m[1]));
  }

}

