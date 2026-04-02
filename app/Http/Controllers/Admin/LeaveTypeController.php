<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveType;

class LeaveTypeController extends Controller
{
	public function index()
	{
		$leaveTypes = LeaveType::all();
		return view('admin.leave_types.index', compact('leaveTypes'));
	}

	public function create()
	{
		return view('admin.leave_types.create');
	}

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

	public function edit($id)
	{
		$leaveType = LeaveType::findOrFail($id);
		return view('admin.leave_types.edit', compact('leaveType'));
	}

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

	public function destroy($id)
	{
		$leaveType = LeaveType::findOrFail($id);
		$leaveType->delete();
		return redirect()->route('admin.leave_types.index')->with('success', 'Leave type deleted successfully.');
	}

	public function toggleStatus($id)
	{
		$leaveType = LeaveType::findOrFail($id);
		$leaveType->status = $leaveType->status === 'active' ? 'inactive' : 'active';
		$leaveType->save();
		return redirect()->route('admin.leave_types.index')->with('success', 'Leave type status updated successfully.');
	}
}
