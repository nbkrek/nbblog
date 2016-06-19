<?php

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

    // See if the extrated folder exists or is older as the file.
    if (! file_exists($folderpath)
        || filemtime($folderpath) < filemtime($filepath)) {

        if (file_exists($folderpath)) {
            // Delete the folder 
            system("rm -rf $folderpath");
        }

        // Extract the tar.bz2 file.
        system("tar --touch -C $documentroot -xjf $filepath");
        
        // Redirect to the same url.
        header("Refresh:0");
        exit;

    }

    $ourfile =  $stub . '/text.xml';
    $xml = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/' . $ourfile);
    $article = new SimpleXMLElement($xml);

    $content = $article->content[0]->asXML();
    echo $content;
