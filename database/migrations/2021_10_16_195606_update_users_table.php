<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;


class UpdateUsersTable extends Migration
{
    protected $table = 'users';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_img')->default('no_image.jpg')->after('remember_token');
        });

        $userModel = new \App\Models\User();
        $users = $userModel->get_users_id();
        foreach($users as $k => $user){
            DB::table($this->table)->where('id', $user->id)->update([
                'profile_img' => 'no_image.jpg'
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
        //
    }
}
