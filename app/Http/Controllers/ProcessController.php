<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcessController extends Controller
{
    public function handleProcess(Request $request)
    {
        try {
            $startTime = microtime(true);

// Primera consulta: Insert
            DB::table('tests')->insert(['text' => 'value1']);
            usleep(500000); // 500ms

// Segunda consulta: Update
            DB::table('tests')->where('id', 1)->update(['text' => 'new_value']);
            usleep(500000); // 500ms

// Tercera consulta: Select
            $data = DB::table('tests')->where('id', 1)->first();
            usleep(500000); // 500ms

            $endTime = microtime(true);
            $responseOfHeader = $request->header('responseof');

            return response()->json([
                'query_no' => mt_rand(1, 1000), // Simula un número de consulta.
                'duration' => round(($endTime - $startTime) * 1000), // Tiempo en ms.
                'status' => 'ok',
                'message' => 'Consultas ejecutadas correctamente.',
                'cabecera' => $responseOfHeader,
                'data' => $data,
            ])->header('Server', gethostname());
        } catch (\Exception $e) {
            return response()->json([
                'query_no' => mt_rand(1, 1000),
                'duration' => 0,
                'status' => 'error',
                'message' => $e->getMessage(),
            ])->header('Server', gethostname());
        }
    }
}
