<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DepartmentToggleStatusController extends Controller
{
    public function toggle($id)
    {
        $row = DB::table('department')->where('department_id', $id)->first();
        if (!$row) {
            return redirect()->route('admin.departments_hods.index');
        }
        $current = strtolower(trim($row->status ?? ''));
        $new = ($current === 'active') ? 'inactive' : 'active';
        DB::table('department')->where('department_id', $id)->update(['status' => $new]);
        return redirect()->route('admin.departments_hods.index');
    }
}
