<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Donwload;
use AppBundle\Business\ServiceBS;
use AppBundle\Business\Rss;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DownloadController extends Controller
{
    /**
     * Croise un flux Rss de torrent avec mes données de betaséries pour établir la liste des épisodes à télécharger avec leur lien
     * Met à jour une table des épisodes à télécharger
     */
    public function updateDownloadction()
    {
        $em         = $this->getDoctrine()->getManager();      
        $serviceBS  = $this->get('serviceBS');
        $rss        = new Rss();
        $seriesRss  = $rss->parse($this->container->getParameter('torrent_rss'));
        $serviceBS->login();
        $series     = $serviceBS->getEpisodeToDownload();
        $serviceBS->logout();
        $resultats  = array_intersect_key($seriesRss,$series);
     
        foreach($resultats as $name => $resultat){
            arsort($resultat);
            $episode  = array_shift($resultat);
            $download = $em->getRepository('AppBundle:Download')->findByIdTvdb($series[$name]['id_episode']);
            $serie    = $em->getRepository('AppBundle:Serie')->findByIdTvdb($series[$name]['id']); 

            if(!$download){
                 $download = new Download();
                 $download->setName($resultat['name'])
                     ->setSerie($serie)
                     ->setIdTvdb($series[$name]['id_episode']);
             }
             $download->setUrl($resultat['url'])
                 ->setName($resultat['name']);
             
             $em->persist($download);
        }
        $em->flush();

        return $this->render('AppBundle:Rss:download.html.twig',array('download'=>$download));
    }
    
    public function downloadAction()
    {
         $download = $em->getRepository('AppBundle:Download')->findAll();
         
         return $this->render('AppBundle:Rss:download.html.twig',array('download'=>$download));
    }
    
    
    
}