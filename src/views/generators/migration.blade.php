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

            $table->integer('{{ $rolesTable }}_id', true);
            $table->string('{{ $rolesTable }}_name', 255);
            $table->tinyInteger('{{ $rolesTable }}_is_active')->default(1);
            $table->dateTime('{{ $rolesTable }}_update_time');
            $table->dateTime('{{ $rolesTable }}_create_time');
            $table->integer('{{ $rolesTable }}_modby');
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('{{ $roleUserTable }}', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('{{ $roleUserTable }}_id', true);
            $table->integer('{{ $usersTable }}_id');
            $table->integer('{{ $rolesTable }}_id');
            $table->string('{{ $roleUserTable }}_name', 255);
            $table->dateTime('{{ $roleUserTable }}_update_time');
            $table->dateTime('{{ $roleUserTable }}_create_time');
            $table->integer('{{ $roleUserTable }}_modby');

            $table->index('{{ $rolesTable }}_id', 'fk_{{ $roleUserTable }}_{{ $rolesTable }}1_idx');
            $table->foreign('{{ $usersTable }}_id', 'fk_{{ $roleUserTable }}_{{ $usersTable }}')->references('{{ $userKeyName }}')->on('{{ $usersTable }}')->onDelete('no action')->onUpdate('no action');
            $table->foreign('{{ $rolesTable }}_id', 'fk_{{ $roleUserTable }}_{{ $rolesTable }}1')->references('{{ $rolesTable }}_id')->on('{{ $rolesTable }}')->onDelete('no action')->onUpdate('no action');
        });

        // Create table for storing permissions
        Schema::create('{{ $permissionsTable }}', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('{{ $permissionsTable }}_id', true);
            $table->string('{{ $permissionsTable }}_name', 255)->unique();
            $table->string('{{ $permissionsTable }}_description', 255)->nullable();
            $table->tinyInteger('{{ $permissionsTable }}_is_active')->default(1);
            $table->dateTime('{{ $permissionsTable }}_update_time');
            $table->dateTime('{{ $permissionsTable }}_create_time');
            $table->integer('{{ $permissionsTable }}_modby');

            $table->index('{{ $permissionsTable }}_name', '{{ $permissionsTable }}_name_UNIQUE')->unique();
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('{{ $permissionRoleTable }}', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('{{ $rolesTable }}_id');
            $table->integer('{{ $permissionsTable }}_id');
            $table->dateTime('{{ $permissionRoleTable }}_update_time');
            $table->dateTime('{{ $permissionRoleTable }}_create_time');
            $table->integer('{{ $permissionRoleTable }}_modby');

            $table->primary(['{{ $rolesTable }}_id', '{{ $permissionsTable }}_id'], 'm_app_role_m_app_perm_primary');
            $table->index('{{ $permissionsTable }}_id', 'fk_{{ $permissionRoleTable }}_{{ $permissionsTable }}1_idx');
            $table->foreign('{{ $permissionsTable }}_id', 'fk_{{ $permissionRoleTable }}_{{ $permissionsTable }}1')->references('{{ $permissionsTable }}_id')->on('{{ $permissionsTable }}')->onDelete('no action')->onUpdate('no action');
            $table->foreign('{{ $rolesTable }}_id', 'fk_{{ $permissionRoleTable }}_{{ $rolesTable }}1')->references('{{ $rolesTable }}_id')->on('{{ $rolesTable }}')->onDelete('no action')->onUpdate('no action');
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
