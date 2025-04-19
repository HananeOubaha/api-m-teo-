<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    private $apiKey = '487070eecc9a24ade4da33b9467bee69';
    private $baseUrl = 'https://api.openweathermap.org/data/2.5/';

    public function index()
    {
        return view('weather');
    }

    public function getCurrentWeather(Request $request)
    {
        try {
            $city = $request->query('city');
            
            if (empty($city)) {
                return response()->json(['error' => 'Veuillez spécifier une ville'], 400);
            }
            
            Log::info('Tentative de récupération de la météo pour: ' . $city);
            
            $url = $this->baseUrl . 'weather';
            $params = [
                'q' => $city,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'lang' => 'fr'
            ];
            
            Log::info('URL de requête: ' . $url);
            Log::info('Paramètres: ' . json_encode($params));
            
            $response = Http::get($url, $params);
            
            Log::info('Statut de la réponse: ' . $response->status());
            Log::info('Corps de la réponse: ' . $response->body());

            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            // Vérifier si l'erreur est due à une ville non trouvée
            if ($response->status() === 404) {
                return response()->json(['error' => 'Ville non trouvée. Veuillez vérifier l\'orthographe.'], 404);
            }
            
            // Autres erreurs
            $errorMessage = 'Erreur lors de la récupération des données météo';
            if ($response->status() === 401) {
                $errorMessage = 'Clé API invalide ou non activée. Veuillez vérifier votre clé API.';
            }
            
            return response()->json(['error' => $errorMessage], $response->status());
        } catch (\Exception $e) {
            Log::error('Exception lors de la récupération de la météo: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    public function getForecast(Request $request)
    {
        try {
            $city = $request->query('city');
            
            if (empty($city)) {
                return response()->json(['error' => 'Veuillez spécifier une ville'], 400);
            }
            
            Log::info('Tentative de récupération des prévisions pour: ' . $city);
            
            $url = $this->baseUrl . 'forecast';
            $params = [
                'q' => $city,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'lang' => 'fr'
            ];
            
            Log::info('URL de requête prévisions: ' . $url);
            Log::info('Paramètres prévisions: ' . json_encode($params));
            
            $response = Http::get($url, $params);
            
            Log::info('Statut de la réponse prévisions: ' . $response->status());
            Log::info('Corps de la réponse prévisions: ' . $response->body());

            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            // Vérifier si l'erreur est due à une ville non trouvée
            if ($response->status() === 404) {
                return response()->json(['error' => 'Ville non trouvée. Veuillez vérifier l\'orthographe.'], 404);
            }
            
            // Autres erreurs
            $errorMessage = 'Erreur lors de la récupération des prévisions';
            if ($response->status() === 401) {
                $errorMessage = 'Clé API invalide ou non activée. Veuillez vérifier votre clé API.';
            }
            
            return response()->json(['error' => $errorMessage], $response->status());
        } catch (\Exception $e) {
            Log::error('Exception lors de la récupération des prévisions: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }
}
