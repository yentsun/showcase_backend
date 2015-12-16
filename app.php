<?php

################
# Конфигурация #
################

// временная зона
date_default_timezone_set('Europe/Moscow');

// Twig
$twig_view = new \Slim\Views\Twig();
$log_writer = new \Slim\LogWriter(fopen('log/slim_error.log', 'a'));

// общая конфигурация
$app = new \Slim\Slim(array(
    'mode' => $ENV,
    'view' => $twig_view,
    'templates.path' =>'templates',
    'upload.path' => 'uploads',
    'log.writer' => $log_writer
) + $config);


########
# Виды #
########

require_once 'views/lot.php';
require_once 'views/video.php';
require_once 'views/order.php';
require_once 'views/feedback.php';


// админ
$app->group('/admin', function () use ($app) {

    require_once 'views/admin/lot.php';

    // редирект с главной
    $app->get('/', function () use ($app) {
        $app->redirect($app->urlFor('lots'));
    });

    // данные для всех видов
    $app->view->appendData(array(
        'project_title'=> 'Biganto::Бэкенд',
        'app'=> $app
    ));
});