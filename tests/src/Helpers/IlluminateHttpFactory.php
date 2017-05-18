<?php
namespace HelpScout\Specter\Tests\Helpers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait IlluminateHttpFactory
{
    public function getCallableMiddleware(Response $response)
    {
        return function (Request $request) use ($response) {
            return $response;
        };
    }

    public function requestFactory()
    {
        $uri = 'https://example.com/foo/bar?abc=123';
        return Request::create($uri, 'GET');
    }

    public function responseFactory($content, $code = 200)
    {
        $response = new Response;
        $response->setContent($content);
        $response->setStatusCode($code);

        return $response;
    }
}
