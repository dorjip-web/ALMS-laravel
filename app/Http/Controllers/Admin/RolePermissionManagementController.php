<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class RolePermissionManagementController extends Controller
{
    public function index(Request $request)
    {
        $roles = DB::table('role')->orderBy('role_name')->get();
        $permissions = DB::table('permission')->orderBy('permission_name')->get();
        $editRole = null;
        $editPermission = null;
        $assignedPermissions = [];
        $message = session('message', '');

        if ($request->has('edit_role')) {
            $editRole = DB::table('role')->where('role_id', $request->input('edit_role'))->first();
        }
        if ($request->has('edit_perm')) {
            $editPermission = DB::table('permission')->where('permission_id', $request->input('edit_perm'))->first();
        }
        if ($request->has('assign_role_id') || $request->has('assign_role')) {
            $roleParam = $request->input('assign_role_id') ?: $request->input('assign_role');
            $assignedPermissions = DB::table('role_permission')
                ->where('role_id', $roleParam)
                ->pluck('permission_id')->toArray();
        }

        return view('admin.roles_permissions.index', compact(
            'roles', 'permissions', 'editRole', 'editPermission', 'assignedPermissions', 'message'
        ));
    }

    public function toggleRole($id)
    {
        $role = DB::table('role')->where('role_id', $id)->first();
        if ($role) {
            $new = (strtolower(trim($role->status ?? '')) === 'active') ? 'inactive' : 'active';
            DB::table('role')->where('role_id', $id)->update(['status' => $new]);
            return Redirect::route('admin.roles_permissions');
        }
        return Redirect::route('admin.roles_permissions');
    }

    public function togglePermission($id)
    {
        $perm = DB::table('permission')->where('permission_id', $id)->first();
        if ($perm) {
            $new = (strtolower(trim($perm->status ?? '')) === 'active') ? 'inactive' : 'active';
            DB::table('permission')->where('permission_id', $id)->update(['status' => $new]);
            return Redirect::route('admin.roles_permissions');
        }
        return Redirect::route('admin.roles_permissions');
    }

    public function saveRole(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);
        $roleId = $request->input('role_id');
        if ($roleId) {
            DB::table('role')->where('role_id', $roleId)->update([
                'role_name' => $validated['role_name'],
                'status' => $validated['status'],
            ]);
            return Redirect::route('admin.roles_permissions')->with('message', 'Role updated.');
        } else {
            DB::table('role')->insert([
                'role_name' => $validated['role_name'],
                'status' => $validated['status'],
            ]);
            return Redirect::route('admin.roles_permissions')->with('message', 'Role added.');
        }
    }

    public function savePermission(Request $request)
    {
        $validated = $request->validate([
            'permission_name' => 'required|string|max:255',
            'pstatus' => 'required|in:active,inactive',
        ]);
        $permId = $request->input('perm_id');
        if ($permId) {
            DB::table('permission')->where('permission_id', $permId)->update([
                'permission_name' => $validated['permission_name'],
                'status' => $validated['pstatus'],
            ]);
            return Redirect::route('admin.roles_permissions')->with('message', 'Permission updated.');
        } else {
            DB::table('permission')->insert([
                'permission_name' => $validated['permission_name'],
                'status' => $validated['pstatus'],
            ]);
            return Redirect::route('admin.roles_permissions')->with('message', 'Permission added.');
        }
    }

    public function saveAssign(Request $request)
    {
        $roleId = $request->input('assign_role_id');
        $perms = $request->input('assign_permissions', []);
        if ($roleId) {
            DB::beginTransaction();
            try {
                DB::table('role_permission')->where('role_id', $roleId)->delete();
                foreach ($perms as $pid) {
                    DB::table('role_permission')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $pid,
                    ]);
                }
                DB::commit();
                return Redirect::route('admin.roles_permissions')->with('message', 'Assigned permissions updated.');
            } catch (\Exception $e) {
                DB::rollBack();
                return Redirect::route('admin.roles_permissions')->with('message', 'Error saving assignments.');
            }
        } else {
            return Redirect::route('admin.roles_permissions')->with('message', 'Select a role to assign permissions.');
        }
    }
}
