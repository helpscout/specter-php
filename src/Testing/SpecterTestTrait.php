<?php
/**
 * Specter Test Trait
 *
 * Used to assert a response matches an JSON spec. This is a PHPUnit trait that
 * provides a new `assertResponse` method for use in integration testing.
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Testing;

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Diff;
use Diff_Renderer_Text_Unified;
use Faker;
use Faker\Generator;
use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Trait SpecterTestTrait
 *
 * -----------------------------------------------------------------------------
 * Place a reference to PHPUnit assertions to quiet several IDE warnings.
 * -----------------------------------------------------------------------------
 * @codingStandardsIgnoreStart
 * @method static void assertEquals() assertEquals($expected, $actual, $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false) assert equals
 * @method static void assertTrue()   assertTrue($condition, $message = '') assert true
 * @method static void fail()         fail($other, $description = '') fail a test
 * @codingStandardsIgnoreEnd
 *
 * @package HelpScout\Specter
 */
trait SpecterTestTrait
{
    /**
     * @var Generator
     */
    static protected $faker;

    /**
     * A list of Faker methods and their Matcher test types
     *
     * @var array
     */
    static private $fakerMatchers = [
        'freeEmail'    => '@string@.isEmail()',
        'email'        => '@string@.isEmail()',
        'companyEmail' => '@string@.isEmail()',
        'url'          => '@string@.isUrl()',
        // inArray($value)
        // oneOf(...$expanders) - example usage "@string@.oneOf(contains('foo'), contains('bar'), contains('baz'))"
    ];

    /**
     * JSON fixture trigger
     *
     * Values of `@firstName@` will be processed by default
     *
     * @var string
     */
    static protected $trigger = '@';

    /**
     * Full path to the json fixture data files
     *
     * @var string
     */
    static protected $fixtureFolder = '';

    /**
     * Assert that a response matches a spec file and status code
     *
     * For more about the PSR7 interfaces, please see:
     *  https://git.io/psr7_ResponseInterface
     *
     * @param ResponseInterface $response   psr7 response object
     * @param string            $filename   path to fixture file (no extension)
     * @param string            $mimeType   fixture file extension and mime type
     * @param integer           $statusCode expected status code of response
     *
     * @return void
     * @throws RuntimeException
     * @throws LogicException
     */
    static public function assertResponse(
        ResponseInterface $response,
        $filename,
        $mimeType = 'json',
        $statusCode = 200
    ) {
        self::assertResponseCode($response, $statusCode);
        self::assertResponseContent($response, $filename, $mimeType);
    }

    /**
     * Assert an api response http code
     *
     * @param ResponseInterface $response   psr7 response object
     * @param integer           $statusCode expected status code
     *
     * @return void
     */
    static public function assertResponseCode(
        ResponseInterface $response,
        $statusCode
    ) {
        self::assertEquals(
            $statusCode,
            $response->getStatusCode(),
            'Incorrect status code'
        );
    }

    /**
     * Assert that a response matches fixture data (with wildcard patterns)
     *
     * This uses the matcher format for json files. See the project at
     * https://github.com/coduo/php-matcher for more information.
     *
     * Construct a JSON fixture file with the php-matcher patterns and you can
     * assert that a server response matches the spec, even if the data is
     * changing.
     *
     * @param ResponseInterface $response psr7 response object
     * @param string|resource   $filename path within `$fixtureFolder`
     * @param string            $mimeType file extension and mime type
     *
     * @throws LogicException
     * @throws RuntimeException
     * @return void
     */
    static public function assertResponseContent(
        ResponseInterface $response,
        $filename,
        $mimeType = 'json'
    ) {
        self::$faker = Faker\Factory::create();
        $spec        = self::getFixtureText($filename, $mimeType);
        $actual      = $response->getBody()->getContents();

        // Check that the spec and actual are both valid json
        $test = json_decode($spec);
        if ($test === null && json_last_error() !== JSON_ERROR_NONE) {
            self::fail('Invalid Specter File, unable to decode '.$filename.'.');
        }
        $test = json_decode($actual);
        if ($test === null && json_last_error() !== JSON_ERROR_NONE) {
            self::fail('Invalid Specter File, unable to decode response.');
        }
        unset($test);


        // Build these both into arrays for processing
        $actual = json_decode($actual, true);
        $spec   = json_decode($spec, true);

        // Convert the spec to matcher format
        $matcherSpec = self::getMatcherFormat($spec);
        $factory     = new SimpleFactory();
        $matcher     = $factory->createMatcher();

        // Pass this test because the matcher matched
        if ($matcher->match($actual, $matcherSpec)) {
            self::assertTrue(true);
            return;
        }

        // Display the output in a better format by using a diffing tool
        // We convert to strings to try to make the output more accurate
        $difference   = $matcher->getError().PHP_EOL;
        $specString   = json_encode($spec, JSON_PRETTY_PRINT);
        $specString   = explode(PHP_EOL, $specString);
        $actualString = json_encode($actual, JSON_PRETTY_PRINT);
        $actualString = explode(PHP_EOL, $actualString);
        $diffOptions  = [];
        $diff         = new Diff($specString, $actualString, $diffOptions);
        $renderer     = new Diff_Renderer_Text_Unified();

        self::fail(
            $difference.$diff->render($renderer),
            'Incorrect api response'
        );
    }

    /**
     * Convert a Specter specification into a matcher formatted structure
     *
     * @param array $spec
     *
     * @return array
     * @throws \LogicException
     */
    static private function getMatcherFormat(array $spec)
    {
        foreach ($spec as $jsonKey => $jsonValue) {
            // Recurse into a json structure.
            if (is_array($jsonValue)) {
                $spec[$jsonKey] = self::getMatcherFormat($jsonValue);
                continue;
            }

            // Confirm that this property is meant to be mocked by starting
            // with our trigger
            if (strpos($jsonValue, self::$trigger) !== 0) {
                continue;
            }

            // Use the Faker producer for this data type if available.
            $producer       = trim($jsonValue, self::$trigger);
            $spec[$jsonKey] = self::getMatcherType($producer);
        }

        return $spec;
    }

    /**
     * Map a faker provider to the correct matcher string
     *
     * @param string $producer faker producer name
     *
     * @return string string matcher type
     * @throws LogicException
     */
    static private function getMatcherType($producer)
    {
        // Explode a `randomElements|args1|arg2|arg3` into its constituent pieces
        $pieces       = explode('|', $producer);
        $producerName = $pieces[0];
        $arguments    = array_splice($pieces, 1);

        // The arguments might be a csv list for an array
        $arguments = array_map(
            function ($argument) {
                if (str_contains($argument, ',')) {
                    return explode(',', $argument);
                }
                return $argument;
            },
            $arguments
        );

        // These are explicitly set by configuration to match a php-matcher to
        // the output of a particular Faker provider
        if (array_key_exists($producer, self::$fakerMatchers)) {
            return self::$fakerMatchers[$producer];
        }

        // These are type checked by getting the type from Faker output
        try {
            $result = call_user_func_array([self::$faker, $producerName], $arguments);
            $type   = gettype($result);
            return '@'.$type.'@';
        } catch (InvalidArgumentException $e) {
            throw new LogicException('Unsupported formatter: @'.$producer.'@');
        }
    }

    /**
     * Get the content of a fixture file or resource
     *
     * @param string|resource $filename resource or filename in $fixtureFolder
     * @param string          $mimeType file extension
     *
     * @return string fixture data as json string
     * @throws LogicException
     */
    static private function getFixtureText($filename, $mimeType)
    {
        if (is_resource($filename)) {
            return stream_get_contents($filename);
        }

        $fixtureFolder = self::getFixtureFolder();
        if (self::$fixtureFolder === '') {
            throw new LogicException(
                'Please set a fixture folder for Specter JSON files'
            );
        }
        $fixtureFile = sprintf(
            '%s/%s.%s',
            $fixtureFolder,
            $filename,
            $mimeType
        );
        if (!file_exists($fixtureFile)) {
            throw new LogicException('Spec is not readable: ' . $fixtureFile);
        }
        return file_get_contents($fixtureFile);
    }

    /**
     * Get the API fixture data path
     *
     * @return string path to api fixture data where json
     *
     * @codeCoverageIgnore
     */
    static public function getFixtureFolder()
    {
        return self::$fixtureFolder;
    }

    /**
     * Set the API fixture data path
     *
     * @param string $path to api fixture data where json
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    static public function setFixtureFolder($path)
    {
        self::$fixtureFolder = rtrim($path, '/');
    }
}

/* End of file SpecterTestTrait.php */
