<?php

namespace AppBundle\Controller;

use AppBundle\Business\ServiceServiio;
use AppBundle\Entity\Serie;
use AppBundle\Entity\Episode;
use AppBundle\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DbController extends Controller
{
    public function initServiioAction()
    {
        
        $server = $this->container->getParameter('serviio_server');
       
        $series = $serviioManager->getAllSeriesId();
        $em = $this->getDoctrine()->getEntityManager();

        foreach ($series as $idSerie => $seasons) {
            list($idSerieServiio, $serieName) = explode(',',$idSerie);  
            $serie = new Serie();
            $serie->setIdServiio($idSerieServiio)
                  ->setName($serieName);

            foreach ($seasons as $idSeason => $episodes) {
                list($idSeasonServiio, $seasonName) = explode(',',$idSeason);        
                $season = new Season();
                $season->setIdServiio($idSeasonServiio)
                       ->setSerie($serie)
                       ->setName($seasonName);               

                foreach ($episodes as $idEpisode) {
                    $episode = new Episode();
                    $episode->setIdServiio($idEpisode);
                    $episode->setSeason($season);
                    $this->save($episode);
                }
                $this->save($season);
             }
              $this->save($serie);
            break; 
        }

        return $this->render('AppBundle:Db:index.html.twig');
    }

    private function save($object)
    {           
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist( $object);
        $em->flush();
    }

}