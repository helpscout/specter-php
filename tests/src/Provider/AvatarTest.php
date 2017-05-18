<?php
namespace HelpScout\Specter\Tests\Provider;

use HelpScout\Specter\Provider\Avatar;
use HelpScout\Specter\Tests\Helpers\FakerFactory;
use PHPUnit\Framework\TestCase;

/**
 * Exercise the Avatar Provider
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2017 Help Scout
 * @group     providers
 */
class AvatarTest extends TestCase
{
    use FakerFactory;

    /**
     * @test
     * @return void
     */
    public function providerCanInstantiate()
    {
        $faker = \Faker\Factory::create();

        $this->assertInstanceOf(
            Avatar::class,
            new Avatar($faker)
        );
    }

    /**
     * @test
     * @return void
     */
    public function providerCanProviderRobotAvatar()
    {
        $this->assertContains(
            'robohash',
            $this->fakerFactory()->randomRobotAvatar,
            'The robot avatar provider is not working'
        );
    }

    /**
     * @test
     * @return void
     */
    public function providerCanProviderRandomGravatar()
    {
        $this->assertContains(
            'gravatar',
            $this->fakerFactory()->randomGravatar,
            'The gravatar provider is not working'
        );
    }
}

/* End of file AvatarTest.php */
