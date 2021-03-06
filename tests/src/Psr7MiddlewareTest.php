<?php
/**
 * Specter Middleware Test
 *
 * This confirms the behavior of the middleware by using the Slim helpers. It
 * does not use the SpecterTestTrait, so that it may be tested separately.
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Tests;

use HelpScout\Specter\Middleware\SpecterPsr7;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class Psr7MiddlewareTest extends TestCase implements SpecterMiddlewareTestInterface
{
    use Helpers\PSR7HttpFactory, Helpers\FakerFactory;

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
        $body       = '{"__specter": "", "name":"@name@"}';
        $seed       = 3;
        $expected   = $this->fakerFactory($seed)->name;
        $request    = $this->requestFactory()->withHeader('SpecterSeed', $seed);
        $response   = $this->responseFactory($body);
        $middleware = new SpecterPsr7();
        $callable   = $this->getCallableMiddleware();
        $response   = $middleware($request, $response, $callable);
        $json       = json_decode((string) $response->getBody(), true);

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
        $body       = '{"name":"@name@"}';
        $seed       = 3;
        $expected   = $this->fakerFactory($seed)->name;
        $request    = $this->requestFactory()->withHeader('SpecterSeed', $seed);
        $response   = $this->responseFactory($body);
        $middleware = new SpecterPsr7();
        $callable   = $this->getCallableMiddleware();
        $response   = $middleware($request, $response, $callable);

        self::assertSame(
            $body,
            (string) $response->getBody(),
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
        $middleware = new SpecterPsr7();
        $middleware($request, $response, $this->getCallableMiddleware());
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
        $middleware = new SpecterPsr7();
        $callable   = $this->getCallableMiddleware();
        $response   = $middleware($request, $response, $callable);
        $json       = json_decode((string) $response->getBody(), true);

        self::assertStringStartsWith(
            'Unsupported formatter',
            $json['name'],
            'The error message from Specter was not correct'
        );
    }
}

/* End of file Psr7MiddlewareTest.php */
