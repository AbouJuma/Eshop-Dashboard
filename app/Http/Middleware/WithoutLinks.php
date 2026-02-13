<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WithoutLinks
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Process JsonResponse
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);

            if (isset($data['links'])) {
                unset($data['links']);
            }
            if (isset($data['meta'], $data['meta']['links'])) {
                unset($data['meta']['links']);
            }

            $response->setData($data);
        }
        // Process ResourceCollection
        elseif ($response instanceof \Illuminate\Http\Resources\Json\ResourceCollection) {
            // ResourceCollections are converted to JsonResponse when sent
            // We need to wrap the response processing
            $originalResponse = $response;
            
            // Create a new response that will be processed by Laravel
            $response = response()->json($originalResponse->toArray($request));
            
            $data = $response->getData(true);

            if (isset($data['links'])) {
                unset($data['links']);
            }
            if (isset($data['meta'], $data['meta']['links'])) {
                unset($data['meta']['links']);
            }

            $response->setData($data);
        }

        return $response;
    }

}
