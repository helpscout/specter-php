<?php
namespace HelpScout\Specter\Tests;

use HelpScout\Specter\Middleware\SpecterIlluminate;
use HelpScout\Specter\Tests\Helpers\FakerFactory;
use HelpScout\Specter\Tests\Helpers\IlluminateHttpFactory;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class IlluminateMiddlewareTest extends TestCase implements SpecterMiddlewareTestInterface
{
    use FakerFactory;
    use IlluminateHttpFactory;

    /**
     * Assert that we process a Specter JSON file to random data.
     *
     * We run the middleware with a seed header so that we can assert that the
     * response matches the expectation.
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function testMiddlewareCanProcessSimpleJson()
    {
        $body     = '{"__specter": "", "name":"@name@"}';
        $seed     = 3;
        $expected = $this->fakerFactory($seed)->name;
        $request  = $this->requestFactory();

        $request->headers->set('SpecterSeed', $seed);

        $response   = $this->responseFactory($body);
        $middleware = new SpecterIlluminate;
        $callable   = $this->getCallableMiddleware($response);
        $response   = $middleware->handle($request, $callable);
        $json       = json_decode((string) $response->content(), true);

        self::assertSame($expected, $json['name'], 'Incorrect json value');
    }

    /**
     * Assert that we ignore a file without the __specter property trigger
     *
     * We run the middleware with a seed header so that we can assert that the
     * response matches the expectation.
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function testMiddlewareCanIgnoreNonSpecterFile()
    {
        $body    = '{"name":"@name@"}';
        $seed    = 3;
        $request = $this->requestFactory();

        $request->headers->set('SpecterSeed', $seed);

        $response   = $this->responseFactory($body);
        $middleware = new SpecterIlluminate;
        $callable   = $this->getCallableMiddleware($response);
        $response   = $middleware->handle($request, $callable);

        self::assertSame(
            $body,
            (string) $response->getContent(),
            'Specter did not ignore a non-specter file'
        );
    }

    /**
     * Assert that the correct exception is thrown for invalid Specter JSON.
     *
     * @return            void
     * @expectedException LogicException
     * @throws            InvalidArgumentException
     * @throws            RuntimeException
     */
    public function testMiddlewareFailsOnInvalidJson()
    {
        $body       = '{"__specter": "", "name":"@name@"';
        $request    = $this->requestFactory();
        $response   = $this->responseFactory($body);
        $middleware = new SpecterIlluminate;
        $middleware->handle($request, $this->getCallableMiddleware($response));
    }

    /**
     * Assert that the correct output is sent for invalid Specter selector.
     *
     * @return void
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function testMiddlewareFailsOnInvalidProviderJson()
    {
        $body       = '{"__specter": "", "name":"@nameButMaybeMisspelled@"}';
        $request    = $this->requestFactory();
        $response   = $this->responseFactory($body);
        $middleware = new SpecterIlluminate;
        $callable   = $this->getCallableMiddleware($response);
        $response   = $middleware->handle($request, $callable);
        $json       = json_decode((string) $response->getContent(), true);

        self::assertStringStartsWith(
            'Unsupported formatter',
            $json['name'],
            'The error message from Specter was not correct'
        );
    }
}
