<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Serie;
use AppBundle\Business\ServiceBS;
use AppBundle\Business\Rss;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DownloadController extends Controller
{
    public function buildRssAction()
    {
        $serviceBS = $this->get('serviceBS');
        $rss = new Rss();
        $seriesRss = $rss->parse($this->container->getParameter('torrent_rss'));
        $serviceBS->login();
        $series = $serviceBS->getEpisodeToDownload();
        $serviceBS->logout();       
        $resultats = array_intersect_key($seriesRss,$series);
        // echo '<pre>';
 //        var_dump($resultats);
 //        echo '</pre>';die;
        foreach($resultats as $name => $resultat){
            arsort($resultat);
            $download[$name]=array_shift($resultat);
        }
         // echo '<pre>';
       //   var_dump($download);
       //   echo '</pre>';die;
             return $this->render('AppBundle:Rss:download.html.twig',array('download'=>$download));
    }
    
}