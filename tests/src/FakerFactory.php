<?php
/**
 * Faker Factory
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
namespace HelpScout\Specter\Tests;

use Faker;

/**
 * Trait FakerFactory
 * @package HelpScout\Specter\Tests\Helpers
 */
trait FakerFactory
{
    /**
     * Create a faker instance with an optional seed.
     *
     * @param int $seed random generator seed for repeatable results
     * @return Faker\Generator
     */
    public function fakerFactory($seed = 0)
    {
        $faker = Faker\Factory::create();
        if ($seed) {
            $faker->seed($seed);
        }
        return $faker;
    }
}

/* End of file FakerFactory.php */
