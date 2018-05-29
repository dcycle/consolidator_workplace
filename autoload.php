<?php

/**
 * @file
 * Class autoloader.
 */

spl_autoload_register(function ($class) {
  if (substr($class, 0, strlen('consolidator_workplace\\')) == 'consolidator_workplace\\') {
    $class = preg_replace('/^consolidator_workplace\\\\/', '', $class);
    $path = 'src/' . str_replace('\\', '/', $class);
    if (!module_load_include('php', 'consolidator_workplace', $path)) {
      throw new \Exception('Could not load ' . $path);
    }
  }
});
