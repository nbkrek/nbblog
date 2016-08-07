<?php

class PageTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        system ('rm -rf ' . __DIR__ . '/exampledata/examplepage');

        $this->c = new \Pimple\Container();
        $this->c['config_folders-pages'] = __DIR__ . '/exampledata';
        $this->c['language'] = 'en';
        $this->c['config_folders-extract'] = __DIR__ . '/exampledata';
        $this->c['twig'] = function ($c) {
            // Loading the templating system.
            require_once __DIR__ . '/../vendor/twig/twig/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();

            $loader = new Twig_Loader_Filesystem(__DIR__ . '/testtemplate');

            return new Twig_Environment($loader, array(
                'cache' => '/tmp/templatecache'
                ));

        };
    }

    protected function tearDown() {
        system ('rm -rf ' . __DIR__ . '/exampledata/examplepage');
    }


    public function test_renderHtml() {
        $xmlstring = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<page>

    <meta>
        <author>
            <name>Nicole Fleckenstein</name>
            <email>nf@nbkr.net</email>
        </author>
        <date>2016-06-16T12:13:14</date>
    </meta>

    <content language="de" title="Beispielblubb">

        <h1>Das ist die Hauptüberschrift</h1>

        Das hier ist <strong>Wichtig</strong>

        <img src='images/testimage.png' />

    </content>

    <content language="en" title="Beispielblubb in English">

        <h1>This is the main headding.</h1>

        This is <strong>important.</strong>

        <ul>
            <li>Liste</li>
            <li>Liste</li>
            <li>Liste</li>
        </ul>

        <ul>
            <li>Liste</li>
            <li>Liste</li>
            <li>Liste</li>
        </ul>

        <img src='images/testbild.png' />

    </content>

</page>
XML;
        $data = new \nbkrnet\nbblog\page\Page($this->c, $xmlstring);
        $result = $data->renderHtml();
        $this->assertTrue(strpos($result, '<html') !== false);
    }

}
