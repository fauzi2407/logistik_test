<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMenuPermission
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Super Admin role overrides all permission checks
        if ($user->role === 'admin') {
            return $next($request);
        }

        $routeName = $request->route()->getName();

        if ($routeName) {
            // Whitelist operational routes for Couriers / Drivers
            if ($user->role === 'courier') {
                $courierAllowedRoutes = [
                    'dashboard',
                    'shipments.complete-task',
                    'shipments.update-status',
                    'courier-assignments.complete',
                    'courier-assignments.manifest',
                    'courier-assignments.show',
                    'courier-payrolls.index',
                    'courier-payrolls.show',
                    'courier-payrolls.print-slip',
                    'courier-cash-advances.index',
                    'courier-cash-advances.store',
                    'courier-cash-advances.destroy',
                    'courier-cash-advances.print',
                ];

                if (in_array($routeName, $courierAllowedRoutes)) {
                    return $next($request);
                }
            }

            // Whitelist operational routes for Customers
            if ($user->role === 'customer') {
                $customerAllowedRoutes = [
                    'dashboard',
                    'delivery-orders.create',
                    'delivery-orders.store',
                    'delivery-orders.template-csv',
                    'delivery-orders.import-csv',
                    'delivery-orders.print',
                    'delivery-orders.show',
                    'invoices.show',
                    'invoices.print',
                    'invoices.payment',
                    'shipments.create',
                    'shipments.store',
                    'shipments.show',
                    'shipments.print-label',
                ];

                if (in_array($routeName, $customerAllowedRoutes)) {
                    return $next($request);
                }
            }

            $action = 'view';
            if (str_contains($routeName, '.create') || str_contains($routeName, '.store')) {
                $action = 'create';
            } elseif (str_contains($routeName, '.edit') || str_contains($routeName, '.update') || str_contains($routeName, '.complete')) {
                $action = 'edit';
            } elseif (str_contains($routeName, '.destroy')) {
                $action = 'delete';
            }

            // Derive primary index route (e.g., 'customers.index')
            $routeParts = explode('.', $routeName);
            $primaryIndexRoute = $routeParts[0] . '.index';

            $hasViewPerm = $user->hasPermission($primaryIndexRoute, $action) && $user->hasPermission($routeName, $action);

            if (!$hasViewPerm) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['message' => "Akses Ditolak: Anda tidak memiliki izin ({$action}) untuk menu ini."], 403);
                }
                abort(403, "Akses Ditolak: Role ({$user->role}) Anda tidak memiliki izin ({$action}) untuk mengakses menu ini.");
            }
        }

        return $next($request);
    }
}
