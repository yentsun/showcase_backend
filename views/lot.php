<?php

// счетчик кол-ва лотов
$app->get('/lot_count', function() use ($app) {
    $data = array(
        'total'=>Lot::count(),
        'published'=>Lot::count(true)
    );
    $app->response->headers->set('Content-Type', 'application/json');
    $app->response->setBody(json_encode($data));
});


// JSON всех лотов постранично
$app->get('/lot_pages(/:page)', function($page=1) use ($app) {
    $limit = $app->config('lots_per_page');
    $offset = ($page - 1) * $limit;
    $lots = Lot::fetch_all($limit, $offset);
    $brief_data = array();
    foreach ($lots as $lot) {
        $brief_data[] = $lot->brief();
    }
    $app->response->headers->set('Content-Type', 'application/json');
    $app->response->setBody(json_encode($brief_data));
});

// полный JSON лота
$app->get('/lots/:id', function($id) use ($app) {
    $lot = Lot::fetch($id);
    $app->response->headers->set('Content-Type', 'application/json');
    $app->response->setBody(json_encode($lot));
});

// HTML видео
$app->get('/lots/:id/video', function($id) use ($app) {
    $app->render('video.twig', array('id' => $id));
});