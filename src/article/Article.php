<?php

/*

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
    $ourfile =  $_SERVER['DOCUMENT_ROOT'] '/' . $stub . '/text.xml';
    $template = $twig->loadTemplate('article.html'); 

    $language = 'de'; //TODO: We need to determine this rather than hardwireing it.

    echo $template->render(array('data' => \nbkrnet\nbblog\util\XmlExtractor::extractor(file_get_contents($ourfile), $language)));
 */
namespace nbkrnet\nbblog\article;

class Article {

    public static function loadFromStub($stub) {
        throw new \Exception('Invalid stub.');
    }

    public static function loadFromString($string) {
        try {
            $data = new \SimpleXMLElement($string);
        } catch (Exception $e) {
            throw $e;
        }
    }    

}
