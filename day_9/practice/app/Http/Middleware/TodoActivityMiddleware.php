<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TodoActivityMiddleware
{
    /**
     * Handle an incoming request through the architecture pipeline.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) Str::uuid();

        // 1. Establish contextual metadata for this request cycle
        Context::add('request_id', $requestId);
        Context::add('pipeline_stage', 'Middleware');
        Context::add('endpoint', $request->path());

        // 2. Normalize and sanitize text input fields
        if ($request->has('title') && is_string($request->input('title'))) {
            $request->merge(['title' => trim($request->input('title'))]);
        }
        if ($request->has('description') && is_string($request->input('description'))) {
            $request->merge(['description' => trim($request->input('description'))]);
        }

        // 3. Forward to next pipeline layer (Form Request / Controller)
        $response = $next($request);

        // 4. Attach architecture tracking headers to response
        $response->headers->set('X-Request-Id', $requestId);
        $response->headers->set(
            'X-Architecture-Pipeline',
            'HTTP -> Route -> Middleware -> FormRequest -> Controller -> DTO -> Service -> RepoInterface -> Container -> RepoImpl -> Eloquent -> Database'
        );

        return $response;
    }
}
