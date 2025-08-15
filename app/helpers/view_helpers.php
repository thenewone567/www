<?php
// View helper functions to safely handle data
function safeArray($data) {
    return isset($data) && is_array($data) ? $data : [];
}

function safeObject($data) {
    return isset($data) && is_object($data) ? $data : (object)[];
}

function safeProperty($object, $property, $default = "") {
    return (is_object($object) && property_exists($object, $property)) ? $object->$property : $default;
}

function safeForeach($array, $callback) {
    if (!is_array($array)) return;
    foreach ($array as $item) {
        if (is_object($item)) {
            $callback($item);
        }
    }
}
?>