<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index($value1, $value2 = null, $fibonacci = null){
        if (!is_numeric($value1) || ($value2 !== null && !is_numeric($value2))) {
            return response()->json(['error' => 'los parametros deben de ser numeros deben de ser numeros enteros'], 400);
        }

        $value1 = (int) $value1;
        $value2 = $value2 ? (int) $value2 : null;

        if ($fibonacci === 'fibonacci') {
            return response()->json([
                'fibonacci' => $this->fibonacciSequence($value1, $value2)
            ]);
        } elseif ($value2 !== null) {
            if ($value2 < $value1) {
                return response()->json(['error', 'El segundo valor debe de ser mayor a el primer valor'], 400);
            }

            $result =[];
            for ($i = $value1; $i <= $value2; $i++) {
                $result[$i] = $this->multiplicationTable($i);
            }

            return response()->json($result);
        } else {
            return response()->json($this->multiplicationTable($value1));
        }
    }

    private function multiplicationTable($number)
    {
        $table = [];
        for ($i = 1; $i <= 10; $i++) {
            $table[$i] = $number * $i;
        }
        return $table;
    }

    private function fibonacciSequence($start, $end)
    {
        $sequence = [];
        $a = 0;
        $b = 1;

        while ($a <= ($end ?? PHP_INT_MAX)) {
            if ($a >= $start) {
                $sequence[] = $a;
            }
            $next = $a + $b;
            $a = $b;
            $b = $next;
        }

        return $sequence;
    }
}
