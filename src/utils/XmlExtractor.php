<?php

/*
 * We basically have just 2 types of XML files. Articles and Pages
 * The only thing they differ function xmlextractor($path) {
 *  * Existance of a date
 *  * Existance of an author
 *  * the base element. 
 * 
 * => It makes sense to combine this in one function
 */
namespace nbkrnet\nbblog\utils;


class XmlExtractor {
    public static function extractor($filecontent, $language = 'de') {

        $xml = new \SimpleXMLElement($filecontent);
        $type = $xml->getName();

        // Finding the content of the current language.
        $content = $xml->xpath('/' . $type . '/content[@language="' . $language . '"]')[0]->asXML();

        // Removing the <content>-Tags from the actual content.
        $content = substr($content, strpos($content, '>') + 1);
        $content = substr($content, 0, strrpos($content, '<'));

        // Title
        $title = (string) $xml->xpath('/' . $type . '/content[@language="' . $language . '"]')[0]['title'];

        if ($type == 'article') {
            // Author
            $author = array('name' => (string) $xml->xpath('/' . $type . '/meta/author/name')[0],
                       'email' => (string) $xml->xpath('/' . $type .'/meta/author/email')[0]);

            // Date
            $date = (string) $xml->xpath('/' . $type . '/meta/date')[0];

            return array('title' => $title,
                         'content' => $content,
                         'author' => $author,
                         'type' => $type,
                         'language' => $language,
                         'date' => $date);
        } else {
            return array('title' => $title,
                         'content' => $content,
                         'type' => $type,
                         'language' => $language);
        }

    }
}
