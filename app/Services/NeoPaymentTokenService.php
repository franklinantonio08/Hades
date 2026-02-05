<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\ConfigPayment;

class NeoPaymentTokenService {

    protected static function getAccessToken(): string {
        
        $config = self::config();

        // clave por ambiente/cliente/host para evitar mezclar tokens
        $cacheKey = 'neopayment_access_token:' . md5($config['host'].'|'.$config['client_id']);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::asForm()->post(
            $config['host'] . '/oauth/token',
            [
                'grant_type'    => $config['grant_type'],
                'client_id'     => $config['client_id'],
                'client_secret' => $config['client_secret'],
            ]
        );

        if (!$response->ok()) {
            throw new \Exception('NeoPayment OAuth error: ' . $response->body());
        }

        $token     = $response->json('access_token');
        $expiresIn = (int) ($response->json('expires_in') ?? 3600);

        // guarda el token un poco menos que su expiración real
        Cache::put($cacheKey, $token, max(60, $expiresIn - 120));

        return $token;
    }


    protected static function config(): array {

        $rows = ConfigPayment::where('estatus', 'Activo')
            ->pluck('descripcion', 'tipo');

        return [
            'host'          => rtrim($rows['host'], '/'),
            'grant_type'    => $rows['grant_type'],
            'client_id'     => $rows['client_id'],
            'client_secret' => $rows['client_secret'],
        ];
    }

    public static function createCheckout(array $payload): string
    {
        $token  = self::getAccessToken();
        $config = self::config();

        $response = Http::withToken($token)
            ->post($config['host'].'/api/v2/checkout', $payload)
            ->json();

        if (!isset($response['data']['redirect_url'])) {
            throw new \Exception('NeoPayment checkout error: '.json_encode($response));
        }

        return $response['data']['redirect_url'];
    }
}
