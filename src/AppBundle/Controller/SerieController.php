<?php

namespace AppBundle\Controller;

use AppBundle\Business\ServiceServiio;
use AppBundle\Business\ServiceTvdb;
use AppBundle\Entity\Serie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SerieController extends Controller
{
    public function listServiioAction()
    {
        $serviceServiio = $this->get('serviceServiio');
        $em             = $this->getDoctrine()->getManager();
        $serviceServiio->authenticate();
        $series = $serviceServiio->browse('V_S');
        
        foreach ($series['objects'] as $serviioSerie) {
            $id = $serviioSerie['id'];
            $serie = $em->getRepository('AppBundle:Serie')->findByIdServiio($id);
            if(!$serie){
                $serie = new Serie();
                $serie->setIdServiio($serviioSerie['id'])
                      ->setName($serviioSerie['title']);
                $em->persist($serie); 
            }
        }
        $em->flush();
        
        return new Response();
    }
    
    public function updateInfosAction()
    {
        $em      = $this->getDoctrine()->getManager();
        $tvdb    = $this->get('serviceTvdb');
        $bs      = $this->get('serviceBS');
        $series  = $em->getRepository('AppBundle:Serie')->findAll();       
        $bsInfos = $bs->getMemberTvdBSeries(); // un seul appel plutôt qu'un par série

        foreach ($series as $serie) {
            if($serie->getIdTvdb() == NULL){
                $tvdbInfos = $tvdb->getSerieByName($serie->getName());
                if($tvdbInfos)
                    $serie->setIdTvdb($tvdbInfos->id);
            }           
            
            if(!empty($bsInfos[(int)$serie->getIdTvdb()])){
                $serieInfo = $bsInfos[(int)$serie->getIdTvdb()];
                $serie->setNbSeason($serieInfo['seasons']);
                if($serieInfo['in_account'] === true){
                    $serie->setRemaining($serieInfo['user']['remaining']);
                    $serie->setArchived($serieInfo['user']['archived']);  
                }
            }
            
            $em->persist($serie);
        }
        $em->flush();
        $bs->logout();
       
        return new Response();
    }
    
    public function randomBannerAction($id,$type,$param=false)
    {
           
        $tvdb   = $this->get('serviceTvdb');
        $banner = $tvdb->getSerieRandomImage($id,$type,$param);
            
        $response = $this->render('AppBundle:Serie:banner.html.twig',array('banner'=>$banner));
        $response->setPublic();
        $response->setSharedMaxAge(600);
         
        return $response;
    }  
    
    // A lancer souvent pour éviter de récupérer trop de données
    public function getXmlInfoAction()
    {
        $em     = $this->getDoctrine()->getManager();
        $series = $em->getRepository('AppBundle:Serie')->findAll();      
        $tvdb   = $this->get('serviceTvdb');
        $date = new \DateTime();
        $date->modify("-2 year"); //TODO stocker le dernier timestamp de mise à jour
        $updated= $tvdb->getLastUpdates($date->getTimestamp()); 
      
        foreach ($series as $serie) {   
      //      if(in_array($serie->getIdTvdb(),$updated))
                $tvdb->getSerieZip($serie->getIdTvdb()); //TODO logger les updates
        } 
        
        $tvdb->unzipSeries();
        
        return new Response();
    }
    
    public function viewAction($id)
    {
        $em    = $this->getDoctrine()->getManager();
        $serie = $em->getRepository('AppBundle:Serie')->findById($id);      
        $tvdb  = $this->get('serviceTvdb');
        $serieXml = $tvdb->getSerieById($serie[0]->getIdTvdb());
        $episodes = $tvdb->getEpisodesBySerieId($serie[0]->getIdTvdb());
       //  echo '<pre>';
       // var_dump($episodes);
       // echo '</pre>';die;
           
        $response = $this->render('AppBundle:Serie:view.html.twig',array('serie'=>$serieXml[0], 'episodes'=>$episodes));
        $response->setPublic();
        $response->setSharedMaxAge(600);
        
        return $response;
       
    }
    
}