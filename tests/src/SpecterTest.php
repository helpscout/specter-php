<?php
/**
 * Specter Test
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Tests;

use Faker;
use HelpScout\Specter\Specter;
use PHPUnit_Framework_TestCase;

/**
 * Class SpecterTest
 *
 * @package HelpScout\Specter\Tests
 */
class SpecterTest extends PHPUnit_Framework_TestCase
{
    use FakerFactory;

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
     * @return void
     */
    public function testSpecterCanProcessJson()
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
}

/* End of file SpecterTest.php */
