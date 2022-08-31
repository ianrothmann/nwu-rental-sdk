<?php


namespace App\Classes\Api\RentalListing;

use GuzzleHttp\Client;

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

    public function suburbs()
    {
        return $this->clientCall('suburbs');
    }

    public function residences()
    {
        return $this->clientCall('residences');
    }

    public function complexes()
    {
        return $this->clientCall('complexes');
    }

    public function features()
    {
        return $this->clientCall('features');
    }

    public function featureCategories()
    {
        return $this->clientCall('featurecategories');
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
     * @param array $features ['refcode' => string, 'value'=> boolean/int/string]
     * @param array $files ['filename'=> string, 'mimetype'=> string, 'extension'=> string, 'size'=> int, 'disk'=> string, 'base_url'=> string, 'key'=> string, 'thumbnail_key'=> string]
     */
    public function updateListing(int $listing, array $parameters, array $files = null, array $features = null)
    {
        $api = 'agencies/' . $this->agencyID . '/listings/' . $listing;


        $parameters['files'] = $files;
        $parameters['features'] = $features;

        return $this->clientCall($api, 'patch', $parameters, $files);
    }

    /**
     * @param array $parameters ['listing_name'=> string, 'num_units'=> int, 'complexid'=> int, 'listingtypeid'=> int]
     * @param array $features ['refcode' => string, 'value'=> int/string(shared)]
     * @param array $files ['filename'=> string, 'mimetype'=> string, 'extension'=> string, 'size'=> int, 'disk'=> string, 'base_url'=> string, 'key'=> string, 'thumbnail_key'=> string]
     */
    public function createListing(array $parameters, array $files = null, array $features)
    {
        $api = 'agencies/' . $this->agencyID . '/listings';

        $parameters['files'] = $files;
        $parameters['features'] = $features;

        return $this->clientCall($api, 'post', $parameters, $files);
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

    protected function clientCall($api, $type = 'get', $parameters = null, $files = null)
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
            'form_params' =>  $parameters
        ]);

        return json_decode($res->getBody()->getContents());
    }
}