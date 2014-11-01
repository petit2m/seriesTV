<?php

namespace AppBundle\Business;


use Buzz\Browser;

/**
* Class to access TVDB APIs services
*/
class ServiceTvdb
{
    
    function __construct($server, $api_key, $language='fr')
    {
        $this->client   = new Browser();
        $this->server   = $server;
        $this->apiKey   = $api_key;
        $this->language = $language;
    }

    public function getSerieByName($name)
    {
        $xml = $this->getSimpleXmlResponse('GetSeries.php?seriesname='.urlencode($name).'&language='.$this->language);
        $series = $xml->xpath('//Series');

        return $series[0];
    }

    public function getSerieById($id)
    {
        $xml = $this->getSimpleXmlResponse($this->apiKey.'/series/'.$id.'/'.$this->language.'.xml'); 
        if($xml)
            return $xml->xpath('//Series');

        return false;
    }

    public function getEpisodesBySerieId($id)
    {
        $xml = $this->getSimpleXmlResponse($this->apiKey.'/series/'.$id.'/all/'.$this->language.'.xml'); 

        return $xml->xpath('//Episode');
    }

    public function getEpisodeById($id)
    {
        return $this->getSimpleXmlResponse($this->apiKey.'/episodes/'.$id.'/'.$this->language.'.xml'); 
    }

    public function getSerieBannersById($id)
    {
        return $this->getSimpleXmlResponse($this->apiKey.'/series/'.$id.'/banners.xml');           
    }

    public function getSerieActorsById($id)
    {
        return $this->getSimpleXmlResponse($this->apiKey.'/series/'.$id.'/actors.xml');           
    }

    public function getServerTime()
    { 
        return $this->getSimpleXmlResponse('Updates.php?type=none');
    }

    public function getLastUpdates($last_update_time)
    {
        return $this->getSimpleXmlResponse('Updates.php?type=all&time='.$last_update_time);
    }

    private function getSimpleXmlResponse($url)
    {
        $response = $this->client->get($this->server.'/api/'.$url);
        
        if ($response->getStatusCode() != 200)
            return false;

         return simplexml_load_string($response->getContent());
    }
}