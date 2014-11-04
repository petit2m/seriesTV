<?php

namespace AppBundle\Controller;

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
        $series = $serviceBS->getNotifications(100,250719216);
        $serviceBS->logout();
        
        return $this->render('AppBundle:Default:index.html.twig',array('serie' => var_export($series,true)));
    }
    
    /* TEST SERVIIO */
    public function serviioAction()
    {
		
	   $server = $this->container->getParameter('serviio_server');
       $password       = $this->container->getParameter('serviio_password');
       $serviceServiio = new ServiceServiio($server ,$password);
       $series = $serviceServiio->getSeries();
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
         return $this->render('SamsungServiioAppBundle:Default:index.html.twig',array('serie'=>$series));
     }
}

