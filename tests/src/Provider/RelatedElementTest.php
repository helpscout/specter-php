<?php
namespace src\Provider;

use HelpScout\Specter\Provider\RelatedElement;
use HelpScout\Specter\Tests\Helpers\FakerFactory;
use PHPUnit\Framework\TestCase;

/**
 * Exercise the Related Element provider
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2017 Help Scout
 * @group     providers
 */
class RelatedElementTest extends TestCase
{
    use FakerFactory;

    /**
     * @test
     */
    public function providerCanInstantiate()
    {
        $faker = \Faker\Factory::create();

        $this->assertInstanceOf(
            RelatedElement::class,
            new RelatedElement($faker)
        );
    }

    /**
     * @test
     */
    public function providerCanSelectRelatedWithStaticValue()
    {
        $seed  = 8;
        $faker = $this->fakerFactory($seed);

        $fixture = [
            'type' => 'guest',
        ];

        $fixture['name'] = $faker->relatedElement(
            'type',
            $fixture,
            [
                'guest' => 'Guest Account',
                'user'  => '@name@'
            ]
        );

        $this->assertEquals('Guest Account', $fixture['name']);
    }

    /**
     * @test
     */
    public function providerCanSelectRelatedWithFakerValue()
    {
        $seed     = 8;
        $faker    = $this->fakerFactory($seed);
        $expected = $faker->name;

        // reset the seed so we get the same via @name@
        $faker =  $this->fakerFactory($seed);

        $fixture = [
            'type' => 'user',
        ];

        $fixture['name'] = $faker->relatedElement(
            'type',
            $fixture,
            [
                'guest' => 'Guest Account',
                'user'  => '@name@'
            ]
        );

        $this->assertEquals($expected, $fixture['name']);
    }
}

/* End of file RelatedElementTest.php */
