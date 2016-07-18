<?php

namespace nbkrnet\nbblog\index;

class Index {

    function __construct($container, $tag = Null) {
        $this->c = $container;

        $this->tag = $tag;
        if ($this->tag != Null) {
            // Count if this tag exists.
            $sql = 'SELECT COUNT(*) FROM tags AS t WHERE t.tag = ?';
            $sth = $this->c['db']->prepare($sql);
            $sth->execute(array($this->tag));
            $res = $sth->fetch();
            if ($res[0] == 0) {
                throw new \Exception('Unkown Tag');
            }


        }
    }

    public function renderHtml($page=1) {
        $twig = $this->c['twig'];
        $template = $twig->loadTemplate('index.html'); 
        $language = 'de'; //TODO

        $offset = $this->c['config_articles-per-page'] * ($page - 1);

        $sth = Null;
        if ($this->tag == Null) { 
            $sql = 'SELECT stub FROM articles ORDER BY publishdate DESC LIMIT ? OFFSET ?';
            $sth = $this->c['db']->prepare($sql);
            $sth->execute(array($this->c['config_articles-per-page'], $offset));
        } else {
            // Select only articles for specific tag. TODO
            $sql = 'SELECT a.stub AS stub FROM articles AS a, tags AS t, tags2articles AS ta WHERE t.tag = ? AND ta.tagid = t.id and ta.articleid = a.id ORDER BY a.publishdate DESC LIMIT ? OFFSET ?';
            $sth = $this->c['db']->prepare($sql);
            $sth->execute(array($this->tag, $this->c['config_articles-per-page'], $offset));
        }

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

        return $template->render(array('data' => $data, 'pageno' => $page, 'pagetotalno' => $pagetotalno, 'next' => $next, 'previous' => $previous, 'tag' => $this->tag));
    }

}
