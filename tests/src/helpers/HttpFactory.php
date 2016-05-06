<?php
/**
 * PSR7 HTTP Objects Factory
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Tests\Helpers;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use RuntimeException;

/**
 * Trait HttpFactory
 *
 * @package HelpScout\Specter\Tests
 */
trait HttpFactory
{
    /**
     * Create a final step in a mock middleware stack to access the body.
     *
     * @return Closure
     * @throws RuntimeException
     */
    public function getCallableMiddleware()
    {
        return function (
            RequestInterface $request,
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
     *
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
     *
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function responseFactory($content, $code = 200)
    {
        $headers = [];
        return new Response($code, $headers, $content);
    }

    /**
     * Create a new PSR7 Request Object.
     *
     * @return Request
     * @throws \InvalidArgumentException
     */
    public function requestFactory()
    {
        $uri     = 'https://example.com/foo/bar?abc=123';
        $headers = [];
        $body    = '';
        $request = new Request(
            'GET',
            $uri,
            $headers,
            $body
        );

        return $request;
    }
}

/* End of file HttpFactory.php */
