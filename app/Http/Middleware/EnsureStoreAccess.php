<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Session; 
use Symfony\Component\HttpFoundation\Response;

class EnsureStoreAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userData = json_decode($request->user, true);
        $user_id = $userData['id'];

          // Retrieve the selected store ID from the session
          $selectedStoreId = $request->header('selectedStoreId');
          // Find the store based on the selected ID
          $store = Store::where('user_id', $user_id)->where('id', $selectedStoreId)->first();
  

        if (!$store) {
            // Return a JSON response indicating the user doesn't have a store
            return response()->json(['message' => 'You must create a store first.'], 403);
        } else {

            $createdstore = json_encode($store);
            // Attach the store to the request for access in other parts of the application
            $request['store'] = $createdstore;

            return $next($request);
        }
    }
}
