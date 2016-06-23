<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \nbkrnet\nbblog\article\Article as Article;
use \Slim\Exception\NotFoundException as NotFoundException;

require '../vendor/autoload.php';

$app = new \Slim\App;


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
        $article = Article::loadFromStub($stub);
    } catch (Exception $e) {
        throw new NotFoundException($request, $response);
    }
    $response->getBody()->write($article->renderHtml());

    return $response;
});
$app->run();
