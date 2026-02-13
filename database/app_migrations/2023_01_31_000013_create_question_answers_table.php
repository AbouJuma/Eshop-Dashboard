<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionAnswersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'question_answers';

    /**
     * Run the migrations.
     * @table question_answers
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('content')->nullable();
            $table->unsignedInteger('question_id');
            $table->nullableTimestamps();

            $table->index(["question_id"], 'fk_question_answers_questions1_idx');


            $table->foreign('question_id', 'fk_question_answers_questions1_idx')
                ->references('id')->on('questions')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
