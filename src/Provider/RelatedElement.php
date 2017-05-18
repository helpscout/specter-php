<?php
/**
 * Select a related value based on a previously chosen randomElement
 *
 * Let's say we want to have a 'type' of randomly chosen 'user' or 'guest'. In
 * the 'name' field, we want to show a random name if the type is 'user' and
 * the string 'Guest Account' if the 'type' is 'guest'. With strictly random
 * values you cannot accomplish this. However with this 'relatedElement` you
 * can.
 *
 * This scenario would look roughly like this:
 *
 *   {
 *    "type": "@randomElement|user,guest@",
 *    "name": "@relatedElement:type|user:@name@|guest:Guest Account@"
 *   }
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Provider;

use Faker\Generator;
use Faker\Provider\Base;
use InvalidArgumentException;

/**
 * Class RelatedElement
 *
 * @package HelpScout\Specter\Provider
 */
class RelatedElement extends Base
{
    /**
     * JSON fixture trigger to locate the faker producers.
     *
     * This should be set by the parent Specter class
     *
     * @var string
     */
    protected $trigger;

    /**
     * @param Generator $generator
     * @param string    $trigger
     */
    public function __construct(Generator $generator, $trigger = '@')
    {
        parent::__construct($generator);
        $this->trigger = $trigger;
    }

    /**
     * Select the correct option based on a related element in the fixture
     *
     * This is a little awkward for Faker, as it has some extra options
     *
     * @param string $relatedTo Fixture key that this element is related to
     * @param array  $fixture   Current fixture data
     * @param array  $options   Available options
     *
     * @return string
     */
    public function relatedElement($relatedTo, array $fixture, array $options)
    {
        if (!array_key_exists($relatedTo, $fixture)) {
            return 'Invalid related to key: '.$relatedTo;
        }

        if (!array_key_exists($fixture[$relatedTo], $options)) {
            return sprintf(
                'The related to value (%s) was not found in the options list',
                $fixture[$relatedTo]
            );
        }

        $relatedToValue = $options[$fixture[$relatedTo]];

        if (strpos($relatedToValue, $this->trigger) === 0) {
            $producer = trim($relatedToValue, $this->trigger);

            // Parameterized producers are not support here due to the
            // complexity of the syntax that it would cause.
            try {
                return $this->generator->$producer;
            } catch (InvalidArgumentException $e) {
                return 'Unsupported formatter: @'.$producer.'@';
            }
        }

        return $relatedToValue;
    }
}

/* End of file RelatedElement.php */
