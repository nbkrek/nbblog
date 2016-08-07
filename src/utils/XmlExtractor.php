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
    public static function extractor($filecontent) {

        $xml = new \SimpleXMLElement($filecontent);
        $type = $xml->getName();

        // Finding the content of the current language.
        $contents = $xml->xpath('/' . $type . '/content');

        $title = array();
        $content = array();
        $languages = array();
        for ($i = 0; $i < sizeof($contents); $i++) {
            $language = (string) $contents[$i]['language'];
            $languages[] = $language;
            $title[$language] = $contents[$i]['lanuage'];
            $content[$language] = $contents[$i]->asXml();
            // Removing the <content>-Tags from the actual content.
            $content[$language] = substr($content[$language], strpos($content[$language], '>') + 1);
            $content[$language] = substr($content[$language], 0, strrpos($content[$language], '<'));
            
        }


        if ($type == 'article') {
            // Author
            $author = array('name' => (string) $xml->xpath('/' . $type . '/meta/author/name')[0],
                       'email' => (string) $xml->xpath('/' . $type .'/meta/author/email')[0]);

            // Date
            $date = (string) $xml->xpath('/' . $type . '/meta/date')[0];

            // Getting all tags
            $tags = array();
            $tagxml = $xml->xpath('/' . $type . '/meta/tags');
            if ($tagxml) {
                foreach ($tagxml[0]->children() as $tag) {
                    $tags[] = $tag;
                }
            }

            return array('title' => $title,
                         'content' => $content,
                         'author' => $author,
                         'tags' => $tags,
                         'type' => $type,
                         'languages' => $languages,
                         'date' => $date);
        } else {
            return array('title' => $title,
                         'content' => $content,
                         'type' => $type,
                         'languages' => $languages);
        }

    }
}
