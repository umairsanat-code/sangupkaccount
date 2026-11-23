<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\loggerExceptionTrait;
use App\Http\Requests\Admin\Roles\StoreRequest;

class RoleController extends Controller
{
    use loggerExceptionTrait;

    public function __construct()
    {
        $this->middleware(['role:admin']);
    }
    /**
     * listing  : list all roles with permissions using yajra/laravel-datatables-oracle: "~9.0"
     *
     * @param  mixed $request
     * @return void
     */
    public function listing(Request $request)
    {
        if ($request->ajax()) {
            $this->timeZone = $request->timeZone;
            return \Yajra\DataTables\DataTables::of(Role::with(['permissions'])->whereNotIn('name', ['admin', 'seller', 'buyer'])->get())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = ' <button data-id="' . $row->id  . '" data-name="' . ucfirst($row->name)  . '" onclick="return  confirm(\'Are you sure to delete role  ' . strtolower($row->name) . '?\')" class="btn btn-danger btn-sm deletePermission" title="Delete Role & Permissions!"><i class="fa fa-trash"></i></button>';
                    $btn .= ' <button data-id="' . $row->id  . '" data-name="' . ucfirst($row->name)  . '" class="text-white btn btn-info btn-sm btn-flat permissionEdit" title="Edit Permission!"><i class="fa fa-edit"></i></button>';
                    return $btn;
                })
                ->addColumn('permissionsaction', function ($row) {
                    $btn = ' <button data-id="' . $row->id . '" data-name="' . ucfirst($row->name) . '" class="text-white btn btn-info btn-sm btn-flat show" title="View Permissions!"><i class="fa fa-folder"></i> View </button>';
                    return $btn;
                })
                ->addColumn('permissionscount', function ($row) {
                    $count = $row->permissions ? $row->permissions->count() : 'N/A';
                    return $count;
                })
                ->addColumn('name', function ($row) {

                    return ucwords($row->name);
                })

                ->rawColumns(['name', 'action', 'permissionscount', 'permissionsaction'])
                ->make(true);
        }
        $permssions = Permission::all();
        return view('admin.roles.listing', compact('permssions'));
    }

    /**
     * store : store role and assign permissions
     *
     * @param  mixed $request
     * @return void
     */
    public function store(StoreRequest $request)
    {
        if (is_null(Role::where('name', $request->role)->first())) {
            $role = Role::create(['name' => ucwords($request->role)]);
            $role->syncPermissions($request->permissions);
            $role->forgetCachedPermissions();
            $role->load('permissions');
            return response()->json(['status' => true, 'data' => ucwords($request->role) . ' Role with permissions created succcessfully!'], JsonResponse::HTTP_OK);
        }
        return response()->json(['status' => true, 'data' => ucwords($request->role) . ' Role already exist!'], JsonResponse::HTTP_OK);
    }

    /**
     * permissions : list all the permissions
     *
     * @param  mixed $id
     * @return void
     */
    public function permissions($id)
    {
        $role = Role::with('permissions')->find($id);
        return response()->json(['status' => true, 'data' => $role], JsonResponse::HTTP_OK);
    }

    /**
     * edit : show a role with permissions with ajax request
     *
     * @param  mixed $id
     * @return void
     */
    public function edit($id)
    {
        try {
            $role = Role::with(['permissions'])->find($id);
            $persmissionNameArray = $role->permissions->pluck('name')->toArray();
            $permissions = Permission::all();
            return response()->json(['status' => true, 'selected' => $persmissionNameArray, 'data' => $role, 'permissions' => $permissions], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            $this->saveExceptionLog($e, 'RoleController exception');
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);
        }
    }

    /**
     * update : update the given role and refresh permissions
     *
     * @param  mixed $updateRequest
     * @return void
     */
    public function update(Request $updateRequest)
    {
        try {
            DB::beginTransaction();
            $role = Role::find($updateRequest->roleId);
            $roleAlreadyAdded = Role::where('name', ucwords($updateRequest->role))->first();
            /* if user not change the name of role then run this condition */
            if ($role->name == ucwords($updateRequest->role)) {
                $role->syncPermissions($updateRequest->permissions);
                $role->forgetCachedPermissions();
                $role->load('permissions');
                $role->update(['name' => $updateRequest->has('role') ? ucwords($updateRequest->role) : $role->name,]);
                DB::commit();
                toastr()->success('Role updated successfully!');
                return back();
                /* if user  change the name of role then run this condition */
            } elseif (is_null($roleAlreadyAdded)) {
                $role->syncPermissions($updateRequest->permissions);
                $role->forgetCachedPermissions();
                $role->load('permissions');
                $role->update(['name' => $updateRequest->has('role') ? ucwords($updateRequest->role) : $role->name,]);
                DB::commit();
                toastr()->success('Role updated successfully!');
                return back();
                /* if newly entered role already exist */
            } else {
                toastr()->error(ucfirst($updateRequest->role) . ' role already exist!');
            }
            return back();
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'RoleController exception');
            toastr()->error($e->getMessage());
            return back();
        }
    }

    /**
     * delete : delete given role and revoke all the permissions
     *
     * @param  mixed $id
     * @return void
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $role = Role::find($id);
            /* role do not have associated users delete role */
            if ($role->users->isEmpty()) {
                $role->delete();
                DB::commit();
                return response()->json(['status' => true, 'data' => 'Role & permissions deleted successfully!'], JsonResponse::HTTP_OK);
            } else {
                return response()->json(['status' => false, 'data' => 'Can not delete a role which already assigned to users!'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'RoleController exception');
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);
        }
    }
    /**
     * deletePermission : detach a permission against a specific role
     *
     * @param  mixed $permissionId
     * @param  mixed $roleId
     * @return void
     */
    public function deletePermission($permissionId, $roleId)
    {

        try {
            DB::beginTransaction();
            $role = Role::find($roleId);
            $role->permissions()->detach($permissionId);
            $role->forgetCachedPermissions();
            $role->load('permissions');
            DB::commit();
            return response()->json(['status' => true, 'data' => 'Permission deleted successfully!'], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'RoleController exception');
            return response()->json(['status' => false, 'data' => $e->getMessage()], JsonResponse::HTTP_OK);
        }
    }
}
