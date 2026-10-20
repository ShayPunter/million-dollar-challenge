<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Google accounts created before email verification existed never had
     * email_verified_at set. Mark them verified so they don't see the prompt.
     *
     * Admin emails are left out: verification is part of the admin check
     * (User::isAdmin), so they get it from Google itself on the next sign-in.
     */
    public function up(): void
    {
        $adminEmails = array_map('strtolower', config('app.admin_emails', []));

        DB::table('users')
            ->whereNotNull('google_id')
            ->whereNull('email_verified_at')
            ->orderBy('id')
            ->get(['id', 'email'])
            ->reject(fn ($user) => in_array(strtolower($user->email), $adminEmails, true))
            ->each(fn ($user) => DB::table('users')
                ->where('id', $user->id)
                ->update(['email_verified_at' => now()]));
    }

    /**
     * Nothing to undo: the old timestamps can't be told apart from real ones.
     */
    public function down(): void {}
};
