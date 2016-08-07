<?php

class XMLExtractorTest extends PHPUnit_Framework_TestCase {

    public function testXmlExtractor_for_article()
    {
        $filecontent = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<article>

    <meta>
        <author>
            <name>Nicole Fleckenstein</name>
            <email>nf@nbkr.net</email>
        </author>
        <date>2016-06-16T12:13:14</date>
        <tags>
            <tag>Kategorie 1</tag>
            <tag>Kategorie 2</tag>
        </tags>
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

</article>
XML;

        $data = \nbkrnet\nbblog\utils\XmlExtractor::extractor($filecontent); 

        foreach (array('date', 'author', 'content', 'title', 'languages', 'type', 'tags') as $key) {
            $this->assertArrayHasKey($key, $data);
        }

        $this->assertTrue(in_array('Kategorie 1', $data['tags']));
        $this->assertTrue(in_array('Kategorie 2', $data['tags']));

    }

    public function testXmlExtractor_for_page()
    {
        $filecontent = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<page>

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

        $data = \nbkrnet\nbblog\utils\XmlExtractor::extractor($filecontent); 

        foreach (array('content', 'title', 'languages', 'type') as $key) {
            $this->assertArrayHasKey($key, $data);
        }


    }
}
