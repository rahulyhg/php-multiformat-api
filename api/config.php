<?php
//errors and debug
error_reporting(/*E_ALL*/null);
ini_set('display_errors', '0');

//Database
define("DB_TYPE", "mysql");
define("DB_HOST", "localhost");
define("DB_NAME", "temp");
define("DB_USER", "root");
define("DB_PASS", "test");

//App
define("CORE", __DIR__ . "/core/");
define("ROUTES", __DIR__ . "/routes/");
define("WEB", __DIR__ . "/../web/");