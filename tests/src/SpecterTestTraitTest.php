<?php
/**
 * Specter Test Trait Test
 *
 * Exercise the test trait that we use to make assertions about a response
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Tests;

use Exception;
use HelpScout\Specter\SpecterTestTrait;
use PHPUnit_Framework_ExpectationFailedException;
use PHPUnit_Framework_TestCase;

/**
 * Class SpecterTestTraitTest
 * @package HelpScout\Specter\Tests
 */
class SpecterTestTraitTest extends PHPUnit_Framework_TestCase
{
    use HttpFactory, FakerFactory, SpecterTestTrait;

    /**
     * Assert that we pass if the response code is incorrect
     * @throws \InvalidArgumentException
     */
    public function testTraitPassesMatchingResponseCode()
    {
        $code     = 200;
        $response = $this->responseFactory('sample text', $code);
        self::assertResponseCode($response, $code);
    }

    /**
     * Assert that we fail if the response code is incorrect
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @throws \InvalidArgumentException
     */
    public function testTraitFailsIncorrectResponseCode()
    {
        $code     = 200;
        $mismatch = 404;
        $response = $this->responseFactory('sample text', $code);
        self::assertResponseCode($response, $mismatch);
    }

    /**
     * Assert that we fail if a property is missing from the json object
     * @throws \LogicException
     */
    public function testTraitFailsForMissingJsonProperty()
    {
        $spec   = '{"name":"@firstName@","email":"@email@"}';
        $actual = '{"name":"Jeffery Weiss"}';

        // Normally, logic is bad in these tests, but here we have to make
        // assertions about the failure exceptions that our test trait
        // should be throwing.
        try {
            self::assertResponseContent(
                $this->responseFactory($actual),
                $this->streamFactory($spec)
            );
        } catch (Exception $e) {
            self::assertInstanceOf(
                'PHPUnit_Framework_AssertionFailedError',
                $e,
                'SpecterTestTrait should have failed with a missing property.'
            );
            self::assertStringStartsWith(
                'There is no element under path',
                $e->getMessage(),
                'The error message from SpecterTestTrait was incorrect.'
            );
            return;
        }
        self::fail('SpecterTestTrait failed to notice the missing property');
    }

    /**
     * Assert that we fail if a property is of the incorrect type
     */
    public function testTraitFailsForIncorrectJsonPropertyType()
    {
        $spec   = '{"id":"@randomDigit@"}';
        $actual = '{"id":"this should be an integer"}';

        try {
            self::assertResponseContent(
                $this->responseFactory($actual),
                $this->streamFactory($spec)
            );
        } catch (Exception $e) {
            self::assertInstanceOf(
                'PHPUnit_Framework_AssertionFailedError',
                $e,
                'SpecterTestTrait should have failed with a missing property.'
            );
            self::assertContains(
                'does not match',
                $e->getMessage(),
                'The error message from SpecterTestTrait was incorrect.'
            );
            return;
        }
        self::fail(
            'SpecterTestTrait failed to notice the incorrect property type'
        );
    }

    /**
     * Assert that we fail if a property is of the incorrect type
     */
    public function testTraitFailsForExpandedPatterns()
    {
        $spec   = '{"email":"@freeEmail@"}';
        $actual = '{"email":"example.com"}';

        try {
            self::assertResponseContent(
                $this->responseFactory($actual),
                $this->streamFactory($spec)
            );
        } catch (Exception $e) {
            self::assertInstanceOf(
                'PHPUnit_Framework_AssertionFailedError',
                $e,
                'SpecterTestTrait should have failed with a missing property.'
            );
            self::assertContains(
                'does not match',
                $e->getMessage(),
                'The error message from SpecterTestTrait was incorrect.'
            );
            return;
        }
        self::fail(
            'SpecterTestTrait failed to notice the incorrect property type'
        );
    }
}

/* End of file SpecterTestTraitTest.php */
