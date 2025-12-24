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
abstract class Controller
{
	/**
	 * Enforce device token for all non-admin users. Redirects to pair-device if not trusted.
	 */
	protected function enforceDeviceToken($request)
	{
		$user = auth()->user();
		if ($user && method_exists($user, 'isAdmin') && !$user->isAdmin()) {
			$deviceToken = $request->cookie('device_token');
			if (!$deviceToken || !\App\Models\Device::where('token', $deviceToken)->where('user_id', $user->id)->exists()) {
				redirect()->route('pair-device.form')->send();
				exit;
			}
		}
	}
}
