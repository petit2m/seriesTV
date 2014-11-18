<?php

namespace AppBundle\Business;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

/**
* Class to access serviio APIs services
*/
/*
    TODO voir coment gérer le cache grace aux ETag contenus dans la réponse ?    
*/
class ServiceServiio 
{
 
    private $client;

    private $header;
    
    private $token;

    const ERROR_CODE_NAME = "errorCode";
    const TOKEN_NAME      = "parameter";
    const SERIES_FLAG     = 'V_S';

    function __construct($server, $password)
    {
        $this->password = $password;
        $this->client   = new Client( array('base_url' => $server, 'defaults'=>array('exceptions' => false) ) );
        $this->headers  = array('headers' => array('Accept' => 'application/json'));
    }

    public function authenticate()
    {
        //if server is up
        if(!$this->ping())
            return false;
        
        if(!$this->login())
            return false;
        
        return true;
    }

    private function ping()
    {
        $response = $this->client->get('/cds/ping');
  
        return $response->getStatusCode() == 200 ? true : false;
    }

    public function getInfos()
    {
        //if server is up
        if(!$this->ping())
            return false;

        $response = $this->client->get('/cds/application',$this->headers);
        
        return $response->json();
    }

    public function login()
    {
        $date =date("D, d M y H:i:s T");
        $response = $this->client->post(
                        '/cds/login',
                        array('headers' =>array(
                            'Accept'     => 'application/json',
                            'Date' => $date,
                            'Authorization' => 'Serviio '.base64_encode(hash_hmac('sha1',$date,$this->password,true))
                            )
                        )            
                    );
        
        if($response->getStatusCode() != 200)
            return false;
      
        $this->token = $this->getSimpleParameter($response,self::TOKEN_NAME);
        
        return true;
    }

    public function logout()
    {
        $response = $this->client->post('/cds/logout/?authToken='.$this->token, $this->headers);
        if($response->getStatusCode() == 200)
            return true;

        return $this->checkErrorCode($response);            
    }
    
    
    
    public function browse($object_id, $browse_method ='BrowseDirectChildren', $filer='all', $start_index=0, $requested_count=0)
    {       
        $response =$this->client->get('/cds/browse/1/'.$object_id.'/'.$browse_method.'/'.$filer.'/'.
                                      $start_index.'/'.$requested_count.'?authToken='.$this->token, $this->headers);
        if($response->getStatusCode() != 200)
            return false;
         
        // TODO : tester les erreurs           
        return $response->json();
    }
    
    
    public function search($type_file, $term, $start_index=0, $requested_count=0)
    {       
        $response = $this->client->get('/cds/search/1/'.$type_file.'/'.$term.'/'.$start_index.'/'.
                                         $requested_count.'?authToken='.$this->token,$this->headers);
        if($response->getStatusCode() != 200)
            return false;
        
        return $response->json();
    }
	
    private function recursiveBrowse($object_id)
    {
		$ret = array();
		$res = $this->browse($token, $object_id);
        if(!$res)
            return false;
        
        foreach ($res['objects'] as $ressource ) {
            if($ressource['type'] == 'CONTAINER')
                $ret[$ressource['id'].','.$ressource['title']]= self::recursiveBrowse($token,$ressource['id']);
            elseif ($ressource['contentType'] == 'EPISODE'){
                //var_dump($ressource);
                foreach($ressource['onlineIdentifiers'] as $identifier)
                    if($identifier['type'] == 'TVDB'){
                        $ret[] = $identifier['id'].'-'.$ressource['title'];
                        break;
                    }
                
            }
        }
        return $ret;
    }
	
    public function getSeries()
    {
        if(!$this->ping())
            return false;

        $this->login();
       
        $res = $this->browse('0');
        
        $this->logout();

        return $res;
    }
    /**
     * Permet de savoir si la bibliotheque serviio a changé depuis la dernière fois (attention c'est juste si on a ajouté ou supprimé des fichier)
     *
     * @param string $etag 
     * @return void
     * @author Niko
     */
    public function modified($etag)
    {
        $response = $this->client->get('/cds/browse/1/0/BrowseDirectChildren/all/0/1?authToken='.$this->token,$this->headers);   
        
        if($response->getStatusCode() == 200 && $response->getHeader('ETag') == $etag)
            return false;
        else
            return true;
    }

    private function getErrorMessage($error_message)
    {
       // $error = json_decode($response->getContent(),true);
        //Application error codes
        $SERVIIO_ERROR_CODE = array(
            550 => 'missing Date or X-Serviio-Date header',
            551 => 'missing Authorization header',
            552 => 'Authorization header has invalid value (possibly wrong password)',
            554 => 'invalid edition of Serviio, functionality not available for this edition',
            556 => 'the user has not set up their password yet or it is empty',
            557 => 'the server has been stopped');
            
        return isset($SERVIIO_ERROR_CODE[$error_code]) ? $SERVIIO_ERROR_CODE[$error_code] : 'unknown error';  
    }
    
    private function checkErrorCode(Response $response)
    {
        $errorCode = $this->getSimpleParameter( $response, self::ERROR_CODE_NAME);
        
        if($errorCode == 0)
            return true;
        else
            throw new Exception ($this->getErrorMessage[$errorCode]);
    }
    
    private function getSimpleParameter(Response $response, $parameter)
    {
        $res = $response->$this->json();

        return isset($res[$parameter]) ? $res[$parameter][0] : false;
    }
}