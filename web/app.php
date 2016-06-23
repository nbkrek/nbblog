<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \nbkrnet\nbblog\article\Article as Article;
use \Slim\Exception\NotFoundException as NotFoundException;

require '../vendor/autoload.php';

$app = new \Slim\App;

// Our Pimple Container for the nbblog software.
// I'm not sure if we could/should use the slim container ourself.
$nbblogcontainer = new \Pimple\Container();
foreach ($_SERVER as $key => $value) {
    // Our configuration is stored in the webserver environment using lowerkey values.
    // so we just transfer every lowerkey value to our Container and we have our configuration
    // done.
    if (ctype_lower($key)) {
        $nbblogcontainer['config_' . $key] = $value;
    }
}

$nbblogcontainer['twig'] = function ($c) {
            // Loading the templating system.
            require_once __DIR__ . '/../vendor/twig/twig/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();

            $loader = new Twig_Loader_Filesystem($c['config_folders-template']);

            return new Twig_Environment($loader, array(
                'cache' => $c['config_folders-templatecache'];
                ));

        };


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
    $stub = substr($stub, 0, strlen($sub) -1);

    try {
        $article = Article::loadFromStub($nbblogcontainer, $stub);
    } catch (Exception $e) {
        throw new NotFoundException($request, $response);
    }
    $response->getBody()->write($article->renderHtml());

    return $response;
});

$app->run();
