<?php
namespace App\Http\Repositories;

use App\Http\Interfaces\PermissionInterface;
use App\Http\Requests\Permission\CreatePermissionRequest;
use App\Models\Permission;
use Exception;

Class PermissionRepository implements PermissionInterface 
{   
    public function __construct(
        private Permission $permission
    ){}

    
    public function all(): array
    {
        try {
            $permissions = $this->permission::all();
            return [
                'success' => true,
                'message' => 'All permissionss are gotten successfully',
                'permissions' => $permissions,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Could not retreive permissionss",
            ];
        }
    }

    public function createPermission(CreatePermissionRequest $request) : array
    {
        try{
            $permission = $this->permission::create([
                'name' => $request['name'],
                'type' => $this->permission::TYPES['CUSTOM']
            ]);
            return ['success' => 1, 'message' => 'permission created', 'permission' => $permission];
        }
        catch(Exception $e){
            return ['success' => 0, 'message' => 'error adding permission'];
        }
    }

    public function destroyPermission($id) : array
    {
        $permission = $this->permission::find($id);
        if(!$permission) return ['success' => 0, 'message' => 'Permission not found'];
        if ($permission->roles && $permission->roles->count()>0) return ['success' => 0, 'message' => 'Can not delete this permission because it is assigned to roles'];
        if ($permission->type === $this->permission::TYPES['DEFAULT']) return ['success' => 0, 'message' => 'Can not delete this permission because it is a default one'];
        try{
            $permission->delete();
        }
        catch(Exception $e){
            return ['success' => 0, 'message' => 'error while deleting permission'];
        }

        return ['success' =>1, 'message' => 'permission deleted'];
    }
}