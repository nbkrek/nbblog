<?php

namespace nbkrnet\nbblog\article;

class Article {

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

    public function renderHtml() {
        $twig = $this->c['twig'];

        // Actually display the file.
        $ourfile =  $this->c['config_folders-extract'] . '/' . $this->stub . '/text.xml';
        $template = $twig->loadTemplate('article.html'); 

        $language = 'de'; //TODO

        return $template->render(array('data' => \nbkrnet\nbblog\utils\XmlExtractor::extractor(file_get_contents($ourfile), $language)));
    }

}
