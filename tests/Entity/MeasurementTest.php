<?php

namespace App\Tests\Entity;

use App\Entity\Measurement;
use PHPUnit\Framework\TestCase;

class MeasurementTest extends TestCase
{
    /**
     * @dataProvider dataGetFahrenheit
     */
    public function testGetFahrenheit($celsius, $expectedFahrenheit): void
    {
        $measurement = new Measurement();
        $measurement->setCelsius($celsius);
        $this->assertEquals($expectedFahrenheit, $measurement->getFahrenheit(), "{$celsius}°C powinno być równe {$expectedFahrenheit}°F");
    }

    public function dataGetFahrenheit(): array
    {
        return [
            [0, 32],
            [-100, -148],
            [100, 212],
            [37, 98.6],
            [25, 77],
            [0.5, 32.9],
            [-0.5, 31.1],
            [20.3, 68.54],
            [-40, -40],
            [100.5, 212.9],
        ];
    }
}
