<?php


namespace IanRothmann\NWURentalSDK;

use GuzzleHttp\Client;
use http\Env\Response;
use Illuminate\Support\Facades\File;

class RentalListingClass
{
    private $apiToken = null, $agencyID = null, $url = null;

    protected $client = null;

    public function __construct($url, $token, $agencyID)
    {
        $this->url = $url;
        $this->apiToken = $token;
        $this->agencyID = $agencyID;

        $this->client = new Client();
    }

    public function cities()
    {
        return $this->clientCall('cities');
    }

    /**
     * @param String $name
     * @param array $location ['number'=> int, 'street_name'=> string, 'gps_lat'=> float, 'gps_lng'=> float]
     */
    public function createCity(string $name, array $location)
    {
        $parameters = [
            'city_name' => $name,
            'location' => $location
        ];

        return $this->clientCall('city', 'post', $parameters);
    }

    public function suburbs()
    {
        return $this->clientCall('suburbs');
    }

    /**
     * @param array $parameters ['suburb_name' => string, 'cityid'=> int]
     */
    public function createSuburb(array $parameters)
    {
        return $this->clientCall('suburb', 'post', $parameters);
    }

    public function residences()
    {
        return $this->clientCall('residences');
    }

    /**
     * @param array $parameters ['residence_name' => string,'gender_only'=> f/m/all, 'cityid'=> int]
     * @param array $file ['url'=> string, 'file_name'=> string, 'extension'=> string]
     */
    public function createResidence(array $parameters, array $file)
    {
        $parameters['file'] = $file;

        return $this->clientCall('residence', 'post', $parameters);
    }

    public function complexes()
    {
        return $this->clientCall('complexes');
    }

    /**
     * @param array $parameters ['complex_name'=> string, 'residenceid'=> int]
     * @param array $location ['number'=> int, 'street_name'=> string, 'gps_lat'=> float, 'gps_lng'=> float, 'cityid'=> int]
     */
    public function createComplex(array $parameters, array $location)
    {
        $parameters['location'] = $location;

        return $this->clientCall('complex', 'post', $parameters);
    }

    public function features()
    {
        return $this->clientCall('features');
    }

    public function featureCategories()
    {
        return $this->clientCall('featurecategories');
    }

    public function listingTypes(){
        return $this->clientCall('listingtypes');
    }

    public function listings($deployed = null)
    {
        return $this->clientCall('agencies/' . $this->agencyID . '/listings', 'get', ['deployed' => $deployed]);
    }

    public function listing(int $listingID)
    {
        $api = 'agencies/' . $this->agencyID . '/listings/' . $listingID;

        return $this->clientCall($api);
    }

    public function adverts(int $listingID)
    {
        $api = 'agencies/' . $this->agencyID . '/listings/' . $listingID . '/adverts';

        return $this->clientCall($api);
    }

    public function advert(int $listingID, int $advertID)
    {
        $api = 'agencies/' . $this->agencyID . '/listings/' . $listingID . '/adverts/' . $advertID;

        return $this->clientCall($api);
    }

    /**
     * @param array $parameters ['listing_name'=> string, 'num_units'=> int, 'complexid'=> int, 'listingtypeid'=> int]
     * @param array $features ['refcode' => string, 'value'=> int/boolean/boolean, 'type' => number/shared/boolean]
     * @param array $files ['filename'=> string, 'mimetype'=> string, 'extension'=> string, 'size'=> int, 'disk'=> string, 'base_url'=> string, 'key'=> string, 'thumbnail_key'=> string]
     */
    public function updateListing(int $listing, array $parameters, array $files = null, array $features = null)
    {
        $api = 'agencies/' . $this->agencyID . '/listings/' . $listing;


        $parameters['files'] = $files;
        $parameters['features'] = $features;

        return $this->clientCall($api, 'patch', $parameters);
    }

    /**
     * @param array $parameters ['listing_name'=> string, 'num_units'=> int, 'complexid'=> int, 'listingtypeid'=> int]
     * @param array $features ['refcode' => string, 'value'=> int/boolean/boolean, 'type' => number/shared/boolean]
     * @param array $files ['filename'=> string, 'mimetype'=> string, 'extension'=> string, 'size'=> int, 'disk'=> string, 'base_url'=> string, 'key'=> string, 'thumbnail_key'=> string]
     */
    public function createListing(array $parameters, array $files = null, array $features = null)
    {
        $api = 'agencies/' . $this->agencyID . '/listings';

        $parameters['files'] = $files;
        $parameters['features'] = $features;

        return $this->clientCall($api, 'post', $parameters);
    }

    /**
     * @param array $parameters ['starts_at'=> date, 'ends_at'=> date, 'available_at'=> date, 'price_type'=> 'fixed/range', 'month_price'=> float, 'month_price_from'=> float, 'month_price_to'=> float, 'deposit_price'=> float, 'contract_duration_months'=> int, 'number_available'=> int]
     */
    public function updateAdvert(int $listingID, int $advertID, array $parameters)
    {
        $api = 'agencies/' . $this->agencyID . '/listings/' . $listingID . '/adverts/' . $advertID;

        return $this->clientCall($api, 'patch', $parameters);
    }

    /**
     * @param array $parameters ['starts_at'=> date, 'ends_at'=> date, 'available_at'=> date, 'price_type'=> 'fixed/range', 'month_price'=> float, 'month_price_from'=> float, 'month_price_to'=> float, 'deposit_price'=> float, 'contract_duration_months'=> int, 'number_available'=> int]
     */
    public function createAdvert(int $listingID, array $parameters)
    {
        $api = 'agencies/' . $this->agencyID . '/listings/' . $listingID . '/adverts';

        return $this->clientCall($api, 'post', $parameters);
    }

    /**
     *
    */
    public function interestFollowUp($listingID, $advertID, $interestID){
        $api = 'agencies/' . $this->agencyID . '/listings/' . $listingID . '/adverts/' . $advertID . '/interests/' . $interestID . '/followup';

        return $this->clientCall($api, 'post');
    }

    /**
     *
     */
    public function interestDeleted($listingID, $advertID, $interestID){
        $api = 'agencies/' . $this->agencyID . '/listings/' . $listingID . '/adverts/' . $advertID . '/interests/' . $interestID . '/deleted';

        return $this->clientCall($api, 'post');
    }

    /**
     * @param array $parameters ['amenity_name'=> string, 'type'=> general/uni]
     * @param array $location ['number'=> int, 'street_name'=> string, 'gps_lat'=> float, 'gps_lng'=> float, 'cityid'=> int]
     * @param array $file ['filename'=> string, 'mimetype'=> string, 'extension'=> string, 'size'=> int, 'disk'=> string, 'base_url'=> string, 'key'=> string, 'thumbnail_key'=> string]
     */
    public function createPOI(array $parameters, array $location, array $file){
        $api = 'poi';
        $parameters['file'] = $file;
        $parameters['location'] = $location;

        return $this->clientCall($api, 'post', $parameters);
    }

    protected function clientCall($api, $type = 'get', $parameters = null)
    {
        $url = $this->url . '/api/' . $api;
        $deployed = null;

        if (isset($parameters['deployed'])) {
            $deployed = $parameters['deployed'];
            unset($parameters['deployed']);
        }

        $res = $this->client->request($type, $url, [
            'headers' => [
                'Authorization' => "Bearer " . $this->apiToken,
            ],
            'query' => [
                'deployed' => $deployed
            ],
            'form_params' => $parameters
        ]);

        return json_decode($res->getBody()->getContents());
    }
}