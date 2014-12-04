<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Download;
use AppBundle\Business\ServiceBS;
use AppBundle\Business\Rss;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
        $serviceBS->login();
        $series     = $serviceBS->getEpisodeToDownload();
        $serviceBS->logout();
 //$seriesRss['The.Walking.Dead.S05E05'][]=array('name'=>'test2','url'=>'http://test2');
        $resultats  = array_intersect_key($seriesRss,$series);
        // echo'<pre>';
//         var_dump($series);
//                 echo'</pre>';die;
        foreach($resultats as $name => $resultat){
            arsort($resultat);
            $episode  = array_shift($resultat);
            $downloads = $em->getRepository('AppBundle:Download')->findByIdTvdb($series[$name]['id_episode']);
            $serie   = $em->getRepository('AppBundle:Serie')->findByIdTvdb($series[$name]['id']); 
            
            if(!$series) //TODO cas à gérer : l'épisode est à regarder mais je n'ai pas la série...
                continue;
            
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
       
       // return $this->render('AppBundle:Rss:download.html.twig',array('download'=>$download));
    }
    
    public function indexAction()
    {
       $serviceBS  = $this->get('serviceBS');
       $series    = $serviceBS->getUnseenEpisode();
      // die;
       $response = $this->render('AppBundle:Unseen:unseen.html.twig',array('series'=>$series));
       
       $response->setPublic();
       $response->setSharedMaxAge(600);
       
       return $response;
    }
    
    
    
}