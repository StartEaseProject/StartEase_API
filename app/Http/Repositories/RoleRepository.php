<?php

namespace App\Http\Repositories;

use Exception;
use App\Models\Role;
use App\Http\Interfaces\RoleInterface;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use Illuminate\Support\Facades\DB;

Class RoleRepository implements RoleInterface 
{
    public function __construct(
        private Role $role
    ){}


    public function all(): array
    {
        try {
            $roles = $this->role::all();
            return [
                'success' => true,
                'message' => 'roles retreived successfully',
                'roles' => $roles,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Could not retreive roles",
            ];
        }
    }

    public function getRole($id) : array{
        $role = $this->role::find($id);
        if (!$role) {
            return [
                'success' => false,
                'message' => 'Role not found'
            ];
        }

        return ['success' => 1, 'message' => 'permissions retreived', 'role' => $role];
    }

    public function createRole(CreateRoleRequest $request): array
    {   
        $role = null;
        try {
            DB::transaction(function() use(&$role, $request){
                $role = Role::create([
                    'name' => $request->name,
                    'type' => $this->role::TYPES['CUSTOM']
                ]);
                $role->permissions()->sync($request->permissions);
            });
            return [
                'success' => true,
                'message' => 'Role created successfully.',
                'role' => $role
            ];
        } catch (\Throwable $th) {
            return ['success' => 0, 'message' => 'Error creating role'];
        }
        
    }

    public function updatePermissions(UpdateRoleRequest $request): array
    {
        try{
            $role = $this->role::find($request['role']);
            if (!$role) {
                return [
                    'success' => false,
                    'message' => 'Role not found'
                ];
            }
            $role -> permissions() -> sync($request['permissions']);
            return ['success' => 1, 'message' => 'Role updated', 'role' => $role];
        }
        catch(Exception $e){
            return ['success' => 0, 'message' => 'error updating permissions'];
        }
    }
    
    public function destroyRole(int $id):array
    {   
        $role = $this->role::find($id);
        if (!$role) {
            return [
                'success' => false,
                'message' => 'Role not found'
            ];
        }
        if (!$role) return ['success' => 0, 'message' => 'role not found'];
        if ($role->users->count() > 0) return ['success' => 0, 'message' => 'Can not delete this role because it is assigned to users'];
        if ($role->type === $this->role::TYPES['DEFAULT']) return ['success' => 0, 'message' => 'Can not delete this role because it is a default one'];
        $role->delete();
        return [
            'success' => true,
            'message' => 'Role deleted successfully'
        ];
    }
}


