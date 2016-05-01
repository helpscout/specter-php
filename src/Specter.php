<?php
/**
 * Specter Api Mocking and Assertion Library
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter;

use Faker;
use Coduo\PHPMatcher\Matcher;
use InvalidArgumentException;

/**
 * Class Specter
 * @package HelpScout\Specter
 */
class Specter
{
    /**
     * JSON fixture trigger to locate the faker producers.
     *
     * values of `@firstName@` will be processed by default
     *
     * @var string
     */
    protected $trigger = '@';

    /**
     * Used to generate the actual random data for the spec.
     *
     * @var Faker\Generator
     */
    protected $faker;

    /**
     * Specter constructor.
     *
     * Initialize with a seed for repeatable fixture data
     *
     * @param int $seed Faker seed value
     */
    public function __construct($seed = 0)
    {
        $this->faker = Faker\Factory::create();
        if ($seed) {
            $this->faker->seed($seed);
        }
    }

    /**
     * Replace fixture patterns with Faker data
     *
     * @param array $fixture
     * @return array json with random data inserted
     */
    public function substituteMockData(array $fixture)
    {
        foreach ($fixture as $jsonKey => $jsonValue) {
            // Recurse into a json structure.
            if (is_array($jsonValue)) {
                $fixture[$jsonKey] = $this->substituteMockData($jsonValue);
                continue;
            }

            // Confirm that this property is meant to be mocked by starting
            // with our trigger
            if (strpos($jsonValue, $this->trigger) !== 0) {
                continue;
            }

            // Use the Faker producer for this data type if available.
            $producer = trim($jsonValue, $this->trigger);
            try {
                // Note that type conversion will take place here - a matcher
                // of `"@randomDigitNotNull@"` will be turned into an `int`.
                $fixture[$jsonKey] = $this->faker->$producer;
            } catch (InvalidArgumentException $e) {
                $fixture[$jsonKey] = 'Unsupported formatter: @'.$producer.'@';
            }
        }

        return $fixture;
    }
}

/* End of file Specter.php */
