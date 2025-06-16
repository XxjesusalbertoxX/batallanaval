<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            -- 1) Creamos la función que calcula y actualiza la precisión
            CREATE OR REPLACE FUNCTION update_user_precision() RETURNS TRIGGER AS $$
            DECLARE
                uid INTEGER;
                total_moves INTEGER;
                total_hits INTEGER;
                new_precision NUMERIC;
            BEGIN
                -- 1.1) Obtener el user_id desde player_games
                SELECT user_id INTO uid
                  FROM player_games
                 WHERE id = NEW.player_game_id;

                -- 1.2) Contar todos los movimientos y hits de ese usuario
                SELECT COUNT(*)           INTO total_moves
                  FROM moves m
                  JOIN player_games pg ON m.player_game_id = pg.id
                 WHERE pg.user_id = uid;

                SELECT SUM((m.hit::int)) INTO total_hits
                  FROM moves m
                  JOIN player_games pg ON m.player_game_id = pg.id
                 WHERE pg.user_id = uid;

                -- 1.3) Calcular la nueva precisión (porcentaje)
                IF total_moves = 0 THEN
                    new_precision := 0;
                ELSE
                    new_precision := ROUND((total_hits::numeric / total_moves) * 100);
                END IF;

                -- 1.4) Actualizar la tabla users
                UPDATE users
                   SET precision = new_precision
                 WHERE id = uid;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            -- 2) Creamos el trigger que llama a esa función tras cada INSERT en moves
            CREATE TRIGGER trg_update_user_precision
            AFTER INSERT ON moves
            FOR EACH ROW
            EXECUTE FUNCTION update_user_precision();
        ');
    }

    public function down(): void
    {
        DB::unprepared('
            DROP TRIGGER IF EXISTS trg_update_user_precision ON moves;
            DROP FUNCTION IF EXISTS update_user_precision();
        ');
    }
};
