<?php

namespace AppBundle\Controller;

use AppBundle\Business\ServiceServiio;
use AppBundle\Business\ServiceTvdb;
use AppBundle\Business\ServiceBS;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
   /* public function indexAction($format)
    {
        $server = $this->container->getParameter('bs_server');
        $apiKey = $this->container->getParameter('bs_api_key');
        $user = $this->container->getParameter('bs_user');
        $password = $this->container->getParameter('bs_md5_password');
        
        $serviceBS = new ServiceBS($format, $server, $apiKey);
        $serviceBS->login($user, $password);
        $series = $serviceBS->getNotifications(100);
    
         return $this->render('AppBundle:Default:index.html.twig',array('serie' => var_export($series,true)));
    }
    */
    /* TEST SERVIIO */
    public function indexAction($format)
    {
		
	   $server = $this->container->getParameter('serviio_server');
       $password       = $this->container->getParameter('serviio_password');
       $serviceServiio = new ServiceServiio($format, $server ,$password);
       $series = $serviceServiio->getAllSeriesId();

       /* foreach ($series as $idSerie => $seasons) {
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
    // public function indexAction($format)
    // {
    //     $server = $this->container->getParameter('tvdb_server');
    //     $apiKey = $this->container->getParameter('tvdb_api_key');
    //     $tvdb = new ServiceTvdb($server,$apiKey);

    //     // print_r($tvdb->getSerieByName('Walking Dead'));
    //     // print_r($tvdb->getEpisodesBySerieId(221451));
    //     print_r($tvdb->getEpisodeById(4185563));
    //     die;
    //    // return $this->render('SamsungServiioAppBundle:Default:index.html.twig',array('series'=>$series));
    // }
}

