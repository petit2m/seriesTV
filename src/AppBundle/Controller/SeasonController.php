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
        $series         = $em->getRepository('AppBundle:Serie')->findAll();
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

}