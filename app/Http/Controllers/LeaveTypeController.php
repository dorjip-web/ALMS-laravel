<?php
namespace App\Http\Controllers;

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveType;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return view('admin.leave_types.index', compact('leaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.leave_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_name' => 'required|string|max:255',
            'leave_code' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_per_year' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
        ]);
        LeaveType::create($validated);
        return redirect()->route('admin.leave_types.index')->with('success', 'Leave type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        return view('admin.leave_types.edit', compact('leaveType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'leave_name' => 'required|string|max:255',
            'leave_code' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_per_year' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
        ]);
        $leaveType = LeaveType::findOrFail($id);
        $leaveType->update($validated);
        return redirect()->route('admin.leave_types.index')->with('success', 'Leave type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        $leaveType->delete();
        return redirect()->route('admin.leave_types.index')->with('success', 'Leave type deleted successfully.');
    }
}
