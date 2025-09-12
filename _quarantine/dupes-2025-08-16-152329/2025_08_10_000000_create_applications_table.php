<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add foreign keys if the table already exists
        Schema::table('applications', function (Blueprint $table) {
            // Add foreign key if it doesn't already exist
            if (!Schema::hasColumn('applications', 'user_id')) {
                $table->bigInteger('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }

            if (!Schema::hasColumn('applications', 'opportunity_id')) {
                $table->bigInteger('opportunity_id')->unsigned()->nullable();
                $table->foreign('opportunity_id')->references('id')->on('opportunities')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key constraints when rolling back
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'user_id')) {
                $table->dropForeign(['user_id']);
            }

            if (Schema::hasColumn('applications', 'opportunity_id')) {
                $table->dropForeign(['opportunity_id']);
            }
        });
    }
}

