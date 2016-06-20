<?php

    require __DIR__ . '/vendor/autoload.php';

    // check if stub is valid.
    $stub = $_SERVER['REQUEST_URI'];
    $stub = substr($stub, 1);
    if (strpos($stub, '/')) {
        $stub = substr($stub, 0, strpos($stub, '/'));
    }

    if (! preg_match('/^[0-9a-z-]+$/', $stub)) {
        //$error->404();
        // TODO: invalid stub
    }


    $documentroot = $_SERVER['DOCUMENT_ROOT'];
    $folderpath = $documentroot . '/' . $stub . '/';
    $filepath = $_SERVER['folders-articles'] . '/' . $stub . '.tar.bz2';

    // See if the source file exists.
    if (! file_exists($filepath)) {
        // TODO implement error system
        //$error->404();
        exit;
    }

    // Check if the tar.bz2 needs extracting.
    require __DIR__ . '/tarbzextractor.inc.php';

    // Loading the templating system.
    require_once __DIR__ . '/vendor/twig/twig/lib/Twig/Autoloader.php';
    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem($_SERVER['folders-template']);
    $twig = new Twig_Environment($loader, array(
        'cache' => $_SERVER['folders-templatecache'],
        ));


    // Actually display the file.
    $ourfile =  $stub . '/text.xml';
    $xml = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/' . $ourfile);
    $article = new SimpleXMLElement($xml);

    // Determining which language to display.
    $language = 'de'; //TODO: How do we want to set this? Env Variable, determine browser?

    // Finding the content of the current language.
    $content = $article->xpath('/article/content[@language="' . $language . '"]')[0]->asXML();

    // Removing the <content>-Tags from the actual content.
    $content = substr($content, strpos($content, '>') + 1);
    $content = substr($content, 0, strrpos($content, '<'));

    // Title
    $title = (string) $article->xpath('/article/content[@language="' . $language . '"]')[0]['title'];

    // Author
    $author = array('name' => (string) $article->xpath('/article/meta/author/name')[0],
               'email' => (string) $article->xpath('/article/meta/author/email')[0]);

    // Date
    $date = (string) $article->xpath('/article/meta/date')[0];

    $template = $twig->loadTemplate('article.html'); 

    echo $template->render(array('content' => $content, 'title' => $title, 'author' => $author, 'date' => $date, 'language' => $language));
