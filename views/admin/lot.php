<?php

// список
$app->get('/lots', function () use ($app) {
    $lots = Lot::fetch_all(null, null, false);
    $app->render('admin/lots.twig', array('lots' => $lots));
})->name('lots');


// форма редактирования
$app->get('/lots/:id', function($id) use ($app) {
    $lot = Lot::fetch($id);
    $app->render('admin/lot.twig',
                 array('lot'=>$lot, 'upload_dir'=>$app->config('upload.path')));
})->name('lot');


// форма нового лота
$app->get('/new_lot', function() use ($app) {
    $app->render('admin/lot.twig', array('new'=>true));
})->name('new_lot');


// POST нового лота
$app->post('/lot_post', function() use ($app) {
    $data = $app->request->post();
    if (!isset($data['status']))
        $data['status'] = UNPUBLISHED;
    if (!isset($data['rent']))
        $data['rent'] = 0;
    unset($data['preview']);
    try {
        if ($data['title'] == '')
            throw new ErrorException('Поле "название" обязательно!');
        $new_lot = Lot::post($data);
        $id = $new_lot->id;
        if (isset($_FILES['preview'])) {
            // загружаем превью
            if ($_FILES['preview']['size']) {

                $preview_file = $_FILES['preview'];
                list($name, $ext) = explode('.', $preview_file['name']);
                $filename = 'orig_'.$id.'.'.$ext;
                $thumb_name = 'thumb_'.$id.'.'.$ext;
                $upload_dir = $app->config('upload.path');
                $file_path =  $upload_dir.'/'.$filename;
                $thumb_path =  $upload_dir.'/'.$thumb_name;

                if (move_uploaded_file($preview_file['tmp_name'],
                        $file_path) === true) {
                    $imagine = new Imagine\Gd\Imagine();
                    $size = new Imagine\Image\Box(500, 500);
                    $mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
                    $imagine->open($file_path)
                        ->thumbnail($size, $mode)
                        ->save($thumb_path)
                    ;
                    Lot::patch($id, array('preview_url'=>$thumb_path));
                }
            }
        }

        $app->flash('success',
            'Новый лот "'.$new_lot->title.'" создан!');
        $app->redirect($app->urlFor('lots'));

    } catch (ErrorException $e) {

        // что-то пошло не так
        $app->flashNow('error', $e->getMessage());
        $app->render('admin/lot.twig',
            array('lot' => $data,
                  'new' => true));
    }
})->name('lot_post');


// PATCH (апдэйт) существующего лота
$app->post('/lots/:id', function ($id) use ($app) {
    $data = $app->request->post();
    if (!isset($data['status']))
        $data['status'] = UNPUBLISHED;
    if (!isset($data['rent']))
        $data['rent'] = 0;
    if (isset($_FILES['preview'])) {
        // загружаем превью
        if ($_FILES['preview']['size']) {

            unset($data['preview']);
            $preview_file = $_FILES['preview'];
            list($name, $ext) = explode('.', $preview_file['name']);
            $filename = 'orig_'.$id.'.'.$ext;
            $thumb_name = 'thumb_'.$id.'.'.$ext;
            $upload_dir = $app->config('upload.path');
            $file_path = $upload_dir.'/'.$filename;
            $thumb_path = $upload_dir.'/'.$thumb_name;

            if (move_uploaded_file($preview_file['tmp_name'],
                                   $file_path) === true) {
                $imagine = new Imagine\Gd\Imagine();
                $size = new Imagine\Image\Box(500, 500);
                $mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
                $imagine->open($file_path)
                    ->thumbnail($size, $mode)
                    ->save($thumb_path)
                ;
                $data['preview_url'] = $thumb_path;
            }
        }
    }

    try {
        Lot::patch($id, $data);
        $app->flash('success', 'Данные лота обновлены!');
    } catch (Exception $e) {
        // что-то пошло не так
        $app->flash('error', $e->getMessage());
    }
    $app->redirect($app->urlFor('lots'));
});


// удаление лота
$app->get('/delete_lot/:id', function($id) use ($app) {
    $victim = Lot::delete($id);
    $app->flash('success', 'Лот "'.$victim->title.'" удален!');
    $app->redirect($app->urlFor('lots'));
})->name('delete_lot');


// порядок лотов
$app->post('/lot_order', function() use ($app) {
    $post_data = $app->request->post();
    $lot_ids = $post_data['lots'];
    foreach ($lot_ids as $number=>$id) {
        Lot::patch($id, array('order'=>$number));
    }
})->name('lot_order');