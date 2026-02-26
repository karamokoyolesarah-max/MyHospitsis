<?php

namespace App\Services;

class GeolocationService
{
    /**
     * Geocode une adresse en utilisant Nominatim (OpenStreetMap)
     * API gratuite et sans limite
     */
    public function geocodeAddress(string $address): ?array
    {
        try {
            // Construction de l'URL pour Nominatim
            $encodedAddress = urlencode($address);
            $url = "https://nominatim.openstreetmap.org/search?q={$encodedAddress}&format=json&limit=1&addressdetails=1";
            
            // Configuration du user agent (requis par Nominatim)
            $options = [
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: HospitSIS-Application\r\n"
                ]
            ];
            
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
            
            if ($response === false) {
                \Log::error('Geocoding API request failed for address: ' . $address);
                return null;
            }
            
            $data = json_decode($response, true);
            
            if (empty($data)) {
                \Log::warning('No geocoding results for address: ' . $address);
                return null;
            }
            
            $result = $data[0];
            
            return [
                'latitude' => (float) $result['lat'],
                'longitude' => (float) $result['lon'],
                'formatted_address' => $result['display_name'] ?? $address,
                'city' => $result['address']['city'] ?? $result['address']['town'] ?? null,
                'country' => $result['address']['country'] ?? null
            ];
            
        } catch (\Exception $e) {
            \Log::error('Geocoding error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Calcule la distance entre deux points en utilisant la formule de Haversine
     * Retourne la distance en kilometres
     */
    public function calculateDistance(
        float $lat1, 
        float $lon1, 
        float $lat2, 
        float $lon2
    ): float {
        $earthRadius = 6371; // Rayon de la Terre en km
        
        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);
        
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDiff / 2) * sin($lonDiff / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $distance = $earthRadius * $c;
        
        return round($distance, 2);
    }
    
    /**
     * Calcule les frais de deplacement selon le type de tarification du medecin
     */
    public function calculateTravelFee($doctor, float $distanceKm): array
    {
        $baseFee = $doctor->base_travel_fee ?? 5000;
        $feePerKm = $doctor->travel_fee_per_km ?? 500;
        $feeType = $doctor->travel_fee_type ?? 'combined';
        
        $calculatedFee = 0;
        
        switch ($feeType) {
            case 'fixed':
                $calculatedFee = $baseFee;
                break;
                
            case 'per_km':
                $calculatedFee = $distanceKm * $feePerKm;
                break;
                
            case 'combined':
            default:
                $calculatedFee = $baseFee + ($distanceKm * $feePerKm);
                break;
        }
        
        return [
            'distance_km' => $distanceKm,
            'base_fee' => $baseFee,
            'fee_per_km' => $feePerKm,
            'total_travel_fee' => round($calculatedFee, 2),
            'fee_type' => $feeType
        ];
    }
    
    /**
     * Verifie si un medecin peut se deplacer jusqu'a une adresse
     */
    public function isWithinRange($doctor, float $distanceKm): bool
    {
        $maxDistance = $doctor->max_travel_distance ?? 30;
        return $distanceKm <= $maxDistance;
    }
}
