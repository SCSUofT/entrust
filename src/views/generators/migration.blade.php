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
            $table->engine = 'InnoDB';

            $table->integer('m_application_role_id', true);
            $table->string('m_application_role_name', 255);
            $table->tinyInteger('m_application_role_is_active')->default(1);
            $table->dateTime('m_application_role_update_time');
            $table->dateTime('m_application_role_create_time');
            $table->integer('m_application_role_modby');
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('{{ $roleUserTable }}', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('m_staff_application_role_id', true);
            $table->integer('d_staff_id');
            $table->integer('m_application_role_id');
            $table->string('m_staff_application_role_name', 255);
            $table->dateTime('m_staff_application_role_update_time');
            $table->dateTime('m_staff_application_role_create_time');
            $table->integer('m_staff_application_role_modby');

            $table->index('m_application_role_id', 'fk_m_staff_application_role_m_application_role1_idx');
            $table->foreign('d_staff_id', 'fk_m_staff_role_d_staff')->references('{{ $userKeyName }}')->on('{{ $usersTable }}')->onDelete('no action')->onUpdate('no action');
            $table->foreign('m_application_role_id', 'fk_m_staff_application_role_m_application_role1')->references('m_application_role_id')->on('{{ $rolesTable }}')->onDelete('no action')->onUpdate('no action');
        });

        // Create table for storing permissions
        Schema::create('{{ $permissionsTable }}', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('m_application_permission_id', true);
            $table->string('m_application_permission_name', 255)->unique();
            $table->string('m_application_permission_description', 255)->nullable();
            $table->tinyInteger('m_application_permission_is_active')->default(1);
            $table->dateTime('m_application_permission_update_time');
            $table->dateTime('m_application_permission_create_time');
            $table->integer('m_application_permission_modby');
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('{{ $permissionRoleTable }}', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('m_application_role_id');
            $table->integer('m_application_permission_id');
            $table->dateTime('m_application_role_permission_update_time');
            $table->dateTime('m_application_role_permission_create_time');
            $table->integer('m_application_role_permission_modby');

            $table->primary(['m_application_role_id', 'm_application_permission_id'], 'm_app_role_m_app_perm_primary');
            $table->index('m_application_permission_id', 'fk_m_application_role_permission_m_application_permission1_idx');
            $table->foreign('m_application_permission_id', 'fk_m_application_role_permission_m_application_permission1')->references('m_application_permission_id')->on('{{ $permissionsTable }}')->onDelete('no action')->onUpdate('no action');
            $table->foreign('m_application_role_id', 'fk_m_application_role_permission_m_application_role1')->references('m_application_role_id')->on('{{ $rolesTable }}')->onDelete('no action')->onUpdate('no action');
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
