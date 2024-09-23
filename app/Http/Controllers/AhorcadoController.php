<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class AhorcadoController extends Controller
{
    private $palabras = ['pepe', 'elniñobrasileñojugandofutbchibolenelvientredesumadrejaja', 'kevinglezanc', 'ahorcado'];

    public function iniciarJuego()
    {
        $palabra = $this->palabras[array_rand($this->palabras)];

        $juegoId = Str::uuid();

        $estadoJuego = [
            'palabra' => $palabra,
            'oculta' => str_repeat('_', strlen($palabra)),
            'intentos' => [],
            'fallos' => 0,
            'max_fallos' => 6,
            'ganado' => false,
            'perdido' => false,
        ];
        Cache::put("juego_$juegoId", $estadoJuego, 3600);

        return response()->json([
            'id' => $juegoId,
            'oculta' => $estadoJuego['oculta'],
            'fallos' => $estadoJuego['fallos'],
            'max_fallos' => $estadoJuego['max_fallos'],
        ]);
    }

    public function hacerIntento($id, Request $request)
    {
        $estadoJuego = Cache::get("juego_$id");

        if (!$estadoJuego) {
            return response()->json(['error' => 'Juego no encontrado'], 404);
        }

        $request->validate([
            'letra' => 'required|alpha|size:1'
        ]);

        $letra = strtolower($request->input('letra'));

        if (in_array($letra, $estadoJuego['intentos'])) {
            return response()->json(['error' => 'Letra ya intentada'], 400);
        }

        $estadoJuego['intentos'][] = $letra;

        if (strpos($estadoJuego['palabra'], $letra) !== false) {
            $oculta = str_split($estadoJuego['oculta']);
            foreach (str_split($estadoJuego['palabra']) as $index => $char) {
                if ($char === $letra) {
                    $oculta[$index] = $letra;
                }
            }
            $estadoJuego['oculta'] = implode('', $oculta);

            if ($estadoJuego['oculta'] === $estadoJuego['palabra']) {
                $estadoJuego['ganado'] = true;
            }
        } else {
            $estadoJuego['fallos']++;

            if ($estadoJuego['fallos'] >= $estadoJuego['max_fallos']) {
                $estadoJuego['perdido'] = true;
            }
        }

        Cache::put("juego_$id", $estadoJuego, 3600);

        return response()->json([
            'oculta' => $estadoJuego['oculta'],
            'fallos' => $estadoJuego['fallos'],
            'max_fallos' => $estadoJuego['max_fallos'],
            'ganado' => $estadoJuego['ganado'],
            'perdido' => $estadoJuego['perdido'],
        ]);
    }

    public function obtenerEstado($id)
    {
        $estadoJuego = Cache::get("juego_$id");

        if (!$estadoJuego) {
            return response()->json(['error' => 'Juego no encontrado'], 404);
        }

        return response()->json([
            'oculta' => $estadoJuego['oculta'],
            'fallos' => $estadoJuego['fallos'],
            'max_fallos' => $estadoJuego['max_fallos'],
            'ganado' => $estadoJuego['ganado'],
            'perdido' => $estadoJuego['perdido'],
            'intentos' => $estadoJuego['intentos']
        ]);
    }
}
