<?php
namespace App\Service;

// external library
use Symfony\Contracts\HttpClient\HttpClientInterface;
use DateTime;
use Psr\Log\LoggerInterface;

class WeatherService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $apiUrl;
    private string $apiLocation;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, string $apiKey, string $apiUrl, string $apiLocation, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->apiLocation = $apiLocation;
        $this->logger = $logger; 
    }

    public function getWeather(DateTime $startDate, DateTime $endDate, ?string $location = null): array
    {
        $weatherData = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            try {
                $response = $this->httpClient->request('GET', $this->apiUrl .'/forecast.json', [
                    'query' => [
                        'key' => $this->apiKey,
                        'q' => $location ?: $this->apiLocation, // Use provided location or default
                        'dt' => $currentDate->format('Y-m-d'), // Date for the forecast
                        'days' => 1, // Fetch forecast for 1 day
                    ],
                ]);

                $data = $response->toArray();

                // Extract relevant weather data
                $forecast = $data['forecast']['forecastday'][0];
                $weatherData[$currentDate->format('Y-m-d')] = [
                    'temperature' => $forecast['day']['avgtemp_c'], // Average temperature in Celsius
                    'description' => $forecast['day']['condition']['text'], // Weather description
                    'icon' => $forecast['day']['condition']['icon'], // Weather icon
                ];
            } catch (\Exception $e) {
                $this->logger->error('Failed to fetch weather data', [
                    'date' => $currentDate->format('Y-m-d'),
                    'error' => $e->getMessage(),
                ]);
                $weatherData[$currentDate->format('Y-m-d')] = [
                    'temperature' => null,
                    'description' => 'Error fetching data',
                    'icon' => null,
                ];
            }

            $currentDate->modify('+1 day');
        }

        return $weatherData;
    }
}