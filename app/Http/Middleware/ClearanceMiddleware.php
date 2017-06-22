<?php
/**
 * Created by PhpStorm.
 * User: hasyim
 * Date: 6/21/17
 * Time: 8:03 PM
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ClearanceMiddleware
{

    /**
     * Handle an incoming request
     *
     * @param $request
     * @param Closure $next
     */
    public function handle($request, Closure $next)
    {
        //If user has this permission
        if (Auth::user()->hasPermissionTo('Administer roles & permissions'))
        {
            return $next($request);
        }

        if ($request->is('posts/create')) //if user is creating post
        {
            if (!Auth::user()->hasPermissionTo('Create Post'))
            {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->is('posts/*/edit')) //if user editing post
        {
            if (!Auth::user()->hasPermissionTo('Edit Post'))
            {
                abort('401');
            } else {
                $next($request);
            }
        }

        if ($request->isMethod('Delete')) //if user deleting post
        {
            if (!Auth::user()->hasPermissionTo('Delete Post'))
            {
                abort('401');
            } else {
                return $next($request);
            }
        }

        return $next($request);
    }
}