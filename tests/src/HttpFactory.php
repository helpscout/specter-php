<?php
/**
 * PSR7 HTTP Objects Factory
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Tests;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Body;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;

/**
 * Trait HttpFactory
 * @package HelpScout\Specter\Tests
 */
trait HttpFactory
{
    /**
     * Create a final step in a mock middleware stack to access the body.
     *
     * @return \Closure
     * @throws \RuntimeException
     */
    public function getCallableMiddleware()
    {
        return function (
            ServerRequestInterface $request,
            ResponseInterface $response
        ) {
            return $response->getBody()->getContents();
        };
    }

    /**
     * Create a php stream with text.
     *
     * @param string $text
     * @param string $mode
     * @return resource
     */
    public function streamFactory($text, $mode = 'r+')
    {
        $stream = fopen('php://temp', $mode);
        fwrite($stream, $text);
        rewind($stream);

        return $stream;
    }

    /**
     * Create a new PSR7 Response Object.
     *
     * @param string  $content
     * @param integer $code
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function responseFactory($content, $code = 200)
    {
        $headers = new Headers();
        $body    = new Body($this->streamFactory($content));

        return new Response($code, $headers, $body);
    }

    /**
     * Create a new PSR7 Request Object.
     *
     * @return Request
     */
    public function requestFactory()
    {
        $env           = Environment::mock();
        $uri           = Uri::createFromString('https://example.com/foo/bar?abc=123');
        $headers       = Headers::createFromEnvironment($env);
        $cookies       = ['user' => 'john', 'id' => '123'];
        $serverParams  = $env->all();
        $body          = new RequestBody();
        $uploadedFiles = UploadedFile::createFromEnvironment($env);
        $request = new Request(
            'GET',
            $uri,
            $headers,
            $cookies,
            $serverParams,
            $body,
            $uploadedFiles
        );

        return $request;
    }
}

/* End of file HttpFactory.php */
