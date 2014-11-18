<?php

namespace AppBundle\Business;


use GuzzleHttp\Client;

/**
* Class to access TVDB APIs services
*/
class ServiceTvdb
{
    function __construct($server, $api_key, $language='en')
    {
        $this->language = $language;
        $this->apiKey   = $api_key;
        $this->language = $language;
        $this->client = new Client( array('base_url' => $server, 'defaults'=>array('exceptions' => false) ) );
    }   
    


    public function getSerieByName($name)
    {
        $xml = $this->getSimpleXmlResponse('GetSeries.php?seriesname='.urlencode($name).'&language='.$this->language);
        $series = $xml->xpath('//Series');
        
        if(!empty($series))
            return $series[0];
        
        return false;
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
    
    public function getRandomBanner($id)
    {
        $xml = getSerieBannersById($id);
        if($xml){
            //TODO faire un random sur le count du XML
        }
        
        return true; // retourner l'url (full ?) de la banner
    }

    private function getSimpleXmlResponse($url)
    {
        $response = $this->client->get('/api/'.$url);
   
        if ($response->getStatusCode() != 200)
            return false;

         return $response->xml();
    }
    
    
}