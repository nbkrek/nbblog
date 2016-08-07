<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \nbkrnet\nbblog\contenthandler\ContentHandler as ContentHandler;
use \nbkrnet\nbblog\index\Index as Index;
use \Slim\Exception\NotFoundException as NotFoundException;

require '../vendor/autoload.php';


// The container for our app.


$container = new \Slim\Container;

if (isset($_SERVER['debug-mode']) ||
    isset($_SERVER['REDIRECT_debug-mode'])) {
    $container['settings']['displayErrorDetails'] = true;
}

$container['nbblogcontainer'] = function ($c) {
    $nbblogcontainer = new \Pimple\Container;
    foreach ($_SERVER as $key => $value) {
        // Our configuration is stored in the webserver environment using lowerkey values.
        // so we just transfer every lowerkey value to our Container and we have our configuration
        // done. 
        //
        // However, PHP running in CGI Mode shows a problem as mod_rewrite will add a "REDIRECT_" to 
        // the variable name. Switching to mod_php or fcgi removes this behaviour, but hat might
        // not always be an option. So I'm tenting to remove the REDIRECT_ from keys and therefore
        // make it work on all php installations.
        //
        // Hoever there is one parameter called 'REDIRECT_URL' that doesn't come from mod_rewrite
        // so i have to exclude this also
        if (ctype_lower(str_replace('REDIRECT_', '', $key))) {
            $nbblogcontainer['config_' . str_replace('REDIRECT_', '', $key)] = $value;
        }
    }

    // Language selection 
    // - default value is 'en'
    $nbblogcontainer['language'] = 'en';
    // I thought about using the browser settings for the language, but that isn't really realiable.

    // - if there is a valid url parameter we use this
    if (isset($_GET['language']) && strlen($_GET['language'] == 2 && preg_match('/^[a-z]+$/', $_GET['language'])) {
        $nbblogcontainer['language'] = $_GET['language'];
    }
    // - if the language is set in the environment we just use this language
    if (isset($nbblogcontainer['config_language'])) {
        $nbblogcontainer['language'] = $nbblogcontainer['config_language'];
    }

    $nbblogcontainer['twig'] = function ($c) {
                // Loading the templating system.
                require_once __DIR__ . '/../vendor/twig/twig/lib/Twig/Autoloader.php';
                Twig_Autoloader::register();

                $loader = new Twig_Loader_Filesystem($c['config_folders-template']);

                return new Twig_Environment($loader, array(
                    'cache' => $c['config_folders-templatecache'],
                    ));

            };

    $nbblogcontainer['db'] = function ($c) {
            $db = new \PDO('sqlite:' . $c['config_database-file']);
            $db->exec('PRAGMA foreign_keys = ON;');
            $db->exec("CREATE TABLE IF NOT EXISTS articles (
                           id INTEGER PRIMARY KEY, 
                           stub TEXT, 
                           publishdate DATETIME)");

            $db->exec("CREATE TABLE IF NOT EXISTS tags (
                           id INTEGER PRIMARY KEY, 
                           tag TEXT
                           )");


            $db->exec("CREATE UNIQUE INDEX IF NOT EXISTS tagidx ON tags (tag)");

            $db->exec("CREATE TABLE IF NOT EXISTS languages (
                           id INTEGER PRIMARY KEY, 
                           language TEXT
                           )");

            $db->exec("CREATE UNIQUE INDEX IF NOT EXISTS languageidx ON languages(language)");

            $db->exec("CREATE TABLE IF NOT EXISTS tags2articles (
                           tagid INTEGER,
                           articleid INTEGER,
                           FOREIGN KEY(tagid) REFERENCES tags(id),
                           FOREIGN KEY(articleid) REFERENCES articles(id)
                           )");

            $db->exec("CREATE TABLE IF NOT EXISTS articles2languages (
                           languageid INTEGER,
                           articleid INTEGER,
                           FOREIGN KEY(languageid) REFERENCES languages(id),
                           FOREIGN KEY(articleid) REFERENCES articles(id)
                           )");
            return $db;
        };

    return $nbblogcontainer;
};

$app = new \Slim\App($container);

$app->get('/', function (Request $request, Response $response) {
    // Home - show the index
    $index = new Index($this->get('nbblogcontainer'));
    $response->getBody()->write($index->renderHtml());
    return $response;
});

$app->get('/tag/{tagid:[a-z0-9-]+$}', function (Request $request, Response $response) {
    //Tag without / in the url. Redirect to the correct with /.
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) != '/') {
        $uri = $uri->withPath($path . '/');
        return $response->withRedirect((string)$uri, 301);
    }
});

$app->get('/tag/{tagid:[a-z0-9-]+/$}', function (Request $request, Response $response, $args) {
    // Tag - show the index
    try {
        // Index creation will fail if the given tag doesn't exist inside the database.
        $index = new Index($this->get('nbblogcontainer'), $tagid);
    } catch (Exception $e) {
        return $response->withRedirect('/', 301);
    }
    return $response->getBody()->write($index->renderHtml());
}


$app->get('/{stub:[a-z0-9-]+$}', function (Request $request, Response $response, $args) {
    //Article without / in the url. Redirect to the correct with /.
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) != '/') {
        $uri = $uri->withPath($path . '/');
        return $response->withRedirect((string)$uri, 301);
    }
});

$app->get('/{stub:[a-z0-9-]+/$}', function (Request $request, Response $response, $args) {

    // The stub is the thing between the slashes.
    $stub = $args['stub'];
    $stub = substr($stub, 0, strlen($stub) -1);

    try {
        $content = new ContentHandler($this->get('nbblogcontainer'), $stub);
    } catch (Exception $e) {
        throw new NotFoundException($request, $response);
    }

    $renderer = $content->getRenderer();
    try {
        $response->getBody()->write($renderer->renderHtml());
    } catch (Exception $e) {
        return $response->withRedirect('/', 301);
    }

    return $response;
});

$app->run();
