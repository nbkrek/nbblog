<?php

namespace nbkrnet\nbblog\index;

class Index {

    function __construct($container, $tag = Null) {
        $this->c = $container;

        if ($tag != Null) {
            throw new \Exception('Unkown Tag');
        }
    }

    public function renderHtml($page=1) {
        $twig = $this->c['twig'];
        $template = $twig->loadTemplate('index.html'); 
        $language = 'de'; //TODO

        $sql = 'SELECT stub FROM articles ORDER BY publishdate DESC';

        $data = array();
        foreach ($this->c['db']->query($sql) as $res) {
            $textxml = $this->c['config_folders-extract'] . '/' . $res['stub'] . '/text.xml';

            $xmlstring = file_get_contents($textxml);
            $article = \nbkrnet\nbblog\utils\XmlExtractor::extractor($xmlstring, $language);
            // We have to replace the paths of the images.
            $article['content'] = preg_replace('/<img(.*)src="(.*)"/U', '<img$1src="' . $res['stub'] . '/$2"', $article['content']);

            $data[] = $article;
        }

        return $template->render(array('data' => $data));
    }

}
