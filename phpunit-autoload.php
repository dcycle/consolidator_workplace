<?php

/**
 * @file
 * Class autoloader.
 */

require_once 'phpunit-bootstrap.php';

spl_autoload_register(function ($class) {
  if (substr($class, 0, strlen('consolidator_workplace\\')) == 'consolidator_workplace\\') {
    $class = preg_replace('/^consolidator_workplace\\\\/', '', $class);
    $path = 'src/' . str_replace('\\', '/', $class);
    require_once $path . '.php';
  }
});
