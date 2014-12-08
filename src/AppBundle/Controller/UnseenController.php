<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Download;
use AppBundle\Business\ServiceBS;
use AppBundle\Business\Rss;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UnseenController extends Controller
{
    /**
     * Croise un flux Rss de torrent avec mes données de betaséries pour établir la liste des épisodes à télécharger avec leur lien
     * Met à jour une table des épisodes à télécharger
     */
    public function updateAction()
    {
        $em         = $this->getDoctrine()->getManager();      
        $serviceBS  = $this->get('serviceBS');
        $rss        = new Rss();
        $seriesRss  = $rss->parse($this->container->getParameter('torrent_rss'));
        $series     = $serviceBS->getEpisodeToDownload();
        $serviceBS->logout();
        $resultats  = array_intersect_key($seriesRss,$series);
        foreach($resultats as $name => $resultat){
            arsort($resultat);
            $episode  = array_shift($resultat);
            $downloads = $em->getRepository('AppBundle:Download')->findByIdTvdb($series[$name]['id_episode']);
            $serie   = $em->getRepository('AppBundle:Serie')->findByIdTvdb($series[$name]['id']); 
            
            if(!$series){
              //TODO cas à gérer : l'épisode est à regarder mais je n'ai pas la série...
              // utiliser les logs pour tracer l'événement et proposer par la suite de s'abonner à la série
            } 
            
            if(!$downloads){
                 $download = new Download();
                 $download->setName($episode['name'])
                     ->setSerie($serie[0])
                     ->setIdTvdb($series[$name]['id_episode']);
             }else{
                 $download = $downloads[0];
             }
             $download->setDownloadUrl($episode['url'])
                 ->setName($episode['name']);
             
             $em->persist($download);
        }
        $em->flush();
        
        return new Response();
    }
    
    public function indexAction()
    {
       $serviceBS  = $this->get('serviceBS');
       $series    = $serviceBS->getUnseenEpisode();
       $response = $this->render('AppBundle:Unseen:unseen.html.twig',array('series'=>$series));
       
       $response->setPublic();
       $response->setSharedMaxAge(600);
       
       return $response;
    }
    
    
    
}