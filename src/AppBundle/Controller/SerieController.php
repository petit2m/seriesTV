<?php

namespace AppBundle\Controller;

use AppBundle\Business\ServiceServiio;
use AppBundle\Business\ServiceTvdb;
use AppBundle\Entity\Serie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

        return $this->render('AppBundle:Default:index.html.twig',array('serie'=>var_export($series,true)));
    }
    
    public function updateInfosAction()
    {
        $em     = $this->getDoctrine()->getManager();
        $tvdb   = $this->get('serviceTvdb');
        $bs     = $this->get('serviceBS');
        $series = $em->getRepository('AppBundle:Serie')->findAll();       
        $bs->login();
        $bsInfos   = $bs->getMemberTvdBSeries(); // un seul appel plutôt qu'un par série
        
        foreach ($series as $serie) {
            if($serie->getIdTvdb() == NULL){
                $tvdbInfos = $tvdb->getSerieByName($serie->getName());
                if($tvdbInfos)
                    $serie->setIdTvdb($tvdbInfos->id);
            }           
            
            if(isset($bsInfos[$serie->getIdTvdb()])){
                $serieInfo = $bsInfos[$serie->getIdTvdb()];
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
        return $this->render('AppBundle:Default:index.html.twig',array('serie'=>var_export($series,true)));
    }
    
    public function FunctionName($value='')
    {
        # code...
    }  
}