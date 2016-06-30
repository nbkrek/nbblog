<?php

class ContentHandlerTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplearticle');
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplepage');
        system ('rm -rf ' . __DIR__ . '/../exampledata/static');
        system ('rm -rf ' . __DIR__ . '/../exampledata/invalidrenderer');
        system ('rm -rf /tmp/nbblogtest.db');

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

    }

    protected function tearDown() {
        #system ('rm -rf ' . __DIR__ . '/../exampledata/examplearticle');
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplepage');
        system ('rm -rf ' . __DIR__ . '/../exampledata/static');
        system ('rm -rf ' . __DIR__ . '/../exampledata/invalidrenderer');
        //system ('rm -rf /tmp/nbblogtest.db');
    }

    /**
     * @expectedException Exception
     */
    public function test_InvalidStub() {
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'ThisIsAnInvalidStub');
    }

    public function test_ValidStub() {
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
    }

    /**
     * @expectedException Exception
     */
    public function test_ValidStubWithoutTarBz() {
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticlenortarfile');
    }

    public function test_ContentTypeArticle() {
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
        $this->assertEquals('article', $data->getContentType());
    }

    public function test_ContentTypePage() {
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplepage');
        $this->assertEquals('page', $data->getContentType());
    }

    public function test_ContentTypeUnknown() {
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'static');
        $this->assertEquals('unknown', $data->getContentType());
    }

    public function test_updateTarFile() {
        // Mainly for code coverage. Not entirly sure how to check the deletion in between.
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
        system('touch -d "2100-01-01 12:00:00" ' . __DIR__ . '/../exampledata/examplearticle.tar.bz2');
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
        system('touch ' . __DIR__ . '/../exampledata/examplearticle.tar.bz2');
    }

    public function test_getArticleRenderer() {
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
        $emp = $data->getRenderer();
        $this->assertEquals('nbkrnet\nbblog\article\Article', get_class($emp));
    }

    public function test_getPageRenderer() {
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplepage');
        $emp = $data->getRenderer();
        $this->assertEquals('nbkrnet\nbblog\page\Page', get_class($emp));
    }

    /**
     * @expectedException Exception
     */
    public function test_unkownRenderer() {
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'invalidrenderer');
        $emp = $data->getRenderer();
    }

    public function test_articleAddedToIndexDb() {
        $res = $this->c['db']->exec('DELETE FROM articles');
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
        $res = $this->c['db']->query("SELECT COUNT(*) AS res FROM articles WHERE stub = 'examplearticle'")->fetch();
        $this->assertEquals('1', $res['res']);
    }

    public function test_articleAddedToIndexDbOnlyOnce() {
        $res = $this->c['db']->exec('DELETE FROM articles');
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
        $res = $this->c['db']->query("SELECT COUNT(*) AS res FROM articles WHERE stub = 'examplearticle'")->fetch();
        $this->assertEquals('1', $res['res']);
    }

    public function test_tagsIntoDb() {
        $res = $this->c['db']->exec('DELETE FROM tags2articles');
        $res = $this->c['db']->exec('DELETE FROM articles');
        $res = $this->c['db']->exec('DELETE FROM tags');
        $data = new \nbkrnet\nbblog\contenthandler\ContentHandler($this->c, 'examplearticle');
        $res = $this->c['db']->query("SELECT COUNT(*) AS res FROM articles AS a, tags AS t, tags2articles AS ta WHERE ta.articleid = a.id AND ta.tagid = t.id AND t.tag = 'kategorie-1' AND a.stub = 'examplearticle'")->fetch();
        $this->assertEquals('1', $res['res']);
        $res = $this->c['db']->query("SELECT COUNT(*) AS res FROM articles AS a, tags AS t, tags2articles AS ta WHERE ta.articleid = a.id AND ta.tagid = t.id AND t.tag = 'kategorie-2' AND a.stub = 'examplearticle'")->fetch();
        $this->assertEquals('1', $res['res']);
    }
}
