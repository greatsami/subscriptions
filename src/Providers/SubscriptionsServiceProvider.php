<?php

declare(strict_types=1);

namespace Gratesami\Subscriptions\Providers;

use Gratesami\Subscriptions\Models\Plan;
use Illuminate\Support\ServiceProvider;
use Gratesami\Subscriptions\Models\PlanFeature;
use Gratesami\Subscriptions\Models\PlanSubscription;
use Gratesami\Subscriptions\Models\PlanSubscriptionUsage;
use Gratesami\Subscriptions\Console\Commands\MigrateCommand;
use Gratesami\Subscriptions\Console\Commands\PublishCommand;
use Gratesami\Subscriptions\Console\Commands\RollbackCommand;

class SubscriptionsServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'gratesami.subscriptions');

        // Bind eloquent models to IoC container
        $this->app->singleton('gratesami.subscriptions.plan', $planModel = $this->app['config']['gratesami.subscriptions.models.plan']);
        $planModel === Plan::class || $this->app->alias('gratesami.subscriptions.plan', Plan::class);

        $this->app->singleton('gratesami.subscriptions.plan_feature', $planFeatureModel = $this->app['config']['gratesami.subscriptions.models.plan_feature']);
        $planFeatureModel === PlanFeature::class || $this->app->alias('gratesami.subscriptions.plan_feature', PlanFeature::class);

        $this->app->singleton('gratesami.subscriptions.plan_subscription', $planSubscriptionModel = $this->app['config']['gratesami.subscriptions.models.plan_subscription']);
        $planSubscriptionModel === PlanSubscription::class || $this->app->alias('gratesami.subscriptions.plan_subscription', PlanSubscription::class);

        $this->app->singleton('gratesami.subscriptions.plan_subscription_usage', $planSubscriptionUsageModel = $this->app['config']['gratesami.subscriptions.models.plan_subscription_usage']);
        $planSubscriptionUsageModel === PlanSubscriptionUsage::class || $this->app->alias('gratesami.subscriptions.plan_subscription_usage', PlanSubscriptionUsage::class);

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateCommand::class,
                PublishCommand::class,
                RollbackCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish Resources
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('subscriptions.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
