<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verify Your Email Address — URLC Research Portal')
                ->greeting('Hello ' . ($notifiable->name ?? 'Researcher') . '!')
                ->line('Welcome to the University Research Lifecycle – Digital Platform (URLC) at the University of Antique.')
                ->line('Please click the button below to verify your email address and complete your account setup:')
                ->action('Verify Email Address', $url)
                ->line('This verification link will expire in 60 minutes for your security.')
                ->line('If you did not register for an account on the URLC portal, please disregard this email.')
                ->salutation("Best regards,\nUniversity Research & Development Services\nUniversity of Antique");
        });
    }
}
