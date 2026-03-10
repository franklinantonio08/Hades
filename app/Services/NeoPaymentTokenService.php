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

        DB::table('payment_tokens')->insert([
            'user_id' => Auth::id() ?? 1,
            'token' => $token,
            'account_number' => null,
            'last_four_digits' => '0000',
            'brand' => 'OAUTH_DEBUG',
            'cardholder_name' => 'OAuth Token Debug',
            'expiry_date' => now()->addHour(),
            'is_default' => 0,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

/*    public static function createCheckout(array $payload): string
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
    }*/

    public static function createCheckout(array $payload): string
    {
        $token  = self::getAccessToken();
        $config = self::config();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
            'User-Agent' => 'Laravel-HADES'
        ])->post($config['host'].'/api/v2/checkout', $payload);

        if(!$response->ok()){
            throw new \Exception("NeoPayment HTTP Error: ".$response->body());
        }

        $data = $response->json();

        if(($data['status'] ?? null) !== 'ok'){
            throw new \Exception("NeoPayment API Error: ".json_encode($data));
        }

        if(!isset($data['data']['redirect_url'])){
            throw new \Exception("No redirect_url returned: ".json_encode($data));
        }

        return $data['data']['redirect_url'];
    }
}
