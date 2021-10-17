<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CreateUserSettingsTable extends Migration
{
    protected $table = 'user_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name');
            $table->string('value');
            $table->timestamps();
        });

        $date = date('Y-m-d H:i:s');
        $userModel = new \App\Models\User();
        $users = $userModel->get_users_id();
        foreach($users as $k => $user){
            DB::table($this->table)->insert([
                'user_id'    => $user->id,
                'name'       => 'notifications',
                'value'      => '1',
                'created_at' => $date
            ]);
            DB::table($this->table)->insert([
                'user_id'    => $user->id,
                'name'       => 'sounds',
                'value'      => '0',
                'created_at' => $date
            ]);
            DB::table($this->table)->insert([
                'user_id'    => $user->id,
                'name'       => 'enter_on_send',
                'value'      => '0',
                'created_at' => $date
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
}
