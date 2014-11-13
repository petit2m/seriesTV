<?php

namespace AppBundle\Controller;

use AppBundle\Business\Rss;
use AppBundle\Business\ServiceServiio;
use AppBundle\Business\ServiceTvdb;
use AppBundle\Business\ServiceBS;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function bsAction($format)
    {
        $serviceBS = $this->get('serviceBS');
        $serviceBS->login();
        $series = $serviceBS->getMemberTvdBSeries();
        $serviceBS->logout();
        
        return $this->render('AppBundle:Default:index.html.twig',array('serie' => var_export($series,true)));
    }
    
    /* TEST SERVIIO */
    public function serviioAction()
    {
		
	   $server = $this->container->getParameter('serviio_server');
       $password       = $this->container->getParameter('serviio_password');
       $serviceServiio = new ServiceServiio($server ,$password);
       $serviceServiio->authenticate();
       $series = $serviceServiio->getInfos('v','VOSTFR');
      // $series = $serviceServiio->modified("W/\"6a4db39d-2a76-4c94-b1f5-c0bc605f9b02\"");
       /*
        foreach ($series as $idSerie => $seasons) {
            list($idSerieServiio, $serieName) = explode(',',$idSerie);  
            echo $serieName.'<br>';
             foreach ($seasons as $idSeason => $episodes) {
                 list($idSeasonServiio, $seasonName) = explode(',',$idSeason);        
                 echo ' ->'.$seasonName.'<br>';
                 foreach ($episodes as $episodeId) {
                     echo '      '.$episodeId.'<br>';
                 }
             }
        }
        */
        return $this->render('AppBundle:Default:index.html.twig',array('serie'=>var_export($series,true)));
    }
    
    
    
    
    // TEST TVDB
     public function tvdbAction()
     {
         $server = $this->container->getParameter('tvdb_server');
         $apiKey = $this->container->getParameter('tvdb_api_key');
         $tvdb = new ServiceTvdb($server,$apiKey);

         // print_r($tvdb->getSerieByName('Walking Dead'));
         // print_r($tvdb->getEpisodesBySerieId(221451));
         print_r($tvdb->getEpisodeById(250853526));
         die;
          return $this->render('AppBundle:Default:index.html.twig',array('serie'=>var_export($series,true)));
     }
     
     // TEST TVDB
      public function rssAction()
      {

          $rss = new Rss();
          $series = $rss->parse('http://www.frenchtorrentdb.com/rss/0007b6e7c11075c881/tv_vostfr.rss');
         
           return $this->render('AppBundle:Default:index.html.twig',array('serie'=>var_export($series,true)));
      }
}

