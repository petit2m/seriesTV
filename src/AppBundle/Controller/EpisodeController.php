<?php

namespace AppBundle\Controller;

use AppBundle\Business\ServiceServiio;
use AppBundle\Business\ServiceBS;
use AppBundle\Entity\Episode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EpisodeController extends Controller
{
    // récupère les nouveaux épisodes et les enregistre en bdd
    public function listServiioAction()
    {
        $serviceServiio = $this->get('serviceServiio');
        $em             = $this->getDoctrine()->getManager();
        $seasons        = $em->getRepository('AppBundle:Season')->findAll();
        $serviceServiio->authenticate();       
            
        foreach ($seasons as $season) {
            $episodes = $serviceServiio->browse($season->getIdServiio());
        
            foreach ($episodes['objects'] as $serviioEpisode) {
                $id = $serviioEpisode['id'];
                $episode = $em->getRepository('AppBundle:Episode')->findByIdServiio($id);
                if(!$episode){
                    $episode = new Episode();
                    $episode->setIdServiio($serviioEpisode['id'])
                            ->setName($serviioEpisode['title'])
                            ->setIdTvdb($serviioEpisode["onlineIdentifiers"][0]['id'])
                            ->setSeason($season);
                }elseif( $serviioEpisode['title'] != $episode->getName()){
                    $episode->setName($serviioEpisode['title']);
                }
                
                if(false !== strpos($serviioEpisode['title'],'**')){
                    $episode->setWatched(1);
                }
                $em->persist($episode); 
            }    
        }
        $em->flush();
        $serviceServiio->logout();
        
        return $this->render('AppBundle:Default:index.html.twig',array('serie'=>var_export($episode,true)));
    }
    
    // Envoie les nouveaux épisodes téléchargés à betaseries
    public function downloadedAction()
    {
        $em             = $this->getDoctrine()->getManager();
        $episodes = $em->getRepository('AppBundle:Episode')
                            ->createQueryBuilder('e')
                            ->where('e.downloaded = 0')
                            ->getQuery()
                            ->getResult(); 
        
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
                echo 'fail !';
                //TODO voir pourquoi et ajouter la série ?
            }
        }
        $em->flush();
        $serviceBS->login();
        
        return true;
    }
    
    // Envoie les nouveaux épisodes vus à betaseries
    public function watchedAction()
    {
        $em             = $this->getDoctrine()->getManager();
        $episodes = $em->getRepository('AppBundle:Episode')
                            ->createQueryBuilder('e')
                            ->where('e.watched = 1')
                            ->getQuery()
                            ->getResult(); 
        
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
        
        return true;
    }
    
    private function getServiceBS($value='')
    {
        # code...
    }
}