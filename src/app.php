<?php
// vim: set tw=80 colorcolumn=-1 :

# Decide if we have to display the index or an article.
if (! isset($_GET['type'])) {
	$type = 'unkown';
} else {
	$type = $_GET['type'];
}


if ($type == '404') {
    echo 'File not found';
}

if ($type == 'index') {
    echo 'index';
}

if ($type == 'home') {
    echo 'Home';
}
