<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class EmployeeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/employees",
     *     tags={"Employees"},
     *     summary="Get all employees",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of employees")
     * )
     */
    public function index()
    {
        return response()->json(Employee::active()->with('branch')->get());
    }

    /**
     * @OA\Get(
     *     path="/api/employees/{id}",
     *     tags={"Employees"},
     *     summary="Get a specific employee",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Employee details")
     * )
     */
    public function show(Employee $employee)
    {
        return response()->json($employee->load('branch'));
    }

    /**
     * @OA\Get(
     *     path="/api/branches/{branch}/employees",
     *     tags={"Employees"},
     *     summary="Get employees by branch",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="branch", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of employees for the branch")
     * )
     */
    public function getByBranch($branchId)
    {
        $employees = Employee::where('branch_id', $branchId)
            ->active()
            ->get();
        return response()->json($employees);
    }

    /**
     * @OA\Post(
     *     path="/api/employees/{id}/toggle-status",
     *     tags={"Employees"},
     *     summary="Toggle employee status",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Status toggled successfully")
     * )
     */
    public function toggleStatus(Employee $employee)
    {
        $employee->update(['is_active' => !$employee->is_active]);
        return response()->json($employee);
    }
}
