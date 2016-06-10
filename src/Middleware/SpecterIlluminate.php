<?php
namespace HelpScout\Specter\Middleware;

use Closure;
use HelpScout\Specter\Specter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use LogicException;

class SpecterIlluminate
{
    /**
     * JSON must have this property to trigger processing
     *
     * @var string
     */
    protected $specterTrigger = '__specter';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     * @throws LogicException
     */
    public function handle(Request $request, Closure $next)
    {
        /**
         * @var Response $response
         */
        $response = $next($request);

        $fixture = @json_decode($response->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new LogicException(
                'Failed to parse json string. Error: '.json_last_error_msg()
            );
        }

        // We will not process files without the Specter trigger, and instead
        // return an unchanged response.
        if (!array_key_exists($this->specterTrigger, $fixture)) {
            return $response;
        }

        // Process the fixture data, using a seed in case the designer wants
        // a repeatable result.
        $seed    = $request->header('SpecterSeed', 0);
        $specter = new Specter($seed);

        $json = $specter->substituteMockData($fixture);

        return $response->setContent(json_encode($json));
    }
}

/* End of file SpecterIlluminate.php */
