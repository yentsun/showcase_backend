<?php

require_once 'utils.php';

$app->post('/feedback', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');

    $data = json_decode($app->request->getBody(), true);
    $required = array('name', 'email', 'message');
    $required_not_present = get_absent($required, $data);

    // не заполнены обязательные поля
    if (!empty($required_not_present)) {
        $app->response->setBody(json_encode(array(
            'status'=>'error',
            'required_fields'=>$required_not_present)));
    } else {
        // все ок - отправляем письмо
        $email_from = $app->config('email_from');
        $headers = 'From: '.$email_from."\r\n".
                   'Reply-To: '.$email_from."\r\n" .
                   'X-Mailer: PHP/' . phpversion()."\r\n" .
                   'Content-Type: text/html; charset=UTF-8';
        $body = $app->view->render('feedback_mail.twig', array('data'=>$data));
        mail($app->config('feedback_addresses'),
             'Обратная свзять biganto',
             $body,
             $headers);
        $app->response->setBody(json_encode(array('status'=>'ok')));
    }
})->name('feedback');