<?php
/**
 * Slim 3 Api Route
 *
 * An example of using the SpecterMiddleware with a API route. Note that the
 * customers route simple returns the raw Specter formatted JSON and the
 * middleware transforms it into the fixture data.
 *
 * Note: Specter requires Slim 3.3 or newer, after a fix for seeking on temp
 *       php streams. See: https://github.com/slimphp/Slim/issues/1434
 */

/**
 * Replace this with something of your own, a way to load the Specter JSON
 * fixture file.
 *
 * @param string $name fixture file name
 *
 * @return mixed
 */
function getFixture(string $name)
{
    $path = BASE_DIR.'/fixture/'.$name.'.json';
    $data = file_get_contents($path);
    return json_decode($data);
}

/**
 * An example route group for API endpoints
 */
$app->group('/api/v1', function () use ($app) {

    /**
     * An example customer endpoint, returning a random customer.
     */
    $app->get('/customer/{id}', function ($request, $response, $args) {
        return $response->withJson(getFixture('customer'));
    });

})->add(new \HelpScout\Specter\Middleware\SpecterPsr7);

/* End of file Slim3Route.php */
