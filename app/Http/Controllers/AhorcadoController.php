<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class AhorcadoController extends Controller
{
    private $palabras = ['programacion', 'laravel', 'javascript', 'ahorcado'];

    // Iniciar un nuevo juego
    public function iniciarJuego()
    {
        // Seleccionar una palabra aleatoria
        $palabra = $this->palabras[array_rand($this->palabras)];

        // Generar un ID único para el juego
        $juegoId = Str::uuid();

        // Inicializar el estado del juego
        $estadoJuego = [
            'palabra' => $palabra,
            'oculta' => str_repeat('_', strlen($palabra)),
            'intentos' => [],
            'fallos' => 0,
            'max_fallos' => 6,
            'ganado' => false,
            'perdido' => false,
        ];

        // Guardar el estado del juego en caché (puedes usar base de datos si prefieres)
        Cache::put("juego_$juegoId", $estadoJuego, 3600); // 1 hora de duración

        // Retornar la respuesta con el estado inicial del juego
        return response()->json([
            'id' => $juegoId,
            'oculta' => $estadoJuego['oculta'],
            'fallos' => $estadoJuego['fallos'],
            'max_fallos' => $estadoJuego['max_fallos'],
        ]);
    }

    // Hacer un intento (adivinar una letra)
    public function hacerIntento($id, Request $request)
    {
        // Recuperar el estado del juego desde la caché
        $estadoJuego = Cache::get("juego_$id");

        if (!$estadoJuego) {
            return response()->json(['error' => 'Juego no encontrado'], 404);
        }

        // Validar que la letra es correcta
        $request->validate([
            'letra' => 'required|alpha|size:1'
        ]);

        $letra = strtolower($request->input('letra'));

        // Verificar si la letra ya fue intentada
        if (in_array($letra, $estadoJuego['intentos'])) {
            return response()->json(['error' => 'Letra ya intentada'], 400);
        }

        // Agregar la letra a los intentos
        $estadoJuego['intentos'][] = $letra;

        // Comprobar si la letra está en la palabra
        if (strpos($estadoJuego['palabra'], $letra) !== false) {
            // Reemplazar los guiones bajos por la letra acertada
            $oculta = str_split($estadoJuego['oculta']);
            foreach (str_split($estadoJuego['palabra']) as $index => $char) {
                if ($char === $letra) {
                    $oculta[$index] = $letra;
                }
            }
            $estadoJuego['oculta'] = implode('', $oculta);

            // Comprobar si el jugador ha ganado
            if ($estadoJuego['oculta'] === $estadoJuego['palabra']) {
                $estadoJuego['ganado'] = true;
            }
        } else {
            // Incrementar los fallos si la letra no está en la palabra
            $estadoJuego['fallos']++;

            // Comprobar si el jugador ha perdido
            if ($estadoJuego['fallos'] >= $estadoJuego['max_fallos']) {
                $estadoJuego['perdido'] = true;
            }
        }

        // Actualizar el estado del juego en la caché
        Cache::put("juego_$id", $estadoJuego, 3600);

        // Retornar el estado actualizado del juego
        return response()->json([
            'oculta' => $estadoJuego['oculta'],
            'fallos' => $estadoJuego['fallos'],
            'max_fallos' => $estadoJuego['max_fallos'],
            'ganado' => $estadoJuego['ganado'],
            'perdido' => $estadoJuego['perdido'],
        ]);
    }

    // Obtener el estado actual del juego
    public function obtenerEstado($id)
    {
        // Recuperar el estado del juego desde la caché
        $estadoJuego = Cache::get("juego_$id");

        if (!$estadoJuego) {
            return response()->json(['error' => 'Juego no encontrado'], 404);
        }

        // Retornar el estado actual del juego
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
