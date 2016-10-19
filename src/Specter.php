<?php
/**
 * Specter Api Mocking and Assertion Library
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter;

use Faker;
use HelpScout\Specter\Provider\Avatar;
use HelpScout\Specter\Provider\RelatedElement;
use InvalidArgumentException;

/**
 * Class Specter
 *
 * @package HelpScout\Specter
 */
class Specter
{
    /**
     * JSON fixture trigger to locate the faker producers.
     *
     * Values of `@firstName@` will be processed by default
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
     * @param integer $seed Faker seed value
     *
     * @return Specter
     */
    public function __construct($seed = 0)
    {
        $this->faker = Faker\Factory::create();
        $this->faker->addProvider(new Avatar($this->faker));
        $this->faker->addProvider(new RelatedElement($this->faker, $this->trigger));
        if ($seed) {
            $this->faker->seed($seed);
        }
    }

    /**
     * Replace fixture patterns with Faker data
     *
     * @param array $fixture
     *
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
            $jsonValue  = trim($jsonValue, $this->trigger);
            $parameters = explode('|', $jsonValue);
            $producer   = array_shift($parameters);

            // Transform any array-like parameters into arrays.
            foreach ($parameters as $index => $parameter) {
                if (strpos($parameter, ',')) {
                    $parameters[$index] = explode(',', $parameter);
                }
            }

            // Related to has some special requirements in that it needs access
            // the the rest of the fixture data and the special related syntax.
            $relatedTrigger = 'relatedElement:';
            if (stripos($producer, $relatedTrigger) === 0) {
                $relatedTo = str_replace($relatedTrigger, '', $producer);
                $producer  = 'relatedElement';
                $options   = [];
                foreach ($parameters as $parameter) {
                    list($key, $value) = explode(':', $parameter, 2);
                    $options[$key]     = $value;
                }
                $parameters = [$relatedTo, $fixture, $options];
            }

            try {
                // Note that type conversion will take place here - a matcher
                // of `"@randomDigitNotNull@"` will be turned into an `int`.
                switch (count($parameters)) {
                    case 0:
                        $fixture[$jsonKey] = $this->faker->$producer;
                        break;
                    case 1:
                        $fixture[$jsonKey] = $this->faker->$producer($parameters[0]);
                        break;
                    case 2:
                        $fixture[$jsonKey] = $this->faker->$producer($parameters[0], $parameters[1]);
                        break;
                    case 3:
                        $fixture[$jsonKey] = $this->faker->$producer($parameters[0], $parameters[1], $parameters[2]);
                        break;
                    default:
                        $fixture[$jsonKey] = call_user_func_array(array($this->faker, $producer), $parameters);
                        break;
                }
            } catch (InvalidArgumentException $e) {
                $fixture[$jsonKey] = 'Unsupported formatter: @'.$producer.'@';
            }
        }

        return $fixture;
    }
}

/* End of file Specter.php */
