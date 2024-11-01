<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil; // Dodaj import serwisu WeatherUtil
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WeatherController extends AbstractController
{
    #[Route('/weather/{city}', name: 'app_weather')]
    public function city(string $city, LocationRepository $locationRepository, WeatherUtil $weatherUtil): Response
    {
        $location = $locationRepository->findOneBy(['city' => $city]);

        if (!$location) {
            throw $this->createNotFoundException('Location not found');
        }

        $measurements = $weatherUtil->getWeatherForLocation($location);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}
