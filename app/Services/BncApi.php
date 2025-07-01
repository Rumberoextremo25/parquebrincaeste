<?php

namespace App\Services;

use App\Models\ApiBnc;
use App\Models\ExchangeRate; // Importa el nuevo modelo
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BncApi
{
    private ?string $token = null;
    private ?string $refreshToken = null;
    private ?string $defaultToken = null;
    private ?Carbon $tokenExpiresAt = null;

    public function __construct()
    {
        $apiBnc = ApiBnc::find(1);

        if ($apiBnc) {
            $this->token = $apiBnc->token;
            $this->refreshToken = $apiBnc->refresh_token;
            $this->defaultToken = $apiBnc->default_token;
            $this->tokenExpiresAt = $apiBnc->expires_at ? Carbon::parse($apiBnc->expires_at) : null;
        }
    }

    /**
     * Get the current valid access token. Performs logon or refresh if necessary.
     * @return string The valid access token.
     * @throws \Exception If unable to obtain or refresh token.
     */
    public function getAccessToken(): string
    {
        if (!$this->token || $this->isTokenExpired()) {
            Log::info('Bancaribe token is missing or expired. Attempting to refresh or log on.');
            try {
                if ($this->refreshToken) {
                    $this->refreshToken();
                } else {
                    $this->logon();
                }
            } catch (\Exception $e) {
                Log::error('Failed to obtain or refresh Bancaribe token: ' . $e->getMessage());
                throw new \Exception('Failed to connect to Bancaribe API: ' . $e->getMessage());
            }
        }

        return $this->token;
    }

    /**
     * Perform the initial logon to obtain access and refresh tokens.
     * This method should be called when no tokens are available or refresh fails.
     * @return $this
     * @throws \Exception If logon fails.
     */
    public function logon(): static
    {
        Log::info('Attempting Bancaribe API initial logon.');
        $options = ['verify' => false];

        if (env('APP_ENV') === 'local') {
            $options['proxy'] = 'socks5://localhost:8081';
        }

        $response = Http::withOptions($options)
            ->acceptJson()
            ->post(env('BNC_API_TOKEN_URL'), [
                'grant_type' => 'client_credentials',
                'client_id' => env('BNC_API_CLIENT_ID'),
                'client_secret' => env('BNC_API_CLIENT_SECRET'),
            ]);

        if ($response->successful()) {
            $data = $response->json();

            $this->token = $data['access_token'] ?? null;
            $this->refreshToken = $data['refresh_token'] ?? null;
            $expiresInSeconds = $data['expires_in'] ?? 3600;
            $this->tokenExpiresAt = Carbon::now()->addSeconds($expiresInSeconds);

            ApiBnc::updateOrCreate(
                ['id' => 1],
                [
                    'token' => $this->token,
                    'refresh_token' => $this->refreshToken,
                    'expires_at' => $this->tokenExpiresAt,
                ]
            );
            Log::info('Bcn API logon successful. Token obtained.');
        } else {
            Log::error('Bnc API logon failed: ' . $response->body());
            throw new \Exception('Unable to log on to Bancaribe API: ' . $response->body());
        }

        return $this;
    }

    /**
     * Refresh the access token using the refresh token.
     * @return $this
     * @throws \Exception If token refresh fails.
     */
    public function refreshToken(): static
    {
        if (!$this->refreshToken) {
            Log::warning('No refresh token available. Attempting initial logon instead.');
            return $this->logon();
        }

        Log::info('Attempting Bancaribe API token refresh.');
        $options = ['verify' => false];

        if (env('APP_ENV') === 'local') {
            $options['proxy'] = 'socks5://localhost:8081';
        }

        $response = Http::withOptions($options)
            ->acceptJson()
            ->post(env('BNC_API_TOKEN_URL'), [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshToken,
                'client_id' => env('BNC_API_CLIENT_ID'),
                'client_secret' => env('BNC_API_CLIENT_SECRET'),
            ]);

        if ($response->successful()) {
            $data = $response->json();

            $this->token = $data['access_token'] ?? null;
            $this->refreshToken = $data['refresh_token'] ?? $this->refreshToken;
            $expiresInSeconds = $data['expires_in'] ?? 3600;
            $this->tokenExpiresAt = Carbon::now()->addSeconds($expiresInSeconds);

            ApiBnc::updateOrCreate(
                ['id' => 1],
                [
                    'token' => $this->token,
                    'refresh_token' => $this->refreshToken,
                    'expires_at' => $this->tokenExpiresAt,
                ]
            );
            Log::info('Bnc API token refreshed successfully.');
        } else {
            Log::error('Bnc API token refresh failed: ' . $response->body());
            return $this->logon();
        }

        return $this;
    }

    /**
     * Check if the current access token is expired.
     * @return bool
     */
    protected function isTokenExpired(): bool
    {
        return !$this->token || !$this->tokenExpiresAt || $this->tokenExpiresAt->subSeconds(60)->isPast();
    }

    /**
     * Make an authenticated HTTP request, handling token refresh if needed.
     * @param string $method HTTP method (get, post, put, delete)
     * @param string $url The full URL for the request.
     * @param array $data Data to send with the request (for POST/PUT).
     * @param bool $retry If true, will retry once after token refresh.
     * @return \Illuminate\Http\Client\Response
     * @throws \Exception If request fails after retry.
     */
    private function authenticatedRequest(string $method, string $url, array $data = [], bool $retry = true): \Illuminate\Http\Client\Response
    {
        $options = ['verify' => false];
        if (env('APP_ENV') === 'local') {
            $options['proxy'] = 'socks5://localhost:8081';
        }

        $token = $this->getAccessToken();

        $response = Http::withOptions($options)
            ->acceptJson()
            ->withToken($token)
            ->{$method}($url, $data);

        if ($response->status() === 401 && $retry) {
            Log::warning('Bnc API returned 401 Unauthorized. Attempting token refresh and retry.');
            $this->refreshToken();
            return $this->authenticatedRequest($method, $url, $data, false);
        }

        if (!$response->successful()) {
            Log::error("Bnc API request failed ({$method} {$url}): " . $response->body());
            throw new \Exception("Bnc API request failed: " . $response->body());
        }

        return $response;
    }

    /**
     * Get a list of banks from Bancaribe API.
     * @return array
     * @throws \Exception
     */
    public function getBanks(): array
    {
        $response = $this->authenticatedRequest('get', env('BNC_API_BANCOS_URL') . 'listarBancosApi');
        return $response->json()['data'] ?? [];
    }

    /**
     * Query a specific mobile payment (B2P).
     * @param string $phone
     * @param string $bank
     * @param string $date
     * @return array
     * @throws \Exception
     */
    public function findPaymentMobile(string $phone, string $bank, string $date): array
    {
        $response = $this->authenticatedRequest('post', env('BNC_API_OPERACIONES_URL') . 'queryPaymentB2P', [
            'Phone' => $phone,
            'Bank' => $bank,
            'Date' => $date
        ]);
        return $response->json()['lista'] ?? [];
    }

    /**
     * Query mobile payment history (B2P) for a given date.
     * @param string $date
     * @return array
     * @throws \Exception
     */
    public function findPaymentMobileHistory(string $date): array
    {
        $response = $this->authenticatedRequest('post', env('BNC_API_OPERACIONES_URL') . 'queryPaymentB2P', [
            'Date' => $date
        ]);
        return $response->json()['lista'] ?? [];
    }
    public function processC2PPayment(float $amount, string $recipientPhone, string $recipientIdType, string $recipientIdNumber, string $concept): array
    {
        Log::info('Attempting C2P mobile payment.', ['amount' => $amount, 'phone' => $recipientPhone]);
        $response = $this->authenticatedRequest('post', env('BNC_API_C2P_URL'), [
            'amount' => number_format($amount, 2, '.', ''),
            'recipient_phone' => $recipientPhone,
            'recipient_id_type' => $recipientIdType,
            'recipient_id_number' => $recipientIdNumber,
            'concept' => $concept,
        ]);

        return $response->json();
    }
    public function processVPOSPayment(float $amount, string $cardNumber, string $cardHolderName, string $expiryMonth, string $expiryYear, string $cvv, ?string $transactionReference = null): array
    {
        Log::info('Attempting VPOS card payment.', ['amount' => $amount, 'card_last_4' => substr($cardNumber, -4)]);

        $response = $this->authenticatedRequest('post', env('BNC_API_VPOS_URL'), [
            'amount' => number_format($amount, 2, '.', ''),
            'card_number' => str_replace(' ', '', $cardNumber),
            'card_holder_name' => $cardHolderName,
            'expiry_month' => $expiryMonth,
            'expiry_year' => $expiryYear,
            'cvv' => $cvv,
            'transaction_reference' => $transactionReference,
        ]);

        return $response->json();
    }

    /**
     * Obtiene y almacena la tasa de cambio del día.
     * @param string $currencyPair Par de monedas (ej. USDVES).
     * @return float La tasa de cambio del día.
     * @throws \Exception Si no se puede obtener la tasa de cambio.
     */
    public function getDailyExchangeRate(string $currencyPair = 'USDVES'): float
    {
        // Primero, intenta obtener la tasa de la base de datos para hoy.
        $today = Carbon::now()->toDateString();
        $exchangeRate = ExchangeRate::where('date', $today)
                                    ->where('currency_pair', $currencyPair)
                                    ->first();

        if ($exchangeRate) {
            Log::info("Tasa de cambio $currencyPair encontrada en DB para hoy: " . $exchangeRate->rate);
            return $exchangeRate->rate;
        }

        // Si no está en la DB, la obtenemos de la API
        Log::info("Obteniendo tasa de cambio $currencyPair de la API de Bnc.");
        $response = $this->authenticatedRequest('get', env('BNC_API_EXCHANGE_RATE_URL') . $currencyPair);

        $data = $response->json();

        // Asume que la respuesta tiene una clave 'rate' o similar.
        // ¡ADVERTENCIA!: Ajusta esta lógica según la estructura real de la respuesta de la API de Bancaribe.
        $rate = $data['rate'] ?? null; // Ejemplo: Si la API devuelve {'rate': 36.5}

        if ($rate === null || !is_numeric($rate)) {
            Log::error("Respuesta de la API de tasa de cambio inválida: " . json_encode($data));
            throw new \Exception('No se pudo obtener una tasa de cambio válida de la API.');
        }

        // Almacenar la tasa en la base de datos para uso futuro
        ExchangeRate::create([
            'currency_pair' => $currencyPair,
            'rate' => $rate,
            'date' => $today,
        ]);

        Log::info("Tasa de cambio $currencyPair obtenida y almacenada: " . $rate);
        return (float) $rate;
    }
}