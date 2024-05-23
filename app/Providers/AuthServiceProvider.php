<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {   
        $this->registerPolicies();

        foreach (Permission::all() as $permission) {
            Gate::define($permission->name, function (User $user) use ($permission) {
                return $user->permissions->contains('name', $permission->name) ? 
                    Response::allow() : 
                    Response::deny("Forbidden Action");
            });
        }
    }
}
