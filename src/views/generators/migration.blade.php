<?php echo '<?php' ?>

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EntrustSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();

        // Create table for storing roles
        Schema::create('{{ $rolesTable }}', function (Blueprint $table) {
            $table->increments('m_application_role_id');
            $table->string('m_application_role_name');
            $table->boolean('m_application_role_is_active');
            $table->integer('m_application_role_modby');
            $table->datetime('m_application_role_update_time');
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('{{ $roleUserTable }}', function (Blueprint $table) {
            $table->increments('m_staff_application_role_id');
            $table->integer('d_staff_id')->unsigned();
            $table->integer('m_application_role_id')->unsigned();

            $table->foreign('d_staff_id')->references('{{ $userKeyName }}')->on('{{ $usersTable }}')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('m_application_role_id')->references('m_application_role_id')->on('{{ $rolesTable }}')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->integer('m_staff_application_role_modby');
            $table->datetime('m_staff_application_role_update_time');
         
        });

        // Create table for storing permissions
        Schema::create('{{ $permissionsTable }}', function (Blueprint $table) {
            $table->increments('m_application_permission_id');
            $table->string('m_application_permission_name')->unique();
            $table->boolean('m_application_permission_is_active');
            $table->integer('m_application_permission_modby');
            $table->datetime('m_application_permission_last_update');
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('{{ $permissionRoleTable }}', function (Blueprint $table) {
            $table->integer('m_application_permission_id')->unsigned();
            $table->integer('m_application_role_id')->unsigned();

            $table->foreign('m_application_permission_id')->references('m_application_permission_id')->on('{{ $permissionsTable }}')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('m_application_role_id')->references('m_application_role_id')->on('{{ $rolesTable }}')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->integer('m_application_role_permission_modby');
            $table->datetime('m_application_role_permission_last_update');
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('{{ $permissionRoleTable }}');
        Schema::drop('{{ $permissionsTable }}');
        Schema::drop('{{ $roleUserTable }}');
        Schema::drop('{{ $rolesTable }}');
    }
}
