<?php

namespace App\Traits;

trait GenerateBoard
{
    public function generateRandomBoard(int $shipCount = 15): array
    {
        $board = array_fill(0, 8, array_fill(0, 8, 0));
        // Tablero 8x8 lleno de agua (0)
        // Generar todas las coordenadas posibles
        $positions = [];
        for ($x = 0; $x < 8; $x++) {
            for ($y = 0; $y < 8; $y++) {
                $positions[] = [$x, $y];
            }
        }

        // Mezclar posiciones y seleccionar las primeras $shipCount
        shuffle($positions);
        $selected = array_slice($positions, 0, $shipCount);
        //array_slice devuelve un array con las primeras $shipCount posiciones seleccionadas

        // Colocar barcos en las posiciones seleccionadas
        foreach ($selected as [$x, $y]) {
            $board[$x][$y] = 1;
        }

        return $board;
    }
}
