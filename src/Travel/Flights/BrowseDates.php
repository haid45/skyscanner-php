<?php

namespace OzdemirBurak\SkyScanner\Travel\Flights;

use GuzzleHttp\Exception\GuzzleException;
use OzdemirBurak\SkyScanner\TravelService;

class BrowseDates extends TravelService
{
    /**
     * API Endpoint
     *
     * @var string
     */
    protected $endpoint = 'browsedates/v1.0/';

    /**
     * API Uri
     *
     * @var string
     */
    protected $uri = '{country}/{currency}/{locale}/{originPlace}/{destinationPlace}/{outboundPartialDate}/{inboundPartialDate}?apiKey={apiKey}';

    /**
     * API Session Polling Uri
     *
     * @var string
     */
    protected string $uriSession = '';

    /**
     * Main data property that contains pricing information
     *
     * @var string
     */
    protected $property = 'Quotes';

    /**
     * The destination city or airport
     * Specified location schema, or Skyscanner Rnid
     *
     * @var string
     */
    protected string $destinationPlace;

    /**
     * Flights that are parsed via API call
     *
     * @var array
     */
    protected array $flights = [];

    /**
     * The return partial date
     * Formatted as YYYY-mm
     * Blank for one-way flights
     *
     * @var string
     */
    protected string $inboundPartialDate = '';

    /**
     * The origin city or airport
     * Specified location schema, or Skyscanner Rnid
     *
     * @var string
     */
    protected string $originPlace;

    /**
     * The departure partial date
     * Formatted as YYYY-mm
     *
     * @var string
     */
    protected string $outboundPartialDate;

    /**
     * Full URL
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url . $this->endpoint;
    }

    /**
     * Get modified data where the each agent and carrier is assigned to each itinerary
     * If you only want to get get the first one, it will remove Agents property
     * Whereas Agent property will hold the first agent within the array sorted with given sorttype property
     *
     * @param bool $onlyFirstAgentPerItinerary
     *
     * @return array
     * @throws GuzzleException
     */
    public function getFlights(): array
    {
        if ($this->init()) {
            $this->flights = [];
            $this->addQuotes();
        } else {
            $this->printErrorMessage($this->getResponseMessage());
        }
        return $this->flights;
    }

    /**
     * Get array of places for mapping with station ID's
     *
     * @return array
     * @throws GuzzleException
     */
    public function getPlaces(): array
    {
        $places = [];
        if ($this->init()) {
            $places = $this->data->Places;
        } else {
            $this->printErrorMessage($this->getResponseMessage());
        }
        return $places;
    }

    /**
     * @throws GuzzleException
     */
    public function getQuote($quoteId): array
    {
        $quote = [];
        if ($this->init()) {
            $quotes = $this->data->Quotes;
            $key = array_search($quoteId, array_column($quotes, 'QuoteId'));

            if($key !== false) {
                $quote = (array)$quotes[$key];

                foreach (['OutboundLeg', 'InboundLeg'] as $leg) {
                    if (isset($quote[$leg])) {
                        $quote[$leg]->Origin = $this->getStationWithId($quote[$leg]->OriginId);
                        $quote[$leg]->Destination = $this->getStationWithId($quote[$leg]->DestinationId);
                        foreach ($quote[$leg]->CarrierIds as $carrierId){
                            $quote[$leg]->carriers[] = $this->getCarrierWithId($carrierId);
                        }
                    }
                }
            }

        } else {
            $this->printErrorMessage($this->getResponseMessage());
        }
        return $quote;
    }

    /**
     *
     * @throws GuzzleException
     */
    private function addQuotes(): void
    {
        $dates = $this->data->Dates;
        foreach (['OutboundDates', 'InboundDates'] as $leg) {
            if (isset($dates->$leg)) {
               foreach ($dates->$leg as $key => $segment){
                   foreach ($segment->QuoteIds as $quoteId){
                       $dates->$leg[$key]->quotes[] = $this->getQuote($quoteId);
                   }
               }
            }
        }
        $this->flights = (array)$dates;
    }

    /**
     * @return array
     */
    protected function getDefaultParameters(): array
    {
        return array_merge(parent::getDefaultParameters(), [
            'Content-Type'    => 'application/x-www-form-urlencoded',
            'X-Forwarded-For' => $this->getIpAddress()
        ]);
    }

    /**
     * @param $id
     * @param string $type [Available options, Code | Name]
     * @return null
     */
    public function getStationWithId($id, string $type = 'IataCode')
    {
        $name = null;
        $places = $this->data->Places;
        $key = array_search($id, array_column($places, 'PlaceId'));

        if($key !== false)
            $name = $places[$key]->$type;

        return $name;
    }

    /**
     * @param $carrierId
     * @param string $type [Available options, CarrierId | Name]
     * @return null
     */
    public function getCarrierWithId($carrierId, string $type = 'Name')
    {
        $name = null;
        $carriers = $this->data->Carriers;
        $key = array_search($carrierId, array_column($carriers, 'CarrierId'));

        if($key !== false)
            $name = $carriers[$key]->$type;

        return $name;
    }
}
