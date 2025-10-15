<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Professional Information
            $table->string('employment_type')->nullable()->after('position');
            $table->string('employee_id')->unique()->nullable()->after('employment_type');
            $table->string('phone')->nullable()->after('employee_id');
            $table->string('title')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('title');
            $table->string('avatar')->nullable()->after('bio');

            // Organizational Relationships (college_id and department_id already exist in previous migrations)

            // Dates
            $table->date('hire_date')->nullable()->after('avatar');
            $table->date('birth_date')->nullable()->after('hire_date');

            // Contact Information
            $table->text('address')->nullable()->after('birth_date');
            $table->json('emergency_contact')->nullable()->after('address');
            $table->string('emergency_phone')->nullable()->after('emergency_contact');

            // Security Features
            $table->boolean('two_factor_enabled')->default(false)->after('emergency_phone');
            $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->json('two_factor_recovery_codes')->nullable()->after('two_factor_secret');

            // Account Security
            $table->integer('login_attempts')->default(0)->after('last_login_ip');
            $table->timestamp('locked_until')->nullable()->after('login_attempts');
            $table->timestamp('password_changed_at')->nullable()->after('locked_until');
            $table->boolean('must_change_password')->default(false)->after('password_changed_at');

            // User Preferences
            $table->json('preferences')->nullable()->after('must_change_password');
            $table->string('timezone')->default('UTC')->after('preferences');
            $table->string('locale')->default('en')->after('timezone');

            // Add indexes for performance
            $table->index(['is_active', 'position']);
            $table->index(['college_id', 'is_active']);
            $table->index(['department_id', 'is_active']);
            $table->index(['employee_id']);
            $table->index(['locked_until']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Note: college_id and department_id are handled by separate migrations
            
            $table->dropIndex(['is_active', 'position']);
            $table->dropIndex(['college_id', 'is_active']);
            $table->dropIndex(['department_id', 'is_active']);
            $table->dropIndex(['employee_id']);
            $table->dropIndex(['locked_until']);

            $table->dropColumn([
                'employment_type',
                'employee_id',
                'phone',
                'title',
                'bio',
                'avatar',
                'hire_date',
                'birth_date',
                'address',
                'emergency_contact',
                'emergency_phone',
                'two_factor_enabled',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'login_attempts',
                'locked_until',
                'password_changed_at',
                'must_change_password',
                'preferences',
                'timezone',
                'locale',
            ]);
        });
    }
};