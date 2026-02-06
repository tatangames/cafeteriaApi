<?php

namespace App\Http\Controllers\Api\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function listadoRoles()
    {
        $roles = Role::all()->pluck('name', 'id');
        return ['success' => true, 'roles' => $roles];
    }


    public function borrarRolGlobal(Request $request) {
        // Validar que el ID llegue
        if (!$request->has('idrol')) {
            return ['success' => 99, 'message' => 'ID no proporcionado'];
        }

        DB::beginTransaction();
        try {
            // 1. Intentar encontrar el rol (Spatie lanza excepción si no existe)
            $role = Role::findById($request->idrol, 'api');

            // 2. Eliminar permisos asociados explícitamente
            $role->syncPermissions([]);

            // 3. Borrar el rol
            $role->delete();

            DB::commit();
            return ['success' => true];

        } catch (RoleDoesNotExist $e) {
            DB::rollback();
            Log::error('El rol no existe', ['idrol' => $request->idrol]);
            return ['success' => 99, 'error' => 'Rol no encontrado'];
        } catch (\Throwable $e) {
            DB::rollback();
            Log::error('Error al borrar rol', [
                'idrol' => $request->idrol,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Esto te dirá exactamente QUÉ línea falla
            ]);
            return ['success' => 99, 'error' => $e->getMessage()];
        }
    }




    public function tablaRolesPermisos($id){

        $role = Role::findById($id, 'api');

        $permisos = $role->permissions()
            ->select('id', 'name')
            ->get();

        return response()->json([
            'success' => true,
            'permisos' => $permisos
        ]);
    }


    public function borrarPermiso(Request $request)
    {
        $request->validate([
            'idrol' => 'required|integer',
            'idpermiso' => 'required|integer',
        ]);

        DB::beginTransaction();

        try {
            // Rol (MISMO guard)
            $role = Role::findById($request->idrol, 'api');

            // Permiso (MISMO guard)
            $permission = Permission::findById($request->idpermiso, 'api');

            // Revocar permiso
            $role->revokePermissionTo($permission);

            DB::commit();

            return response()->json(['success' => 1]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error al borrar permiso del rol', [
                'idrol' => $request->idrol,
                'idpermiso' => $request->idpermiso,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => 0,
                'message' => 'No se pudo eliminar el permiso',
            ], 500);
        }
    }



}
