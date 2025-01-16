<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcessController extends Controller
{
    public function handleProcess(Request $request)
    {
        try {
            $this->handleQuery1();
        }catch (\Exception $e) {
            return $this->reponseError($e, $request);
        }
        try {
            $this->handleQuery2();
        }catch (\Exception $e) {
            return $this->reponseError($e, $request);
        }
        try {
            $startTime1 = $this->handleQuery3();
        }catch (\Exception $e) {
            return $this->reponseError($e, $request);
        }

        $responseOfHeader = $request->header('Responseof');
        $endTime = microtime(true);
        return response()->json([
            'query_no' => mt_rand(1, 1000), // Simula un número de consulta.
            'duration' => round(($endTime - $startTime1) * 1000), // Tiempo en ms.
            'status' => 'ok',
            'message' => 'Consultas ejecutadas correctamente.',
            'cabecera' => $responseOfHeader,
        ])->header('Server', gethostname());
    }

    private function handleQuery1()
    {
        $startTime = microtime(true);
        DB::table('tests')->insert(['text' => 'value1']);
        usleep(500000); // 500ms
        return $startTime;
    }

    private function handleQuery2()
    {
        $startTime = microtime(true);
        DB::table('tests')->where('id', 1)->update(['text' => 'new_value']);
        usleep(500000); // 500ms
        return $startTime;
    }

    private function handleQuery3()
    {
        $startTime = microtime(true);
        $data = DB::table('tests')->where('id', 1)->first();
        usleep(500000); // 500ms
        return $startTime;
    }

    private function reponseError(\Exception $e, $request)
    {
        $responseOfHeader = $request->header('Responseof');
        return response()->json([
            'query_no' => mt_rand(1, 1000),
            'duration' => 0,
            'status' => 'error',
            'message' => $e->getMessage(),
            'cabecera' => $responseOfHeader,
        ])->header('Server', gethostname());
    }
}
