<?php

function get_args(array $args, array $args_name, array $default_args = null): array {
    $return = array();
    foreach($args_name as $name) {
        if(array_key_exists($name, $args)) {
            $return[$name] = $args[$name];
        } else {
            if($default_args && array_key_exists($name, $default_args)) {
                $return[$name] = $default_args[$name];
            }
        }
    }
    return $return;
}

function compare_args(array $args, array $args_name): bool {
    $compare = true;
    foreach($args_name as $name) {
        if(!array_key_exists($name, $args)) {
            $compare = false;
            break;
        }
    }
    return $compare;
}

function system_print($print) {
    echo "<pre>";
    print_r($print);
    echo "</pre>";
}

function get_post_request(): array {
    $result = [];
    if(isset($_POST)) {
        foreach($_POST as $key => $value) {
            $result[$key] = $value;
        }
    }
    return $result;
}