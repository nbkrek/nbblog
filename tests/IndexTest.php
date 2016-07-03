<?php

class IndexTest extends PHPUnit_Framework_TestCase 
{

    protected function setUp() 
    {
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplearticle');
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplepage');
        system ('rm -rf ' . __DIR__ . '/../exampledata/static');
        system ('rm -rf ' . __DIR__ . '/../exampledata/invalidrenderer');
        $this->c = new \Pimple\Container();
        $this->c['config_folders-articles'] = __DIR__ . '/../exampledata';
        $this->c['config_folders-extract'] = __DIR__ . '/../exampledata';

        $this->c['twig'] = function ($c) {
            // Loading the templating system.
            require_once __DIR__ . '/../vendor/twig/twig/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();

            $loader = new Twig_Loader_Filesystem(__DIR__ . '/../testtemplate');

            return new Twig_Environment($loader, array(
                'cache' => '/tmp/templatecache'
                ));

        };

        include 'db.inc';

        $this->h = new \nbkrnet\nbblog\index\Index($this->c);
        
        // Adding an article
        new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
        
    }

    protected function tearDown() 
    {
        system ('rm -rf /tmp/nbblogtest.db');
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplearticle');
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplepage');
        system ('rm -rf ' . __DIR__ . '/../exampledata/static');
        system ('rm -rf ' . __DIR__ . '/../exampledata/invalidrenderer');
    }

    public function test_renderHtml() 
    {
        $this->assertTrue(strpos($this->h->renderHtml(), '<html') !== false);
    }

    public function test_renderHtmlSecondPage() 
    {
        $this->h->renderHtml(2);
    }

    /**
     * @expectedException Exception
     */
    public function test_noexisitingtag()
    {
        $h = new \nbkrnet\nbblog\index\Index($this->c, 'non-existing-tag');
    }


}
