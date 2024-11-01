<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use App\Entity\Measurement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WeatherApiController extends AbstractController
{
    private $locationRepository;
    private $weatherUtil;

    public function __construct(LocationRepository $locationRepository, WeatherUtil $weatherUtil)
    {
        $this->locationRepository = $locationRepository;
        $this->weatherUtil = $weatherUtil;
    }

    #[Route('/api/v1/weather', methods: ['GET'])]
    public function getWeather(Request $request): Response
    {
        $country = $request->query->get('country');
        $city = $request->query->get('city');
        $format = $request->query->get('format', 'json');
        $twig = $request->query->get('twig', false);

        $location = $this->locationRepository->findOneBy(['country' => $country, 'city' => $city]);

        if (!$location) {
            throw $this->createNotFoundException('Location not found');
        }

        $measurements = $this->weatherUtil->getWeatherForLocation($location);

        if ($format === 'csv') {
            if ($twig) {
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            }

            $csvData = "city,country,date,celsius,fahrenheit\n";
            foreach ($measurements as $measurement) {
                $csvData .= sprintf(
                    "%s,%s,%s,%s,%s\n",
                    $city,
                    $country,
                    $measurement->getDate()->format('Y-m-d'),
                    $measurement->getCelsius(),
                    $measurement->getFahrenheit()
                );
            }

            return new Response($csvData, 200, [
                'Content-Type' => 'text/csv',
            ]);
        }

        if ($twig) {
            return $this->render('weather_api/index.json.twig', [
                'city' => $city,
                'country' => $country,
                'measurements' => $measurements,
            ]);
        }

        return $this->json([
            'city' => $city,
            'country' => $country,
            'measurements' => array_map(fn(Measurement $m) => [
                'date' => $m->getDate()->format('Y-m-d'),
                'celsius' => $m->getCelsius(),
                'fahrenheit' => $m->getFahrenheit(),
            ], $measurements),
        ]);
    }
}
