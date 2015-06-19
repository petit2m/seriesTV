<?php

namespace AppBundle\Business;


use GuzzleHttp\Client;
use Symfony\Component\Finder\Finder;

/**
* Class to access TVDB APIs services
*/
class ServiceTvdb
{
    
    const INFOS   = 'en.xml';
    const ACTORS = 'actors.xml';
    const BANNER = 'banners.xml';
        
    function __construct($server, $api_key, $path, $language='en')
    {
        $this->language = $language;
        $this->apiKey   = $api_key;
        $this->language = $language;
        $this->xmlPath  = $path.'/../web/xml/';
        $this->client = new Client( array('base_url' => $server, 'defaults'=>array('exceptions' => false) ) );
    }   
    
    public function getSerieByName($name)
    {
        $xml = $this->getSimpleXmlResponse('GetSeries.php?seriesname='.urlencode($name).'&language='.$this->language);
        
        if(!$xml)
            return false;
        
        $series = $xml->xpath('//Series');

        if(!empty($series))
            return $series[0];
        
        return false;
    }
    
    public function getSerieZip($id)
    {
        $serieZip = $this->client->get($this->apiKey.'/series/'.$id.'/all/'.$this->language.'.zip');
        var_dump($serieZip);
        $fp = fopen($this->xmlPath.$id.'.zip', 'w');
        fwrite($fp, $serieZip->getBody());
        fclose($fp);  
       
    }
    
    public function unzipSeries()
    {
        $finder = new Finder();
        $finder->files()->in($this->xmlPath)
                        ->name('*.zip');
        $zip = new \ZipArchive;
        foreach ($finder as $file) {
            echo $file->getRealpath();
             if ($zip->open($file->getRealpath()) === TRUE) {
                
                 $zip->extractTo(substr($file->getRealpath(),0,-4));
                 $zip->close();
                 unlink($file->getRealpath());
             }
         }
    }

    public function getSerieById($id)
    {
        $xml = $this->loadXml($id);
        if($xml)
            return $xml->xpath('//Series');

        return false;
    }

    public function getEpisodesBySerieId($id)
    {
        $xml = $this->loadXml($id);
         
        if($xml){
            foreach ($xml->xpath('//Episode') as $episode) {
                $episodes[(int)$episode->SeasonNumber][] = $episode;
            }
            return $episodes;
        }
             
        return false;
    }

    public function getEpisodeById($id)
    {
        return $this->getSimpleXmlResponse($this->apiKey.'/episodes/'.$id.'/'.$this->language.'.xml'); 
     }

    public function getSerieBanners($id, $filter=array())
    {
        $xml = $this->loadXml($id,self::BANNER);
        
        if(!$xml)
            return false;
        
        $xpathFilter ='';
        if(!empty($filter)){
            $xpathFilter = '[';
            foreach ($filter as $key => $value) {
                $xpathFilter.=$key.' = "'.$value.'" and ';
            }
            $xpathFilter = substr($xpathFilter,0,-5); 
            $xpathFilter.=']';
        }

        return $xml->xpath('Banner'.$xpathFilter);     
    }

    public function getSerieActorsById($id)
    {
        $xml = $this->loadXml($id,self::ACTORS);
        
        if($xml)
            return (array)$xml->xpath('//Actor');
        
        return false;
    }

    public function getServerTime()
    { 
        return $this->getSimpleXmlResponse('Updates.php?type=none');
    }

    public function getLastUpdates($last_update_time)
    {
        $xml = $this->getSimpleXmlResponse('Updates.php?type=all&time='.$last_update_time);
        
        return (array)$xml->Series;
    }
    
    public function getSerieRandomImage($id,$type,$param=false)
    {
        switch ($type) {
            case 'large':
                $filter =array('BannerType' => "fanart",
                               'BannerType2'=> "1920x1080");    
                break;
            case 'banner':
                $filter =array('BannerType' => "series",
                               'BannerType2'=> "graphical",
                               'Language'   => "en");   
                break; 
            case 'season':
                $filter =array('BannerType' => "season",
                               'BannerType2'=> "season",
                               'Language'   => "en",
                               'Season'     => (string )$param);   
                               
                break;                          
            default:
                $filter = array();
                break;
        }
        $xml = $this->getSerieBanners($id,$filter);
       
        if($xml){
          return $xml[rand(0,count($xml)-1)];
        }

        return false;
    }
    
    private function getSimpleXmlResponse($url)
    {
        $response = $this->client->get('/api/'.$url);
   
        if ($response->getStatusCode() != 200)
            return false;

         return $response->xml();
    }
    /**
     * retourne le xml d'une serie
     *
     * @param string $filename 
     * @return void
     * @author Niko
     */
    private function loadXml($id,$filename=self::INFOS)
    {
      if(file_exists($this->xmlPath.$id.'/'.$filename))
          return simplexml_load_file($this->xmlPath.$id.'/'.$filename);
      
      return false; //TODO logger une erreur
  }
    
}