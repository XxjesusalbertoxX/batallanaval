<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    // Esta función se ejecutará antes de cada UPDATE a la tabla "users"
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_user_level()
            RETURNS TRIGGER AS $$
            BEGIN
                -- Solo aplica si la experiencia fue incrementada
                IF NEW.exp > OLD.exp THEN
                    WHILE NEW.exp >= 500 LOOP
                        NEW.exp := NEW.exp - 500;
                        NEW.level := NEW.level + 1;
                    END LOOP;
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Crear el trigger que llama a esa función
        DB::unprepared('
            CREATE TRIGGER trg_update_level
            BEFORE UPDATE ON users
            FOR EACH ROW
            EXECUTE FUNCTION update_user_level();
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_level ON users;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_user_level();');
    }

};
