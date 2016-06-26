<?php

namespace nbkrnet\nbblog\article;

class Article {

    function __construct($container, $xmlstring) {

        $this->c = $container;
        $this->xml = $xmlstring;

    }

    public function renderHtml() {
        $twig = $this->c['twig'];
        $template = $twig->loadTemplate('article.html'); 
        $language = 'de'; //TODO
        return $template->render(array('data' => \nbkrnet\nbblog\utils\XmlExtractor::extractor($this->xml, $language)));
    }

}
