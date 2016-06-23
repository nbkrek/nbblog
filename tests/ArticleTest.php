<?php

class ArticleTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException Exception
     */
    public function test_loadFromInvalidStub() {

        $data = \nbkrnet\nbblog\article\Article::loadFromStub('ThisIsAnInvalidStub');

    }

    public function test_loadFromString() {

        $data = \nbkrnet\nbblog\article\Article::loadFromString('<?xml version="1.0" encoding="UTF-8" standalone="no" ?><article><meta></meta><content></content></article>');

    }

    /**
     * @expectedException Exception
     */
    public function test_loadInvalidString() {
        $data = \nbkrnet\nbblog\article\Article::loadFromString('This is <invalid> XML.');
    }

}
