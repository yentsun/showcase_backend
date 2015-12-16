<?php

// получаем окружение: 'production' или 'development'


######################
# Соединение с базой #
######################


ORM::configure($config['db.url']);
ORM::configure('username', $config['db.user']);
ORM::configure('password', $config['db.password']);

const PUBLISHED = 1;
const UNPUBLISHED = 0;


class Lot extends Entity {
    protected static $_table = 'lot';

    public function brief() {
        $brief_fileds = array('id', 'title', 'type', 'preview_url');
        $brief_view = array();
        foreach($brief_fileds as $field)
            $brief_view[$field] = $this->{$field};
        return $brief_view;
    }
}


class Video extends Entity {
    protected static $_table = 'video';
}


class Entity {

    protected static $_table = null;

    public function __construct(ORM $orm_obj) {
        foreach($orm_obj->as_array() as $key => $value){
            $this->{$key} = $value;
        }
    }

    public static function fetch($id) {
        $obj = ORM::for_table(static::$_table)->find_one($id);
        return new static($obj);
    }

    public static function fetch_all($limit=null, $offset=null,
                                     $published_only=true) {
        $instances = array();
        $objects = ORM::for_table(static::$_table)
            ->order_by_asc('order')
            ->limit($limit)
            ->offset($offset);
        if ($published_only)
            $objects = $objects->where('status', PUBLISHED);
        foreach ($objects->find_many() as $obj) {
            $instances[] = new static($obj);
        }
        return $instances;
    }

    public static function count($published=false) {
        if ($published)
            return ORM::for_table(static::$_table)->where('status', 1)->count();
        return ORM::for_table(static::$_table)->count();
    }

    public static function post($data) {
        if (!isset($data['status']))
            $data['status'] = UNPUBLISHED;
        $obj = ORM::for_table(static::$_table)->create($data);
        $obj->id = uniqid();
        $obj->save();
        return new static($obj);
    }

    public static function patch($id, $data) {
        $obj = ORM::for_table(static::$_table)->find_one($id);
        $obj->set($data);
        $obj->save();
        return new static($obj);
    }

    public static function delete($id) {
        $victim = ORM::for_table(static::$_table)->find_one($id);
        $victim->delete();
        return new static($victim);
    }
}