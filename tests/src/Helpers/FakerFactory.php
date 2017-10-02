<?php
/**
 * Faker Factory
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Tests\Helpers;

use Faker;
use HelpScout\Specter\Provider\Avatar;
use HelpScout\Specter\Provider\RelatedElement;

/**
 * Trait FakerFactory
 *
 * @package HelpScout\Specter\Tests\Helpers
 */
trait FakerFactory
{
    /**
     * Create a faker instance with an optional seed.
     *
     * @param int|null $seed random generator seed for repeatable results
     *
     * @return Faker\Generator
     */
    public function fakerFactory($seed = 0)
    {
        $faker = Faker\Factory::create();
        $faker->addProvider(new Avatar($faker));
        $faker->addProvider(new RelatedElement($faker, '@'));

        if ($seed) {
            $faker->seed($seed);
        }

        return $faker;
    }
}

/* End of file FakerFactory.php */
