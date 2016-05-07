<?php
/**
 * Specter Middleware for JSON Mock Data responses
 *
 * A route should return JSON data in the Specter format.
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter;

use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7\Stream;
use RuntimeException;

/**
 * Class SpecterMiddleware
 *
 * @package HelpScout\Specter
 */
class SpecterMiddleware
{
    /**
     * JSON must have this property to trigger processing
     *
     * @var string
     */
    private $specterTrigger = '__specter';

    /**
     * Specter JSON Fake Data
     *
     * The route should return json data of Specter format, and this middleware
     * will substitute fake data into it.
     *
     * @param RequestInterface  $request  PSR7 request
     * @param ResponseInterface $response PSR7 response
     * @param callable          $next     Next middleware
     *
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        // We are a post processor
        $response = $next($request, $response);

        // Decode the json returned by the route and prepare it for mock data
        // processing.
        $fixture = @json_decode($response->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new LogicException(
                'Failed to parse json string. Error: '.json_last_error_msg()
            );
        }

        // We will not process files without the Specter trigger, and instead
        // return an unchanged response.
        if (!array_key_exists($this->specterTrigger, $fixture)) {
            return $response;
        }

        // Process the fixture data, using a seed in case the designer wants
        // a repeatable result.
        $seed    = $request->getHeader('SpecterSeed');
        $specter = new Specter(array_shift($seed));
        $json    = $specter->substituteMockData($fixture);

        // Prepare a fresh body stream
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, json_encode($json));
        rewind($stream);
        $body = new Stream($stream);

        // Return an immutable body in a cloned $request object
        return $response->withBody($body);
    }
}

/* End of file SpecterMiddleware.php */
