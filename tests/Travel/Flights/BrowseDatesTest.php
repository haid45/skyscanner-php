<?php

namespace OzdemirBurak\SkyScanner\Tests\Travel\Flights;

use OzdemirBurak\SkyScanner\Travel\Flights\BrowseDates;
use PHPUnit\Framework\TestCase;

class BrowseDatesTest extends TestCase
{
    /**
     * @group flights-browse-dates-methods
     */
    public function testParameters()
    {
        $pricing = new BrowseDates('something');
        $pricing->setParameters(['currency' => 'AUD']);
        $this->assertEquals('GB', $pricing->getParameter('country'));
        $this->assertEquals('something', $pricing->getParameter('apiKey'));
        $this->assertNull($pricing->getParameter('dummy'));
        $this->assertEquals('AUD', $pricing->getParameter('currency'));
    }

    /**
     * @group flights-browse-dates-raw-data
     */
    public function testRawDataProperties()
    {

        $dates = $this->getBrowseDates();
        $data = $dates->get();

        $status = $dates->getResponseStatus();
        $this->assertContains($status, [200, 304]);
        if ($status !== 304) {
            $this->assertNotEmpty($data);
            $properties = ['Quotes', 'Carriers', 'Places', 'Currencies', 'Dates'];
            foreach ($properties as $property) {
                $data = $dates->get($property);
                $this->assertNotEmpty($data);
            }
        }
    }

    /**
     * @group flights-browse-dates-direct-flights
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testOneWay()
    {
        $dates = $this->getBrowseDates();
        $flights = $dates->getFlights();

        $status = $dates->getResponseStatus();
        $this->assertContains($status, [200, 304]);
        if ($status !== 304) {
            $this->assertNotEmpty($flights);
        }
    }

    /**
     * @group flights-browse-dates-direct-flights
     */
    public function testRound()
    {
        $pricing = $this->getBrowseDates();
        $pricing->setParameters(['inboundPartialDate' => date('Y-m-d', strtotime('+2 week'))]);
        $flights = $pricing->getFlights();
        $status = $pricing->getResponseStatus();
        $this->assertContains($status, [200, 304]);
        if ($status !== 304) {
            $this->assertNotEmpty($flights);
        }
    }

    /**
     * @param array $parameters
     *
     * @return BrowseDates
     */
    private function getBrowseDates(array $parameters = []): BrowseDates
    {
        $dates = new BrowseDates(API_KEY_1);
        $dates->setParameters(!empty($parameters) ? $parameters : [
            'destinationPlace' => 'SYD',
            'originPlace' => 'MEL',
            'outboundPartialDate' => date('Y-m', strtotime('+1 week')),
        ]);
        return $dates;
    }
}
