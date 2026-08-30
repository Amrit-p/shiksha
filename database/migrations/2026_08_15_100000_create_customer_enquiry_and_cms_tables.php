<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiry_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_number')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30);
            $table->string('company')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pin_code', 20)->nullable();
            $table->text('message')->nullable();
            $table->foreignId('enquiry_status_id')->constrained('enquiry_statuses')->restrictOnDelete();
            $table->string('source')->default('website');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['enquiry_status_id', 'created_at']);
            $table->index(['email', 'phone']);
            $table->index('assigned_to');
            $table->index('created_at');
        });

        Schema::create('enquiry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained('enquiries')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('variants')->nullOnDelete();
            $table->foreignId('variant_attribute_id')->nullable()->constrained('variant_attributes')->nullOnDelete();
            $table->string('product_title');
            $table->string('product_sku')->nullable();
            $table->string('variation_label')->nullable();
            $table->string('product_image')->nullable();
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->unsignedInteger('qty')->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('enquiry_id');
            $table->index('product_id');
        });

        Schema::create('enquiry_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained('enquiries')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['enquiry_id', 'created_at']);
        });

        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->boolean('show_in_footer')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text');
            $table->timestamps();

            $table->index('group');
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subject');
            $table->longText('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('smtp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('host')->nullable();
            $table->unsignedInteger('port')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->string('encryption')->nullable();
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            $table->string('admin_email')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
        Schema::dropIfExists('smtp_settings');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('website_settings');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('enquiry_activities');
        Schema::dropIfExists('enquiry_items');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('enquiry_statuses');
    }
};
