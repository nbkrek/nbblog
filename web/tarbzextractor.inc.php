<?php

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
