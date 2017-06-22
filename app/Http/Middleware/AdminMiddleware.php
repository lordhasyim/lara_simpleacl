<?php
/**
 * Created by PhpStorm.
 * User: hasyim
 * Date: 6/21/17
 * Time: 7:48 PM
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class AdminMiddleware
{
    /**
     * Handle an incoming request
     *
     * @param $request
     * @param Closure $next
     */
    public function handle($request, Closure $next)
    {
        $user = User::all()->count();
        if (!($user == 1)) {
            //If user does not have this permission
            if (!Auth::user()->hasPermissionTo('Administer roles & permissions'))
            {
                abort('401');
            }
        }

        return $next($request);
    }

}