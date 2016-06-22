<?php
// vim: set tw=80 colorcolumn=-1 :


// Just a bit of boilerpate to tie the different objects together.
if (! isset($_GET['type'])) {
	$type = 'unkown';
} else {
	$type = $_GET['type'];
}


if ($type == '404') {
    echo 'File not found';
}

if ($type == 'article') {
    // TODO: Get the stub of the article.

    try {
        $article = \nbkrnet\nblog\article\ArticleDisplay::createFromStub($stub);
    } catch (Exception $e) {
        // TODO: 404
        exit;
    }

    $article->display();
}

if ($type == 'index') {
    echo 'index';
}

if ($type == 'home') {
    echo 'Home';
}
