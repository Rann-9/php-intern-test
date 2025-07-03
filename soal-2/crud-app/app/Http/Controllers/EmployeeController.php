<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Employee::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor' => 'required|string|unique:employees',
            'nama' => 'required|string',
            'jabatan' => 'nullable|string',
            'talahir' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Upload ke S3
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 's3');
            $url = Storage::disk('s3')->url($path);
            $validated['photo_upload_path'] = $url;
        }

        $employee = Employee::create($validated);

        Redis::set("emp_{$employee->nomor}", $employee->toJson());

        return response()->json($employee, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return $employee;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'nama' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'talahir' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 's3');
            $url = Storage::disk('s3')->url($path);
            $validated['photo_upload_path'] = $url;
        }

        $employee->update($validated);

        Redis::set("emp_{$employee->nomor}", $employee->toJson());

        return $employee;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        Redis::del("emp_{$employee->nomor}");

        return response()->json(null, 204);
    }
}
