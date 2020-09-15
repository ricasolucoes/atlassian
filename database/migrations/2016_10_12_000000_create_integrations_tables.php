<?php


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntegrationsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'integrations', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id')->unsigned();
                $table->string('name', 255)->nullable();
                $table->string('code');
                $table->integer('status')->default(1);
                $table->unique(['code']);
                $table->timestamps();
                $table->softDeletes();
            }
        );
        Schema::create(
            'services', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id')->unsigned();
                $table->string('name', 255)->nullable();
                $table->integer('status')->default(1);
                $table->bigInteger('integration_id')->unsigned();
                $table->timestamps();
                $table->softDeletes();
            }
        );

        /**
         * Account
         */
        Schema::create(
            'accounts', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id')->unsigned();
                $table->string('username', 255);
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->integer('status')->default(1);
                $table->string('customize_url')->nullable();
                $table->bigInteger('integration_id')->unsigned();
                $table->unique(['username', 'integration_id']);
                $table->timestamps();
                $table->softDeletes();
            }
        );
        Schema::table(
            'accounts', function (Blueprint $table) {
                $table->foreign('integration_id')->references('id')->on('integrations');
            }
        );
        
        /**
         * Accountables
         */
        Schema::create(
            'accountables', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id')->unsigned();
                $table->bigInteger('account_id')->unsigned(); //->nullable();
                $table->boolean('is_sincronizado')->default(false);
                $table->string('accountable_id');
                $table->string('accountable_type', 255);
                $table->timestamps();
                $table->softDeletes();
            }
        );
        Schema::table(
            'accountables', function (Blueprint $table) {
                $table->foreign('account_id')->references('id')->on('accounts');
            }
        );
        
        /**
         * Tokens
         */
        Schema::create(
            'tokens', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('token');
                $table->integer('status')->default(1);
                $table->string('obs')->nullable();
                $table->json('scopes')->nullable();
                $table->bigInteger('account_id')->unsigned();
                $table->unique(['token', 'account_id']);
                $table->timestamps();
            }
        );
        Schema::table(
            'tokens', function (Blueprint $table) {
                $table->foreign('account_id')->references('id')->on('accounts');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('integrations');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('accountables');
        Schema::dropIfExists('tokens');
    }
}
