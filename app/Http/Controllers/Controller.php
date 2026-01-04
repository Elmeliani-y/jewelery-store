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
	 * Validate device token or show blank page instead of 404
	 */
	protected function validateDeviceOrBlank()
	{
		$token = request()->cookie('device_token');
		if ($token) {
			$device = \App\Models\Device::where('token', $token)->first();
			if (!$device || !$device->active || (!$device->user_id && $token !== 'admin-static')) {
				\Auth::logout();
				request()->session()->invalidate();
				request()->session()->regenerateToken();
				\Cookie::queue(\Cookie::forget('device_token'));
				abort(response()->view('landing'));
			}
		}
	}

	/**
	 * Abort with blank page unless device_token cookie or admin_secret_used session is set.
	 * Only allow admin device token for admin users.
	 */
	protected function enforceDeviceOrAdminOr404($request = null)
	{
		$request = $request ?: request();
		$deviceToken = $request->cookie('device_token');
		$adminSecret = $request->session()->get('admin_secret_used');
		\Log::info('enforceDeviceOrAdminOr404 check', [
			'has_device_token' => !empty($deviceToken),
			'has_admin_secret' => !empty($adminSecret),
			'is_authenticated' => auth()->check(),
			'user_id' => auth()->id(),
		]);
		if (!$deviceToken && !$adminSecret) {
			\Log::info('aborting - no device token or admin secret');
			abort(response()->view('landing'));
		}
		// If device token is for admin device, only allow admin users
		if ($deviceToken) {
			$adminDevice = \App\Models\Device::where('token', $deviceToken)->where('name', 'admin')->first();
			if ($adminDevice && (!auth()->check() || !auth()->user()->isAdmin())) {
				abort(response()->view('landing'));
			}
		}
	}
}
