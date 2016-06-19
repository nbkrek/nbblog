<?php
// vim: set tw=80 colorcolumn=-1 :

# Decide if we have to display the index or an article.
if (! isset($_GET['type'])) {
	$type = 'unkown';
} else {
	$type = $_GET['type'];
}


if ($type == 'unkown') {

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
        //$error->404();
        // TODO implement error system
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
    }

}

if ($type == 'index') {
}

if ($type == 'home') {
    echo 'Home';
}
