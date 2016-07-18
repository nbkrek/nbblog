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

        $offset = $this->c['config_articles-per-page'] * ($page - 1);

        $sql = 'SELECT stub FROM articles ORDER BY publishdate DESC LIMIT ? OFFSET ?';
        $sth = $this->c['db']->prepare($sql);
        $sth->execute(array($this->c['config_articles-per-page'], $offset));

        $sql = 'SELECT stub FROM articles ORDER BY publishdate DESC LIMIT ? OFFSET ?';
        $sth = $this->c['db']->prepare($sql);
        $sth->execute(array($this->c['config_articles-per-page'], $offset));

        $data = array();
        while ($res = $sth->fetch()) {
            $textxml = $this->c['config_folders-extract'] . '/' . $res['stub'] . '/text.xml';

            $xmlstring = file_get_contents($textxml);
            $article = \nbkrnet\nbblog\utils\XmlExtractor::extractor($xmlstring, $language);
            // We have to replace the paths of the images.
            $article['content'] = preg_replace('/<img(.*)src="(.*)"/U', '<img$1src="' . $res['stub'] . '/$2"', $article['content']);

            $data[] = $article;
        }


        $sql = 'SELECT COUNT(*) FROM articles';
        $pagetotalno = ceil($this->c['db']->query($sql)->fetch()[0] / $this->c['config_articles-per-page']);

        $next = Null;
        if ($page < $pagetotalno) {
            $next = $page + 1;
        }

        $previous = Null;
        if ($page > 1) {
            $previous = $page - 1;
        }

        return $template->render(array('data' => $data, 'pageno' => $page, 'pagetotalno' => $pagetotalno, 'next' => $next, 'previous' => $previous));
    }

}
