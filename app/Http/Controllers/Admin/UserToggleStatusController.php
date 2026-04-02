<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class UserToggleStatusController extends Controller
{
    public function toggle($id)
    {
        $user = DB::table('tab1')->where('employee_id', $id)->first();
        if (!$user) {
            return redirect()->route('admin.users.index');
        }
        $newStatus = ($user->status === 'Active') ? 'Inactive' : 'Active';
        DB::table('tab1')->where('employee_id', $id)->update(['status' => $newStatus]);
        return redirect()->route('admin.users.index');
    }
}
