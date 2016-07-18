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

                // Get the article id from the database
                $sth = $this->c['db']->prepare('SELECT id FROM articles WHERE stub = ?');
                $res = $sth->execute(array($this->stub));

                $articleid = Null;
                if (! isset($sth->fetch()[0])) {
                    // Insert new article
                    $sth2 = $this->c['db']->prepare('INSERT INTO articles (stub, publishdate) VALUES(?, ?)');
                    $sth2->execute(array($this->stub, $this->xml['date']));
                    $articleid = $this->c['db']->lastInsertId();
                } else {
                    $aticleid = $sth->fetch()[0];
                }

                // Remove existing tag2article assignements for this article
                $sth = $this->c['db']->prepare('DELETE FROM tags2articles WHERE articleid = ?');
                $sth->execute(array($articleid));

                // We also need to add the tags to the database and link them
                foreach ($this->xml['tags'] as $tag) {
                    // Normalise tag
                    $tag = strtolower($tag);
                    $tag = str_replace(' ', '-', $tag);

                    // Get the tag id from the table, if it exists, otherwise create it.
                    $sth = $this->c['db']->prepare('SELECT id FROM tags WHERE tag = ?');
                    $res = $sth->execute(array($tag));
                    $tagid = null;
                    if (! isset($sth->fetch()[0])) {
                        // Insert new tag
                        $sth2 = $this->c['db']->prepare('INSERT INTO tags (tag) VALUES(?)');
                        $sth2->execute(array($tag));
                        $tagid = $this->c['db']->lastInsertId();
                    } else {
                        $tagid = $sth->fetch()[0];
                    }


                    // Add the new link
                    $sth = $this->c['db']->prepare('INSERT INTO tags2articles (tagid, articleid) VALUES(?, ?)');
                    $sth->execute(array($tagid, $articleid));
                }
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

            // Create the folder for the article
            if (! file_exists($folderpath)) {
                mkdir($folderpath);
            }

            // Extract the tar.bz2 file.
            system("tar --touch -C " . $folderpath . " -xjf $filepath");
            
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
