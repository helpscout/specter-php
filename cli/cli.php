<?php
/**
 * Specter command line tool
 *
 * @author    Platform Team <developer@helpscout.net>
 * @copyright 2016 Help Scout
 */
require __DIR__.'/../vendor/autoload.php';

use HelpScout\Specter\Specter;

$fixtureFolder = __DIR__.'/../tests/fixture';

$specter = new Specter();
$json    = file_get_contents($fixtureFolder.'/customer.json');
$fixture = @json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    throw new LogicException(
        'Failed to parse json string. Error: '.json_last_error_msg()
    );
}

var_dump($specter->substituteMockData($fixture));

/* End of file cli.php */
