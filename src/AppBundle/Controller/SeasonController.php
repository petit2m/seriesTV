<?php

namespace AppBundle\Controller;

use AppBundle\Business\ServiceServiio;
use AppBundle\Business\ServiceTvdb;
use AppBundle\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SeasonController extends Controller
{
    public function listServiioAction()
    {
        $serviceServiio = $this->get('serviceServiio');
        $em             = $this->getDoctrine()->getManager();
        $series         = $em->getRepository('AppBundle:Serie')->getUnfinished();
        $serviceServiio->authenticate();       
        
        foreach ($series as $serie) {
            $seasons = $serviceServiio->browse($serie->getIdServiio());

            foreach ($seasons['objects'] as $serviioSeason) {
                $id = $serviioSeason['id'];
                $season = $em->getRepository('AppBundle:Season')->findByIdServiio($id);
                if(!$season){
                    $season = new Season();
                    $season->setIdServiio($serviioSeason['id'])
                            ->setName($serviioSeason['title'])
                            ->setSerie($serie);
                    $em->persist($season); 
                }
            }    
        }
        $em->flush();

        return $this->render('AppBundle:Default:index.html.twig',array('serie'=>var_export($season,true)));
    }
    
    public function updateInfosAction()
    {
        $bs      = $this->get('serviceBS');
        $em      = $this->getDoctrine()->getManager();
        $seasons = $em->getRepository('AppBundle:Season')->findAll();
        $bs->login();
        $bsInfos   = $bs->getMemberTvdBSeries(); // un seul appel plutôt qu'un par série

        foreach ($seasons as $season) {
            if (isset($bsInfos[$season->getSerie()->getIdTvdb()])) {
            //    TODO sortir le code spécifique au json dans le service
                $season->setNbEpisode($bsInfos[$season->getSerie()->getIdTvdb()]
                                              ['seasons_details']
                                              [substr($season->getIdServiio(),-1)-1]
                                              ['episodes']);
                 $em->persist($season); 
            }
        } 
         $em->flush();                             
    }

}