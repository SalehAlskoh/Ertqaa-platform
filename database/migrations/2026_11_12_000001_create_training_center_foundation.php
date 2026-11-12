<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('users')->whereRaw('CHAR_LENGTH(name) > 150')->exists()) {
            throw new RuntimeException('Existing user names exceed 150 characters; reconcile without truncation before migrating.');
        }
        Schema::table('users', function (Blueprint $t) {
            $t->string('name', 150)->change();
            $t->boolean('is_active')->default(true);
        });
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('name', 50)->unique();
        });
        Schema::create('role_user', function (Blueprint $t) {
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('role_id')->constrained()->cascadeOnDelete();
            $t->primary(['user_id', 'role_id']);
        });
        Schema::create('student_profiles', function (Blueprint $t) {
            $t->foreignId('user_id')->primary()->constrained()->restrictOnDelete();
            $t->string('phone_number', 30)->nullable();
            $t->date('date_of_birth')->nullable();
            $t->string('education_level', 100)->nullable();
            $t->string('institution_name', 150)->nullable();
            $t->string('city', 100)->nullable();
            $t->timestamps();
        });
        Schema::create('instructor_profiles', function (Blueprint $t) {
            $t->foreignId('user_id')->primary()->constrained()->restrictOnDelete();
            $t->string('professional_title', 150)->nullable();
            $t->text('biography')->nullable();
            $t->unsignedSmallInteger('experience_years')->nullable();
            $t->timestamps();
        });
        Schema::create('course_categories', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120)->unique();
            $t->string('slug', 150)->unique();
            $t->timestamps();
        });
        Schema::create('courses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->constrained('course_categories')->restrictOnDelete();
            $t->string('title', 200);
            $t->string('slug', 220)->unique();
            $t->text('description')->nullable();
            $t->decimal('planned_hours', 6, 2)->nullable();
            $t->boolean('is_published')->default(false);
            $t->timestamps();
        });
        Schema::create('training_groups', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->restrictOnDelete();
            $t->foreignId('instructor_user_id')->constrained('instructor_profiles', 'user_id')->restrictOnDelete();
            $t->string('code', 50)->unique();
            $t->unsignedSmallInteger('capacity');
            $t->decimal('fee_amount', 10, 2)->nullable();
            $t->char('currency_code', 3)->nullable();
            $t->dateTime('registration_opens_at');
            $t->dateTime('registration_closes_at');
            $t->string('status', 30)->default('draft');
            $t->timestamps();
            $t->index(['course_id', 'status']);
        });
        Schema::create('lectures', function (Blueprint $t) {
            $t->id();
            $t->foreignId('training_group_id')->constrained()->restrictOnDelete();
            $t->unsignedSmallInteger('lecture_number');
            $t->string('title', 200)->nullable();
            $t->dateTime('starts_at');
            $t->dateTime('ends_at');
            $t->string('status', 20)->default('scheduled');
            $t->text('change_reason')->nullable();
            $t->timestamps();
            $t->unique(['training_group_id', 'lecture_number']);
            $t->index(['training_group_id', 'starts_at']);
        });
        Schema::create('enrollment_applications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_user_id')->constrained('users')->restrictOnDelete();
            $t->foreignId('training_group_id')->constrained()->restrictOnDelete();
            $t->string('status', 30)->default('pending');
            $t->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->restrictOnDelete();
            $t->dateTime('reviewed_at')->nullable();
            $t->text('rejection_reason')->nullable();
            $t->dateTime('offer_expires_at')->nullable();
            $t->timestamps();
            $t->unique(['student_user_id', 'training_group_id'], 'applications_student_group_unique');
            $t->index(['training_group_id', 'status', 'offer_expires_at'], 'applications_group_status_expiry');
            $t->index(['student_user_id', 'status']);
        });
        Schema::create('enrollments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('application_id')->unique()->constrained('enrollment_applications')->restrictOnDelete();
            $t->string('status', 20)->default('active');
            $t->dateTime('enrolled_at');
            $t->dateTime('withdrawn_at')->nullable();
            $t->timestamps();
            $t->index('status');
        });
        Schema::create('public_announcements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('author_user_id')->constrained('users')->restrictOnDelete();
            $t->string('title', 200);
            $t->text('body');
            $t->string('image_path', 500)->nullable();
            $t->dateTime('published_at')->nullable()->index();
            $t->timestamps();
        });
        Schema::create('student_announcements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('author_user_id')->constrained('users')->restrictOnDelete();
            $t->foreignId('course_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('training_group_id')->nullable()->constrained()->restrictOnDelete();
            $t->string('title', 200);
            $t->text('body');
            $t->dateTime('published_at')->nullable();
            $t->timestamps();
            $t->index(['course_id', 'published_at']);
            $t->index(['training_group_id', 'published_at']);
        });
        Schema::create('notification_messages', function (Blueprint $t) {
            $t->id();
            $t->string('type', 50);
            $t->string('title', 200);
            $t->text('body');
            $t->timestamp('created_at');
        });
        Schema::create('notification_recipients', function (Blueprint $t) {
            $t->id();
            $t->foreignId('notification_message_id')->constrained()->restrictOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->dateTime('read_at')->nullable();
            $t->unique(['notification_message_id', 'user_id'], 'recipients_message_user_unique');
            $t->index(['user_id', 'read_at']);
        });
        foreach ([
            ['courses', 'hours_positive', 'planned_hours IS NULL OR planned_hours > 0'],
            ['training_groups', 'capacity_positive', 'capacity > 0'],
            ['training_groups', 'fee_nonnegative', 'fee_amount IS NULL OR fee_amount >= 0'],
            ['training_groups', 'registration_ordered', 'registration_closes_at > registration_opens_at'],
            ['lectures', 'lecture_number_positive', 'lecture_number > 0'],
            ['lectures', 'lecture_dates_ordered', 'ends_at > starts_at'],
            ['student_announcements', 'announcement_target_xor', '(course_id IS NOT NULL) <> (training_group_id IS NOT NULL)'],
        ] as [$table, $name, $expression]) {
            DB::statement("ALTER TABLE `$table` ADD CONSTRAINT `$name` CHECK ($expression)");
        }
    }

    public function down(): void
    {
        foreach (['notification_recipients', 'notification_messages', 'student_announcements', 'public_announcements', 'enrollments', 'enrollment_applications', 'lectures', 'training_groups', 'courses', 'course_categories', 'instructor_profiles', 'student_profiles', 'role_user', 'roles'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn('is_active');
            $t->string('name', 255)->change();
        });
    }
};
