<?php

// JSON всех видео
$app->get('/videos', function() use ($app) {
    $videos = Video::fetch_all();
    $app->response->headers->set('Content-Type', 'application/json');
    $app->response->headers->set('Access-Control-Allow-Origin',
        'http://localhost:7000');
    $app->response->setBody(json_encode($videos));
});
