<?php
/**
 * Example Test - A Real World Specter Test
 *
 * See the
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Tests;

use HelpScout\Specter\SpecterTestTrait;
use PHPUnit_Framework_TestCase;

/**
 * Class ExampleTest
 * @package HelpScout\Specter\Tests
 */
class ExampleTest extends PHPUnit_Framework_TestCase
{
    use SpecterTestTrait;

    /**
     * This could be a wrapper to run routes in your application
     *
     * @var WebTestClient
     */
    public $client;

    /**
     * Create our testing client that will execute routes
     */
    public function setUp()
    {
        $this->client = new WebTestClient();
        self::setFixtureFolder(TEST_FIXTURE_FOLDER);
    }

    /**
     * Clean up after the tests
     */
    public function tearDown()
    {
        unset($this->client);
        parent::tearDown();
    }

    /**
     * Test that our customer api route returns a appropriate response
     *
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function testCustomerRouteMeetsSpec()
    {
        $fixtureCustomerId = 37;
        self::assertResponseContent(
            $this->client->get('/api/v1/customer/'.$fixtureCustomerId),
            'customer'
        );
    }
}

// @codingStandardsIgnoreStart
/**
 * This is a pretend interface to a client that would execute routes in your
 * application. For example in Symfony, this might be a client created by
 * WebTestCase::createClient();
 *
 * @package HelpScout\Specter\Tests
 */
class WebTestClient
{
    use HttpFactory;

    /**
     * Run a route in your application
     *
     * In a real application, this would run a route and return the psr7
     * response object. Though, perhaps we should also allow this to return
     * a string.
     *
     * @param $url
     * @return \Slim\Http\Response
     */
    public function get($url)
    {
        // This isn't going to be particularly close to the customer.json file.
        // The diff should us that we meant to implement `fname` and `lname`
        $response = json_encode([
            'id'       => 3,
            'name'     => 'Eric Smith',
            'company'  => 'Serious International',
            'jobTitle' => 'Master Carpenter'
        ]);

        return $this->responseFactory($response);
    }
}
// @codingStandardsIgnoreEnd

/* End of file ExampleTest.php */
