<?php

namespace OzdemirBurak\SkyScanner\Travel\Flights;

use OzdemirBurak\SkyScanner\TravelService;
use OzdemirBurak\SkyScanner\Traits\ImageTrait;

class LivePricing extends TravelService
{
    use ImageTrait;

    /**
     * API Endpoint
     *
     * @var string
     */
    protected $endpoint = 'pricing/v1.0/';

    /**
     * API Uri
     *
     * @var string
     */
    protected $uri = '{country}/{currency}/{locale}/{locationSchema}/{originPlace}/{destinationPlace}/{outboundDate}/{inboundDate}/{cabinClass}/{adults}/{children}/{infants}/{includeCarriers}/{excludeCarriers}/{groupPricing}?apiKey={apiKey}';

    /**
     * API Session Polling Uri
     *
     * @var string
     */
    protected $uriSession = '?sortType={sortType}&sortOrder={sortOrder}&duration={duration}&includeCarriers={includeCarriers}&excludeCarriers={excludeCarriers}&originAirports={originAirports}&destinationAirports={destinationAirports}&stops={stops}&outboundDepartTime={outboundDepartTime}&outboundDepartStartTime={outboundDepartStartTime}&outboundDepartEndTime={outboundDepartEndTime}&outboundArriveStartTime={outboundArriveStartTime}&outboundArriveEndTime={outboundArriveEndTime}&inboundDepartTime={inboundDepartTime}&inboundDepartStartTime={inboundDepartStartTime}&inboundDepartEndTime={inboundDepartEndTime}&inboundArriveStartTime={inboundArriveStartTime}&inboundArriveEndTime={inboundArriveEndTime}&apiKey={apiKey}';

    /**
     * Main data property that contains pricing information
     *
     * @var string
     */
    protected $property = 'Itineraries';

    /**
     * The number of adult passengers
     *
     * @var int
     */
    protected $adults = 1;

    /**
     * The Cabin Class
     *
     * Supported values are: Economy, PremiumEconomy, Business, First
     *
     * @var string
     */
    protected $cabinClass = 'Economy';

    /**
     * The code schema to use for carriers
     * Supported values are: Iata, Icao, Skyscanner
     *
     * @var string
     */
    protected $locationSchema = 'Iata';

    /**
     * The number of children passengers
     *
     * @var int
     */
    protected $children = 0;

    /**
     * Destination airports to filter on
     * List of airport codes delimited by ';'
     *
     * @var string
     */
    protected $destinationAirports;

    /**
     * The destination city or airport
     * Specified location schema, or Skyscanner Rnid
     *
     * @var string
     */
    protected $destinationPlace;

    /**
     * Filter for maximum duration in minutes
     * Supported values are: Between 0 and 1800
     *
     * @var int
     */
    protected $duration;

    /**
     * Filter flights by any but the specified carriers
     * Must be semicolon-separated Iata carrier codes.
     *
     * @link http://www.iata.org/publications/Pages/code-search.aspx
     * @var string
     */
    protected $excludeCarriers;

    /**
     * Flights that are parsed via API call
     *
     * @var array
     */
    protected $flights = [];

    /**
     * Show price-per-adult (false), or price for all passengers (true)
     *
     * @var bool
     */
    protected $groupPricing = false;

    /**
     * The return date
     * Formatted as YYYY-mm-dd
     *
     * @var string
     */
    protected $inboundDate;

    /**
     * Filter for start of range for inbound departure time.
     * Formatted as hh:mm
     *
     * @var string
     */
    protected $inboundArriveStartTime;

    /**
     * Filter for end of range for inbound arrival time.
     * Formatted as hh:mm
     *
     * @var string
     */
    protected $inboundArriveEndTime;

    /**
     * Filter for end of range for inbound departure time
     * Formatted as 'hh:mm'
     *
     * @var string
     */
    protected $inboundDepartEndTime;

    /**
     * Filter for start of range for inbound departure time
     * Formatted as 'hh:mm'
     *
     * @var string
     */
    protected $inboundDepartStartTime;

    /**
     * Filter for inbound departure time by time period of the day (i.e. morning, afternoon, evening)
     * List of day time period delimited by ';' (acceptable values are M, A, E)
     *
     * @var string
     */
    protected $inboundDepartTime;

    /**
     * Filter flights by the specified carriers
     * Must be semicolon-separated Iata carrier codes.
     *
     * @link http://www.iata.org/publications/Pages/code-search.aspx
     * @var string
     */
    protected $includeCarriers;

    /**
     * The number of infant passengers
     *
     * @var int
     */
    protected $infants = 0;

    /**
     ** The code schema used for locations
     * Supported values are: Iata, GeoNameCode, GeoNameId, Rnid, Sky
     *
     * @var string
     */
    protected $locationschema = 'Iata';

    /**
     * Origin airports to filter on
     * List of airport codes delimited by ';'
     *
     * @var string
     */
    protected $originAirports;

    /**
     * The origin city or airport
     * Specified location schema, or Skyscanner Rnid
     *
     * @var string
     */
    protected $originPlace;

    /**
     * The departure date
     * Formatted as YYYY-mm-dd
     *
     * @var string
     */
    protected $outboundDate;

    /**
     * Filter for start of range for outbound departure time.
     * Formatted as hh:mm
     *
     * @var string
     */
    protected $outboundArriveStartTime;

    /**
     * Filter for end of range for outbound arrival time.
     * Formatted as hh:mm
     *
     * @var string
     */
    protected $outboundArriveEndTime;

    /**
     * Filter for end of range for outbound departure time
     * Formatted as 'hh:mm'
     *
     * @var string
     */
    protected $outboundDepartEndTime;

    /**
     * Filter for start of range for outbound departure time
     *
     * Formatted as 'hh:mm'
     *
     * @var string
     */
    protected $outboundDepartStartTime;

    /**
     * Filter for outbound departure time by time period of the day (i.e. morning, afternoon, evening)
     * List of day time period delimited by ';' (acceptable values are M, A, E)
     *
     * @var string
     */
    protected $outboundDepartTime;

    /**
     * Save remote agent images to local where urls are returned from the request
     *
     * @var bool
     */
    protected $saveAgentImages = false;

    /**
     * Save remote carrier images to local where urls are returned from the request
     *
     * @var bool
     */
    protected $saveCarrierImages = false;

    /**
     * Filter for maximum number of stops
     *
     * Supported values are: 0, 1
     *
     * @var int
     */
    protected $stops;

    /**
     * The property to sort on. If specified, you must also specify sortorder
     * Supported values are: carrier, duration, outboundarrivetime, outbounddeparttime,
     * inboundarrivetime, inbounddeparttime, price
     *
     * @var string
     */
    protected $sortType = 'price';

    /**
     * Sort direction
     *
     * Supported values are: asc, desc
     *
     * @var string
     */
    protected $sortOrder = 'asc';

    /**
     * Full URL
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUrl(): string
    {
        [$id, $this->uri] = [$this->getSessionId($this->url . $this->endpoint), $this->uriSession];
        return $this->url . $this->endpoint . $id;
    }

    /**
     * Get modified data where the each agent and carrier is assigned to each itinerary
     * If you only want to get get the first one, it will remove Agents property
     * Whereas Agent property will hold the first agent within the array sorted with given sorttype property
     *
     * @param bool $onlyFirstAgentPerItinerary
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFlights($onlyFirstAgentPerItinerary = true): array
    {
        if ($this->init()) {
            $this->flights = [];
            $this->addItineraries($onlyFirstAgentPerItinerary);
            $this->beautifyFlights($onlyFirstAgentPerItinerary);
        } else {
            $this->printErrorMessage($this->getResponseMessage());
        }
        return $this->flights;
    }

    /**
     * Get array of places for mapping with station ID's
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMeta(): array
    {
        return [
            $this->saveImages($this->get('Agents'), $this->saveAgentImages),
            $this->saveImages($this->get('Carriers')),
            $this->get('Legs')
        ];
    }

    /**
     * @param $onlyFirstAgentPerItinerary
     */
    private function addItineraries($onlyFirstAgentPerItinerary): void
    {
        foreach ($this->data->Itineraries as $key => $itinerary) {
            foreach (['OutboundLegId', 'InboundLegId'] as $leg) {
                if (isset($itinerary->$leg)) {
                    $this->flights[$key][$leg] = $itinerary->$leg;
                }
            }
            foreach ($itinerary->PricingOptions as $itineraryKey => $agent) {
                $this->flights[$key]['Agents'][$itineraryKey] = $agent;
                if ($onlyFirstAgentPerItinerary) {
                    break;
                }
            }
            if (isset($itinerary->BookingDetailsLink)) {
                $this->flights[$key]['BookingDetailsLink'] = $itinerary->BookingDetailsLink;
            }
        }
    }

    /**
     * @param      $objects
     * @param bool $saveCarrierImage
     *
     * @return mixed
     */
    private function saveImages($objects, $saveCarrierImage = false)
    {
        if ($saveCarrierImage === true) {
            foreach ($objects as &$object) {
                $object->ImageUrl = $this->saveImage($object->ImageUrl, $this->savePath);
            }
        }
        return $objects;
    }

    /**
     * Assign flight specific agents, carriers and legs to the each
     *
     * @param bool $onlyFirstAgentPerItinerary
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function beautifyFlights($onlyFirstAgentPerItinerary): void
    {
        [$agents, $carriers, $legs] = $this->getMeta();
        foreach ($this->flights as &$flight) {
            // Find and assign each agent by ID
            foreach ($flight['Agents'] as $key => &$flightAgent) {
                $agent = $agents[$this->arraySearch($flightAgent->Agents[0], $agents, 'Id')];
                foreach ($agent as $property => $propertyValue) {
                    $flightAgent->$property = $propertyValue;
                }
                unset($flight['Agents'][$key]->Agents);
            }
            // Find and assign outbound and inbound legs
            foreach (['OutboundLeg' => 'OutboundLegId', 'InboundLeg' => 'InboundLegId'] as $key => $search) {
                if (isset($flight[$search])) {
                    $legId = $this->arraySearch($flight[$search], $legs, 'Id');
                    foreach ($legs[$legId]->FlightNumbers as $order => $legInformation) {
                        $carrierId = $this->arraySearch($legInformation->CarrierId, $carriers, 'Id');
                        $flightNumber = $legs[$legId]->FlightNumbers[$order]->FlightNumber;
                        $flightCode = $carriers[$carrierId]->Code . $flightNumber;
                        $legs[$legId]->FlightNumbers[$order]->FlightCode = $flightCode;
                        $legs[$legId]->FlightNumbers[$order]->Carrier = $carriers[$carrierId];
                    }
                    $flight[$key] = $legs[$legId];
                    if ($this->removeIds === true) {
                        unset($flight[$search]);
                    }
                }
            }
            $flight['Agent'] = $flight['Agents'][0];
            if ($onlyFirstAgentPerItinerary === true) {
                unset($flight['Agents']);
            }
        }
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
}
