<?php
require 'utils.php';

$ENV = getenv('ENV');
$config = get_config('config.ini', $ENV) + get_config('config.ini', 'general');

require 'vendor/autoload.php';
require 'models.php';
require 'app.php';

session_cache_limiter(false);
session_start();

$app->run();