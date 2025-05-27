<?php

namespace App\Traits;

use App\Models\ApiBancaribe;
use Illuminate\Support\Facades\Http;

class BancaribeApi  
{  
    private string $token;  
    private string $refreshToken;  
    private string $defaultToken;  

    public function getToken(): static  
    {  
        // Obtén las credenciales de la base de datos  
        $apiBancaribe = ApiBancaribe::find(1);  

        $this->token = $apiBancaribe->token;  
        $this->refreshToken = $apiBancaribe->refresh_token;  
        $this->defaultToken = $apiBancaribe->default_token;  

        // Comprueba si el token ha expirado; si es así, intenta refrescarlo  
        if ($this->isTokenExpired(token: $this->token)) {  
            $this->refreshToken();  
        }  

        return $this;  
    }  

    public function refreshToken(): static  
    {  
        $options = ['verify' => false];  

        if (env(key: 'APP_ENV') === 'local') {  
            $options['proxy'] = 'socks5://localhost:8081';  
        }  

        $response = Http::withOptions(options: $options)  
            ->acceptJson()  
            ->post(url: env(key: 'BANCARIBE_API_TOKEN_URL'), data: [  
                'refresh_token' => $this->refreshToken,  
                'client_id' => env(key: 'BANCARIBE_API_CLIENT_ID'),  
                'client_secret' => env(key: 'BANCARIBE_API_CLIENT_SECRET'),  
            ]);  

        if ($response->status() === 200) {  
            $data = json_decode(json: $response->body(), associative: true);
            
             // Codifica el nuevo token y el refresh token a Base64  
             $encodedToken = base64_encode(string: $data['token']);  
             $encodedRefreshToken = base64_encode(string: $data['refresh_token']); // Asegúrate de que esta clave sea correcta en la respuesta  
 
             // Actualiza el registro del token en la base de datos  
             ApiBancaribe::where(column: 'id', operator: 1)->update(attributes: [  
                 'token' => $encodedToken,  
                 'refresh_token' => $encodedRefreshToken // Asegúrate de que esta clave sea correcta  
             ]);
            
            // Actualiza el registro del token en la base de datos  
            ApiBancaribe::where(column: 'id', operator: 1)->update(attributes: [  
                'token' => $data['token'],  
                'refresh_token' => $data['refresh_token'] // Asegúrate de que esta clave sea correcta en la respuesta  
            ]);  

            $this->token = $data['token'];  
            $this->refreshToken = $data['refresh_token']; // Asegúrate de que esta clave sea correcta  
        } else {  
            throw new \Exception(message: 'Unable to refresh the access token: ' . $response->body());  
        }  

        return $this;  
    }  

    protected function isTokenExpired(string $token): bool  
    {  
        // Implementar la lógica para verificar si el token ha expirado  
        // Esto puede incluir almacenar o comprobar la fecha de expiración  
        // Por simplicidad, retornamos false;
        return false; // Cambia esto según tu lógica para manejar la expiración del token  
    }  
    public function getBanks(): mixed
    {
        $options = ['verify' => false];

        if (env(key: 'APP_ENV') === 'local') $options['proxy'] = 'socks5://localhost:8081';

        $response =  Http::withOptions(options: $options)
            ->acceptJson()
            ->withToken(token: $this->token)
            ->get(url: env(key: 'BANCARIBE_API_BANCOS_URL') . 'listarBancosApi');

        if ($response->status() === 503) {
            $this->refreshToken()->getBanks();
        }

        if ($response->status() === 200) {
            return json_decode(json: $response->body(), associative: true)['data'];
        }

        return [];
    }
    public function findPaymentMobile($phone, $bank, $date): mixed
    {
        $options = ['verify' => false];

        if (env(key: 'APP_ENV') === 'local') $options['proxy'] = 'socks5://localhost:8081';

        $response =  Http::withOptions(options: $options)
            ->acceptJson()
            ->withToken(token: $this->token)
            ->post(url: env(key: 'BANCARIBE_API_OPERACIONES_URL') . 'queryPaymentB2P', data: [
                'Phone' => $phone,
                'Bank' => $bank,
                'Date' => $date
            ]);

        if ($response->status() === 503) {
            $this->refreshToken()->findPaymentMobile(phone: $phone, bank: $bank, date: $date);
        }

        if ($response->status() === 200) {
            return json_decode(json: $response->body(), associative: true)['lista'];
        }

        return [];
    }
    public function findPaymentMobileHistory($date): mixed
    {
        $options = ['verify' => false];

        if (env(key: 'APP_ENV') === 'local') $options['proxy'] = 'socks5://localhost:8081';

        $response =  Http::withOptions(options: $options)
            ->acceptJson()
            ->withToken(token: $this->token)
            ->post(url: env(key: 'BANCARIBE_API_OPERACIONES_URL') . 'queryPaymentB2P', data: [
                'Date' => $date
            ]);

        if ($response->status() === 503) {
            $this->refreshToken()->findPaymentMobileHistory(date: $date);
        }

        if ($response->status() === 200) {
            return json_decode(json: $response->body(), associative: true)['lista'];
        }

        return [];
    }
}