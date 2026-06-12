<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// NOTE: enum() columns are intentionally replaced with string() for
// SQLite (local dev) ↔ PostgreSQL/Supabase (production) portability.
// Allowed values are enforced at the model/request layer.

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // ── Core identity ─────────────────────────────────
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('password');
            $table->rememberToken();

            // ── Account control ───────────────────────────────
            // status: pending | approved | rejected
            $table->string('status')->default('pending');
            // role: user | admin
            $table->string('role')->default('user');

            // ── Personal details ──────────────────────────────
            $table->string('gender')->nullable();          // Male | Female | Other
            $table->date('birthday')->nullable();
            $table->unsignedSmallInteger('age')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('religion')->nullable();
            $table->string('home_municipality')->nullable();
            $table->string('home_barangay')->nullable();
            $table->string('permanent_address')->nullable();

            // ── Contact & social ──────────────────────────────
            $table->string('facebook_account')->nullable();
            $table->string('preferred_contact')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('location')->nullable();
            $table->string('workplace')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->string('linkedin_link')->nullable();

            // ── Academic background ───────────────────────────
            // alumni_status: Yes | No
            $table->string('alumni_status')->nullable();
            $table->string('level')->nullable();           // Elementary | Junior High School | Senior High School | College
            $table->string('program', 20)->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->string('highest_degree')->nullable();
            $table->string('honors')->nullable();
            $table->string('board_exam')->nullable();
            $table->text('other_schools')->nullable();

            // ── Employment ────────────────────────────────────
            $table->string('present_occupation')->nullable();
            $table->text('other_experiences')->nullable();
            $table->string('company_address')->nullable();

            // ── College-alumni specific ───────────────────────
            $table->string('academic_performance')->nullable();
            $table->string('employment_status')->nullable(); // Employed | Unemployed
            $table->string('employment_type')->nullable();   // Locally | Abroad | Self-employed
            $table->string('unemployment_reason')->nullable();
            $table->string('time_to_first_job')->nullable();
            $table->string('job_related')->nullable();       // Yes | No
            $table->text('changes_needed')->nullable();      // comma-separated selections

            // ── Miscellaneous ─────────────────────────────────
            $table->text('comments')->nullable();
            $table->string('consent')->nullable();           // Yes | No

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
