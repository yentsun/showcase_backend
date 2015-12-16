<?php

/**
 * Возвращает массив отсутствующих (или пустых) полей в $actual
 * @param array $required
 * @param array $actual
 * @return array
 */
function get_absent(array $required, array $actual) {

    $result = array();
    foreach ($required as $field) {
        if (!array_key_exists($field, $actual) || $actual[$field] == '')
            $result[] = $field;
    }
    return $result;
}


/**
 * Возвращает массив с конфигурацией в указанной секции
 * @param $path
 * @param $section
 * @return array
 */
function get_config($path, $section) {

    $config = parse_ini_file($path, true);
    return $config[$section];
}