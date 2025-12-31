<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Dusty Jewelry Store API",
 *     version="1.0.0",
 *     description="API documentation for Dusty Jewelry Store management system",
 *     @OA\Contact(
 *         email="support@dusty.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller extends \Illuminate\Routing\Controller
{
	/**
	 * Abort with 404 unless device_token cookie or admin_secret_used session is set.
	 * Only allow admin device token for admin users.
	 */
	protected function enforceDeviceOrAdminOr404($request = null)
	{
		$request = $request ?: request();
		$deviceToken = $request->cookie('device_token');
		$adminSecret = $request->session()->get('admin_secret_used');
		if (!$deviceToken && !$adminSecret) {
			abort(404);
		}
		// If device token is for admin device, only allow admin users
		if ($deviceToken) {
			$adminDevice = \App\Models\Device::where('token', $deviceToken)->where('name', 'admin')->first();
			if ($adminDevice && (!auth()->check() || !auth()->user()->isAdmin())) {
				abort(404);
			}
		}
	}
}
