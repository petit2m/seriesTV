<?php

namespace AppBundle\Business;
    
use GuzzleHttp\Client;

/**
* Gestion des flux RSS
*/
class Rss
{
    private $client;
    
    function __construct()
    {
      $this->client   = new Client();
    }
    
    public function parse($url)
    {
     
       $xml = new \SimpleXMLElement($url, LIBXML_NOCDATA, TRUE);
      
        foreach ($xml->xpath('//item') as $item) {
            if(preg_match("/(.*)S\d\dE\d\d/", $item->title,$matches) == 1);
                $filenames[$matches[0]][(string)$item->title] = array('name'   => $item->title,
                                                                      'url'    => $item->enclosure['url']);
        }

        return $filenames;
    }
}