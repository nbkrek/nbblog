<?php

class ArticleTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        system ('rm -rf ' . __DIR__ . '/../exampledata/examplearticle');

        $this->c = new \Pimple\Container();
        $this->c['config_folders-articles'] = __DIR__ . '/../exampledata';
        $this->c['config_folders-extract'] = __DIR__ . '/../exampledata';
        $this->c['twig'] = function ($c) {
            // Loading the templating system.
            require_once __DIR__ . '/../vendor/twig/twig/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();

            $loader = new Twig_Loader_Filesystem(__DIR__ . '/../defaulttemplate');

            return new Twig_Environment($loader, array(
                'cache' => '/tmp/templatecache'
                ));

        };
    }

    protected function tearDown() {
        //system ('rm -rf ' . __DIR__ . '/../exampledata/examplearticle');
    }

    /**
     * @expectedException Exception
     */
    public function test_InvalidStub() {
        $data = new \nbkrnet\nbblog\article\Article($this->c, 'ThisIsAnInvalidStub');
    }

    public function test_ValidStub() {
        $data = new \nbkrnet\nbblog\article\Article($this->c, 'examplearticle');
    }

    /**
     * @expectedException Exception
     */
    public function test_ValidStubWithoutTarBz() {
        $data = new \nbkrnet\nbblog\article\Article($this->c, 'examplearticlenortarfile');
    }

    public function test_updateTarFile() {
        // Mainly for code coverage. Not entirly sure how to check the deletion in between.
        $data = new \nbkrnet\nbblog\article\Article($this->c, 'examplearticle');
        system('touch -d "2100-01-01 12:00:00" ' . __DIR__ . '/../exampledata/examplearticle.tar.bz2');
        $data = new \nbkrnet\nbblog\article\Article($this->c, 'examplearticle');
        system('touch ' . __DIR__ . '/../exampledata/examplearticle.tar.bz2');
    }

    public function test_renderHtml() {
        $data = new \nbkrnet\nbblog\article\Article($this->c, 'examplearticle');
        $result = $data->renderHtml();
        $this->assertTrue(strpos($result, '<html') !== false);
    }

}
