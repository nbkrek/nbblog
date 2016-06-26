<?php

class IndexTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplearticle');
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplepage');
        system ('rm -rf ' . __DIR__ . '/../exampledata/static');
        system ('rm -rf ' . __DIR__ . '/../exampledata/invalidrenderer');
        $this->c = new \Pimple\Container();
        $this->c['config_folders-articles'] = __DIR__ . '/../exampledata';
        $this->c['config_folders-extract'] = __DIR__ . '/../exampledata';
        $this->c['db'] = function ($c) {
            $db = new \PDO('sqlite:/tmp/nbblogtest.db');
            $db->exec("CREATE TABLE IF NOT EXISTS articles (
                           id INTEGER PRIMARY KEY, 
                           stub TEXT, 
                           publishdate DATETIME)");

            return $db;
        };

        $this->c['twig'] = function ($c) {
            // Loading the templating system.
            require_once __DIR__ . '/../vendor/twig/twig/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();

            $loader = new Twig_Loader_Filesystem(__DIR__ . '/../defaulttemplate');

            return new Twig_Environment($loader, array(
                'cache' => '/tmp/templatecache'
                ));

        };

        $this->h = new \nbkrnet\nbblog\index\Index($this->c);
        
        // Adding an article
        new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
        
    }

    protected function tearDown() {
        system ('rm -rf /tmp/nbblogtest.db');
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplearticle');
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplepage');
        system ('rm -rf ' . __DIR__ . '/../exampledata/static');
        system ('rm -rf ' . __DIR__ . '/../exampledata/invalidrenderer');
    }

    public function test_renderHtml() {
        $this->h->renderHtml();
    }

    public function test_renderHtmlSecondPage() {
        $this->h->renderHtml(2);
    }


}
