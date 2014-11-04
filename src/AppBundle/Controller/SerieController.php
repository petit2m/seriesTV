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
        $series = $em->getRepository('AppBundle:Serie')->createQueryBuilder('s')
                                                       ->where('s.idTvdb is NULL')
                                                       ->getQuery()
                                                       ->getResult();       
        
        foreach ($series as $serie) {
            $seriesInfos = $tvdb->getSerieByName($serie->getName());
            if($seriesInfos)
                $serie->setIdTvdb($seriesInfos->id);
            
            $em->persist($serie);
        }
        $em->flush();
        
        return $this->render('AppBundle:Default:index.html.twig',array('serie'=>var_export($seriesInfos,true)));
    }  
}