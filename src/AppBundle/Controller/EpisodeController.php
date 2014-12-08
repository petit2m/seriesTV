<?php

namespace AppBundle\Controller;

use AppBundle\Business\ServiceServiio;
use AppBundle\Business\ServiceBS;
use AppBundle\Entity\Episode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class EpisodeController extends Controller
{
    // récupère les nouveaux épisodes et les enregistre en bdd
    public function listServiioAction()
    {
        $serviceServiio = $this->get('serviceServiio');
        $em             = $this->getDoctrine()->getManager();
        $seasons        = $em->getRepository('AppBundle:Season')->getActive();
        $serviceServiio->authenticate();       
            
        foreach ($seasons as $season) {
            $serviioEpisodes = $serviceServiio->browse($season->getIdServiio());
        
            foreach ($serviioEpisodes['objects'] as $serviioEpisode) {
                $id = $serviioEpisode['id'];
                $episodes = $em->getRepository('AppBundle:Episode')->findByIdServiio($id);
                foreach ($serviioEpisode["onlineIdentifiers"] as $onlineId) {
                    if($onlineId['type'] == 'TVDB'){
                        $idTvdb = $onlineId['id'];
                        break;
                    }
                }
                
                if(!$episodes){
                    $episode = new Episode();
                    $episode->setIdServiio($serviioEpisode['id'])
                            ->setName($serviioEpisode['title'])
                            ->setIdTvdb($idTvdb)
                            ->setSeason($season);
                }else{
                    $episode=$episodes[0];
                    if($serviioEpisode['title'] != $episodes[0]->getName())
                        $episode->setName($serviioEpisode['title']);
                }
                
                if(false !== strpos($serviioEpisode['title'],'**')){
                    $episode->setWatched(1);
                }
                $em->persist($episode); 
            }  
            $episode->getSeason()->setNbDownloadedEpisode(count($serviioEpisodes['objects']));  
            $em->persist($season);
        }
        $em->flush();
        $serviceServiio->logout();
        
        return new Response();
    }
    
    // Envoie les nouveaux épisodes téléchargés à betaseries
    public function downloadedAction()
    {
        $em       = $this->getDoctrine()->getManager();
        $episodes = $em->getRepository('AppBundle:Episode')
                            ->getUndownloaded();
        
        if(count($episodes) == 0)
            return false;      
        
        $serviceBS = $this->get('serviceBS');
        $serviceBS->login();
        
        foreach ($episodes as $episode) {
            if($serviceBS->setDownloaded($episode->getIdTvdb())){
                $episode->setDownloaded(1);
                $em->persist($episode);
            }else{
                echo $episode->getSeason()->getSerie()->getName().'\n';
                //TODO voir pourquoi et ajouter la série ?
            }
        }
        $em->flush();
        $serviceBS->login();
        
        return new Response();
    }
    
    // Envoie les nouveaux épisodes vus à betaseries
    public function watchedAction()
    {
        $em             = $this->getDoctrine()->getManager();
        $episodes = $em->getRepository('AppBundle:Episode')
                            ->getWatched(); 
        
        if(count($episodes) == 0)
            return false;      
        
        $serviceBS = $this->get('serviceBS');
        $serviceBS->login();
 
        foreach ($episodes as $episode) {
            if($serviceBS->setWatched($episode->getIdTvdb())){
                $episode->setWatched(1);
                $em->persist($episode);
                echo 'success !'.'\n';
            }else{
                echo $episode->getSeason()->getSerie()->getName()."\n";
                echo 'fail !';
                //TODO voir pourquoi et ajouter la série ?
            }
            //break;
        }
        $em->flush();
        $serviceBS->logout();
        
        return new Response();
    }
    
}