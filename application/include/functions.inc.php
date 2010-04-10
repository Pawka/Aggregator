<?php


/**
 * Išveda duomenų informaciją į ekraną ir nutraukia skripto vykdymą.
 *
 * @param array|object|mixed $data
 */
function lost($data) {
    lstOutputRow(func_get_args());
    exit;
}

/**
 * Išveda duomenų informaciją į ekraną ir nutraukia skripto vykdymą.
 *
 * @param array|object|mixed $data
 */
function dump($data) {
    lstOutputRow(func_get_args());
    exit;
}


/**
 * Išveda duomenų informaciją į ekraną.
 *
 * @param array|object|mixed $data
 */
function lst($data) {
    lstOutputRow(func_get_args());
}

/**
 * Išveda duomenų informaciją į ekraną.
 *
 * @param array|object|mixed $data
 */
function pa($data) {
    lstOutputRow(func_get_args());
}


function lstOutputRow($data) {
    if (!is_array($data) || empty($data)) {
        return;
    }

    $id = "dump_" . rand(1, time());

    echo "<div style=\"border:1px solid #555;background-color:#DDD;\">";
    echo "<div style=\"background-color: #333; padding: 2px;\" onclick=\"var el = document.getElementById('{$id}'); el.style.display = (el.style.display == 'none') ? 'block' : 'none';\">";
    echo "<input value=\"Hide\" type=\"button\" />";
    echo "</div>";
    echo "<div id=\"{$id}\">";
    echo "<pre style=\"margin:3px;font-size:11px;color:blue\">";
    foreach ($data as $row) {
        lstOutputItem($row);
    }
    echo "</div></div>";
}


function lstOutputItem($data) {
    echo "<pre style=\"margin:6px 3px;font-size:11px;color:blue;border-bottom: 1px solid rgb(205, 205, 205);padding-bottom: 5px\">";
    var_dump($data);
    echo "</pre>";
}
