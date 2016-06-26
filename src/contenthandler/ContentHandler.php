<?php

namespace nbkrnet\nbblog\contenthandler;

class ContentHandler {

    function __construct($container, $stub) {

        $this->c = $container;
        $this->stub = $stub;

        // Check if the this->stub is valid.
        // The app.php does this already, but it doesn't hurt to do
        // it again, just in case ...
        if (! preg_match('/^[0-9a-z-]+$/', $this->stub)) {
            throw new \Exception('Stub is invalid.');
        }
       

        $filepath = $this->c['config_folders-articles'] . '/' . $this->stub . '.tar.bz2';

        if (! file_exists($filepath)) {
            throw new \Exception('Could not find tar.bz2 file to that stub.');
        }

        // See if the source file exists.
        $this->extractTarBz();

        // We need the xml data in many places so we extract it once and for all.
        // If it is an article, add it to the database.
        $textxml = $this->c['config_folders-extract'] . '/' . $this->stub . '/text.xml';
        $this->xmlstring = Null;

        // We also have to set the content type for later purposes
        $this->contenttype = 'unknown';
        if (file_exists($textxml)) {

            $this->xmlstring = file_get_contents($textxml);
            $this->xml = \nbkrnet\nbblog\utils\XmlExtractor::extractor($this->xmlstring);

            if ($this->xml['type'] == 'article') {
                $this->contenttype = 'article';

                // We need to add this to the index -- but only once!
                $sth = $this->c['db']->prepare('DELETE FROM articles WHERE stub = ?');
                $sth->execute(array($this->stub));

                $sth = $this->c['db']->prepare('INSERT INTO articles (stub, publishdate) VALUES(?, ?)');
                $sth->execute(array($this->stub, $this->xml['date']));
            }

            if ($this->xml['type'] == 'page') {
                $this->contenttype = 'page';
            }
        }

    }

    private function extractTarBz() {

        $folderpath = $this->c['config_folders-extract'] . '/' . $this->stub . '/';
        $filepath = $this->c['config_folders-articles'] . '/' . $this->stub . '.tar.bz2';

        if (! file_exists($folderpath)
            || filemtime($folderpath) < filemtime($filepath)) {

            if (file_exists($folderpath)) {
                // Delete the folder 
                system("rm -rf $folderpath");
            }

            // Extract the tar.bz2 file.
            system("tar --touch -C " . $this->c['config_folders-extract'] . " -xjf $filepath");
            
        }
    }

    public function getContentType() {
        return $this->contenttype;
    }

    public function getRenderer() {
        $type = $this->getContentType();

        if ($type == 'article') {
            return new \nbkrnet\nbblog\article\Article($this->c, $this->xmlstring);
        }

        if ($type == 'page') {
            return new \nbkrnet\nbblog\page\Page($this->c, $this->xmlstring);
        }

        // No valid type found
        throw new \Exception('Non-displayable content');

    }


}
