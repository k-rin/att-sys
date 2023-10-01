<?php

namespace App\Providers;

use App\Enums\AdminRole;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Guards\GoogleTokenGuard;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use DB;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // define Gate
        Gate::define('isMaster', function ($admin) {
            return $admin->role == AdminRole::Master;
        });
        Gate::define('isNotReadonly', function ($admin) {
            return $admin->role != AdminRole::Readonly;
        });
        Gate::define('isManager', function ($user) {
            return $user->isManager;
        });

        Auth::extend('token', function () {
            return new GoogleTokenGuard(
                app(TokenUserProvider::class),
                app('request')
            );
        });

        // debug sql statement
        //DB::listen(function ($query) {
        //    $bindings = implode(',', $query->bindings);
        //    \Log::info("Query Time:{$query->time}s {$query->sql}: {$bindings}");
        //});
    }
}