<?php

namespace nbkrnet\nbblog\page;

class Page {

    function __construct($container, $xmlstring) {

        $this->c = $container;
        $this->xml = $xmlstring;

    }

    public function renderHtml() {
        $twig = $this->c['twig'];
        $language = $this->c['language'];

        // Select the language specific tempate.
        try {
            $template = $twig->loadTemplate('page.' . $language . '.html'); 
        } catch (\Exception $e) {
            $template = $twig->loadTemplate('page.html'); 
        }

        // Get the data and set content and title to the lanuage specific one
        $data = \nbkrnet\nbblog\utils\XmlExtractor::extractor($this->xml);
        $data['content'] = $data['content'][$language];
        $data['title'] = $data['title'][$language];
        $data['language'] = $language;

        return $template->render(array('data' => $data));
    }

}
