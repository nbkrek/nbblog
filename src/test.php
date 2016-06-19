<?php

    $stub = $_SERVER['REQUEST_URI'];
    $stub = substr($stub, 1);
    if (strpos($stub, '/')) {
        $stub = substr($stub, 0, strpos($stub, '/'));
    }

    echo 'Stub: ' . $stub . ' ist: <br>';

    if (! preg_match('/^[0-9-a-z-]+$/', $stub)) {
        echo 'Ungueltig';
    } else {
        echo 'Ok';
    }
