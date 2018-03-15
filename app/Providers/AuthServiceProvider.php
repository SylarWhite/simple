<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = array(
        'App\Model' => 'App\Policies\ModelPolicy',

        // 授权模型和授权策略绑定
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Status::class => \App\Policies\StatusPolicy::class
    );

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
