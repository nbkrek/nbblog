<?php

class IndexTest extends PHPUnit_Framework_TestCase 
{

    protected function setUp() 
    {
        system ('rm -rf ' . __DIR__ . '/exampledata/examplearticle');
        system ('rm -rf ' . __DIR__ . '/exampledata/examplearticle*/');
        system ('rm -rf ' . __DIR__ . '/exampledata/examplepage');
        system ('rm -rf ' . __DIR__ . '/exampledata/static');
        system ('rm -rf ' . __DIR__ . '/exampledata/invalidrenderer');
        system ('rm -rf /tmp/templatecache');
        $this->c = new \Pimple\Container();
        $this->c['config_folders-articles'] = __DIR__ . '/exampledata';
        $this->c['config_folders-extract'] = __DIR__ . '/exampledata';
        $this->c['config_articles-per-page'] = 2;

        $this->c['twig'] = function ($c) {
            // Loading the templating system.
            require_once __DIR__ . '/../vendor/twig/twig/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();

            $loader = new Twig_Loader_Filesystem(__DIR__ . '/testtemplate');

            return new Twig_Environment($loader, array(
                'cache' => '/tmp/templatecache'
                ));

        };

        require 'db.inc';

        // Adding articles
        new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
        new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle1');
        new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle2');
        new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle3');

        $this->h = new \nbkrnet\nbblog\index\Index($this->c);
        
        
    }

    protected function tearDown() 
    {
        system ('rm -rf /tmp/nbblogtest.db');
        system ('rm -rf ' . __DIR__ . '/exampledata/examplearticle');
        system ('rm -rf ' . __DIR__ . '/exampledata/examplearticle*/');
        system ('rm -rf ' . __DIR__ . '/exampledata/examplepage');
        system ('rm -rf ' . __DIR__ . '/exampledata/static');
        system ('rm -rf ' . __DIR__ . '/exampledata/invalidrenderer');
        system ('rm -rf /tmp/templatecache');
    }

    public function test_renderHtml() 
    {
        $this->assertTrue(strpos($this->h->renderHtml(), 'Page 1 of 2') !== false);
        $this->assertTrue(strpos($this->h->renderHtml(), '<a href="/page/2">Next</a>') !== false);
    }

    public function test_renderHtmlSecondPage() 
    {
        $this->assertTrue(strpos($this->h->renderHtml(2), 'Page 2 of 2') !== false);
        $this->assertTrue(strpos($this->h->renderHtml(2), '<a href="/page/1">Previous</a>') !== false);
    }


    public function test_renderHtmlWithTag() 
    {
        $this->t = new \nbkrnet\nbblog\index\Index($this->c, 'kategorie-1');
        $this->assertTrue(strpos($this->t->renderHtml(), 'Page 1 of 2') !== false);
        $this->assertTrue(strpos($this->t->renderHtml(), '<a href="/tag/kategorie-1/page/2">Next</a>') !== false);
    }

    /**
     * @expectedException Exception
     */
    public function test_noexisitingtag()
    {
        $h = new \nbkrnet\nbblog\index\Index($this->c, 'non-existing-tag');
    }


}
