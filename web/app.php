<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \nbkrnet\nbblog\article\Article as Article;
use \nbkrnet\nbblog\contenthandler\ContentHandler as ContentHandler;
use \Slim\Exception\NotFoundException as NotFoundException;

require '../vendor/autoload.php';


// The container for our app.


$container = new \Slim\Container;
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
        //
        // TODO: Make this somehow nicer.
        if (strpos($key, '-') !== false) {
            $nbblogcontainer['config_' . str_replace('REDIRECT_', '', $key)] = $value;
        }
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

    return $nbblogcontainer;
};

$app = new \Slim\App($container);


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
