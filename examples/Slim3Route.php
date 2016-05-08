<?php
/**
 * Slim 3 Api Route
 *
 * An example of using the SpecterMiddleware with a API route. Note that the
 * customers route simple returns the raw Specter formatted JSON and the
 * middleware transforms it into the fixture data.
 */

/**
 * Replace this with something of your own, a way to load the Specter JSON
 * fixture file.
 *
 * @param string $name fixture file name
 *
 * @return mixed
 */
function getFixture($name)
{
    $path = BASE_DIR.'/fixture/'.$name.'.json';
    $data = file_get_contents($path);
    return json_decode($data);
}


$app->group('/api/v1', function () use ($app) {

    $app->get('/customer', function ($request, $response) {
        return $response->withJson(getFixture('customer'));
    });

})->add(new \HelpScout\Specter\SpecterMiddleware);
