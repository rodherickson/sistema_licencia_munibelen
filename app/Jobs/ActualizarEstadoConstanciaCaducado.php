<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Constancia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ActualizarEstadoConstanciaCaducado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fechaActual = Carbon::now();
        $constancias = Constancia::whereIn('estado', ['Expedido', 'Por Caducar'])->get();

        foreach ($constancias as $constancia) {
            $fechaCaducidad = Carbon::parse($constancia->fechaCaducidad);

            if ($fechaCaducidad->lessThanOrEqualTo($fechaActual)) {
                $constancia->estado = 'Caducado';
                $constancia->save();
            } else {
                // Calcular los días para caducar
                $diasParaCaducar = $fechaActual->diffInDays($fechaCaducidad);
                if ($diasParaCaducar <= 7) {
                    $constancia->estado = 'Por Caducar';
                    $constancia->save();
                }
            }
        }
    }
}
