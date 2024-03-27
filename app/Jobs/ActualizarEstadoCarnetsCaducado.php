<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CarnetModel;
use Illuminate\Support\Carbon;

class ActualizarEstadoCarnetsCaducado implements ShouldQueue
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
        $carnets = CarnetModel::whereIn('estado', ['Expedido', 'Por Caducar'])->get();

        foreach ($carnets as $carnet) {
            $fechaCaducidad = Carbon::parse($carnet->fechaCaducidad);

            if ($fechaCaducidad->lessThanOrEqualTo($fechaActual)) {
                $carnet->estado = 'Caducado';
                $carnet->save();
            } else {
                // Calcular los días para caducar
                $diasParaCaducar = $fechaActual->diffInDays($fechaCaducidad);
                if ($diasParaCaducar <= 7) {
                    $carnet->estado = 'Por Caducar';
                    $carnet->save();
                }
            }
        }
    }
}
