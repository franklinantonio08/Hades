<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RIDMigrantes;
use App\Models\RIDProcesslogs;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMigrateData extends Command
{
    protected $signature = 'migrante:send';
    protected $description = 'Send migrante data to the external API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('Starting migrante:send command');

        // Authenticate and get token
        $token = $this->authenticate();

        if ($token) {
            $this->info('Authenticated successfully');
            Log::info('Authenticated successfully');

            // Obtén los registros con estado 'Pendiente'
            $migrantes = RIDMigrantes::where('estatus', RIDMigrantes::STATUS_PENDIENTE)->get();

            foreach ($migrantes as $migrante) {
                try {
                    $response = Http::withToken($token)->post('http://172.20.10.47:8000/api/denuncias/migrateData', [
                        'nombre' => $migrante->nombre,
                        'apellido' => $migrante->apellido,
                        'fechaNacimiento' => $migrante->fechaNacimiento,
                        'codigo' => $migrante->codigo,
                        'documento' => $migrante->documento,
                        'regionId' => $migrante->regionId,
                        'paisId' => $migrante->paisId,
                        'nacionalidadId' => $migrante->nacionalidadId,
                        'genero' => $migrante->genero,
                        'tipo' => $migrante->tipo,
                        'puestoId' => $migrante->puestoId,
                        'afinidadId' => $migrante->afinidadId,
                        'infoextra' => $migrante->infoextra,
                        'estatus' => $migrante->estatus,
                        'usuarioId' => $migrante->usuarioId,
                    ]);

                    if ($response->successful()) {
                        // Si la respuesta es exitosa, cambia el estado a 'Enviado'
                        $migrante->estatus = RIDMigrantes::STATUS_ENVIADO;
                        $this->info('Data sent successfully for migrante ID: ' . $migrante->id);
                        Log::info('Data sent successfully for migrante ID: ' . $migrante->id);
                    } else {
                        // Si la respuesta falla, cambia el estado a 'Fallido'
                        $migrante->estatus = RIDMigrantes::STATUS_FALLIDO;
                        $this->logError('Failed to send data', 'migrante:send', $response->body());
                        Log::error('Failed to send data for migrante ID: ' . $migrante->id);
                    }
                } catch (\Exception $e) {
                    // En caso de excepción, cambia el estado a 'Fallido'
                    $migrante->estatus = RIDMigrantes::STATUS_FALLIDO;
                    $this->logError($e->getMessage(), 'migrante:send', $e->getTraceAsString());
                    Log::error('Exception occurred for migrante ID: ' . $migrante->id . ' - ' . $e->getMessage());
                }

                // Guarda los cambios en la base de datos
                $migrante->save();
            }
        } else {
            $this->logError('Authentication failed', 'migrante:send');
            Log::error('Authentication failed');
        }

        return 0;
    }

    private function authenticate()
    {
        try {
            $response = Http::post('http://172.20.10.47:8000/api/login', [
                'username' => 'antonio08',
                'password' => '123456',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? null;
            } else {
                $this->logError('Failed to authenticate', 'authenticate', $response->body());
                Log::error('Failed to authenticate');
                return null;
            }
        } catch (\Exception $e) {
            $this->logError($e->getMessage(), 'authenticate', $e->getTraceAsString());
            Log::error('Exception occurred during authentication - ' . $e->getMessage());
            return null;
        }
    }

    private function logError($message, $processName, $extraInfo = null)
    {
        RIDProcesslogs::create([
            'log_status' => 'Error',
            'message' => $message,
            'process_name' => $processName,
            'extra_info' => $extraInfo,
        ]);

        $this->error($message);
    }
}
