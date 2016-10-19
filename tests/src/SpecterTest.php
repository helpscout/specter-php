<?php
/**
 * Specter Test
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Tests;

use HelpScout\Specter\Specter;
use PHPUnit_Framework_TestCase;

/**
 * Class SpecterTest
 *
 * @package HelpScout\Specter\Tests
 */
class SpecterTest extends PHPUnit_Framework_TestCase
{
    use Helpers\FakerFactory;

    /**
     * A quick health check to make sure we're namespaced and lint free
     *
     * @return void
     */
    public function testSpecterCanInitSpecter()
    {
        $specter = new Specter();
        self::assertInstanceOf(
            'HelpScout\Specter\Specter',
            $specter
        );
    }

    /**
     * Assert that we can process simple json and return a consistent result
     * by using a seed value
     *
     * Note: We have to be careful to call the expected values in the same
     *       order that we find them in the fixture file. We start with the
     *       same `$seed` and we have to march in lockstep with the Specter
     *       producer so that the values are generated in the same order.
     *
     * @test
     * @return void
     */
    public function specterCanProcessJson()
    {
        $seed      = 5;
        $faker     = $this->fakerFactory($seed);
        $id        = $faker->randomDigitNotNull;
        $firstName = $faker->firstName;
        $lastName  = $faker->lastName;
        $specter   = new Specter($seed);
        $json      = file_get_contents(TEST_FIXTURE_FOLDER.'/customer.json');
        $fixture   = json_decode($json, true);
        $data      = $specter->substituteMockData($fixture);

        self::assertSame($id, $data['id']);
        self::assertSame($firstName, $data['fname']);
        self::assertSame($lastName, $data['lname']);
    }

    /**
     * Specter can generate random numbers
     *
     * @test
     * @return void
     */
    public function specterCanGenerateNumbers()
    {
        $specter = new Specter();
        $json    = file_get_contents(TEST_FIXTURE_FOLDER.'/numbers.json');
        $fixture = json_decode($json, true);
        $data    = $specter->substituteMockData($fixture);

        self::assertGreaterThanOrEqual(
            1000,
            $data['id'],
            'The random id should have been between 1000 and 9000'
        );
        self::assertLessThanOrEqual(
            9000,
            $data['id'],
            'The random id should have been between 1000 and 9000'
        );

        self::assertInternalType(
            'integer',
            $data['quantity'],
            "The quantity should be an integer"
        );

        self::assertRegExp(
            '~[0-9]~',
            $data['description'],
            "The description should have a number in it"
        );
    }

    /**
     * Specter should be able to select a random value from a list
     *
     * @test
     * @return void
     */
    public function specterCanSelectRandomValues()
    {
        $specter = new Specter();
        $json    = file_get_contents(TEST_FIXTURE_FOLDER.'/random-element.json');
        $fixture = json_decode($json, true);
        $data    = $specter->substituteMockData($fixture);

        self::assertContains(
            $data['type'],
            ['customer', 'vendor', 'owner'],
            'The random value was not in the expected set'
        );

        self::assertEquals(
            2,
            count($data['subscriptions']),
            'The subscriptions subset was not the correct length'
        );
    }

    /**
     * Specter should be able to generate Avatar urls
     *
     * @test
     * @return void
     */
    public function specterCanMakeAvatarUrls()
    {
        $specter = new Specter();
        $json    = file_get_contents(TEST_FIXTURE_FOLDER.'/customer.json');
        $fixture = json_decode($json, true);
        $data    = $specter->substituteMockData($fixture);

        self::assertContains(
            'gravatar',
            $data['avatar'],
            'The avatar does not appear to be value'
        );
    }


    /**
     * Specter should be able to select a related value from a list
     *
     * @test
     * @return void
     */
    public function specterCanSelectRelatedValuesWithGeneratedValue()
    {
        $seed  = 1;
        $faker = $this->fakerFactory($seed);
        $faker->randomDigitNotNull; // Call to keep the random generator in sync
        $faker->randomDigitNotNull;
        $name    = $faker->name;
        $specter = new Specter($seed);
        $json    = file_get_contents(TEST_FIXTURE_FOLDER.'/related-element.json');
        $fixture = json_decode($json, true);
        $data    = $specter->substituteMockData($fixture);
        self::assertEquals(
            $data['type'],
            'user',
            'The seed should have made the type == customer'
        );
        self::assertEquals(
            $data['name'],
            $name,
            'Incorrect related name generated'
        );
    }

    /**
     * Specter should be able to select a related value from a list
     *
     * @test
     * @return void
     */
    public function specterCanSelectRelatedValuesWithStaticValue()
    {
        $seed    = 2;
        $specter = new Specter($seed);
        $json    = file_get_contents(TEST_FIXTURE_FOLDER.'/related-element.json');
        $fixture = json_decode($json, true);
        $data    = $specter->substituteMockData($fixture);
        self::assertEquals(
            $data['type'],
            'guest',
            'The seed should have made the type == guest'
        );
        self::assertEquals(
            $data['name'],
            'Guest User',
            'A guest user should have been created'
        );
    }
}

/* End of file SpecterTest.php */
