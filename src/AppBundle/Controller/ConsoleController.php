<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Download;
use AppBundle\Business\ServiceBS;
use AppBundle\Business\Rss;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ConsoleController extends Controller
{
     
    public function indexAction()
    {
       
       $response = $this->render('AppBundle:Console:console.html.twig');
       
    
       return $response;
    }
    
    
    
}