<?php

    # TODO: Pruefung ob der 'stub' Ordner existiert.

    $ourfile =  $_SERVER['REQUEST_URI'] . 'text.xml';
    $xml = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $ourfile);
    $article = new SimpleXMLElement($xml);

    $content = $article->content[0]->asXML();
    echo $content;
