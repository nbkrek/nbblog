<?php

/* Everytime an article get's extracted, it should be indexed, so that
 * the overview system can show lists */
function reindexArticle($stub) {

    // open text.xml within the documentroot
    // Extract the data from it
    $data = xmlextractor($ourfile);

    // Store it inside an database

}
