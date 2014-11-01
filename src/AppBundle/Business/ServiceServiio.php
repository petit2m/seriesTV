<?php

namespace AppBundle\Business;

use GuzzleHttp\Client;

/**
* Class to access serviio APIs services
*/

class ServiceServiio 
{
 
    private $client;

    private $format;
    
    private $header;

    const HEADER_LENGTH   = "Content-Length: 0";
    const HEADER_CLOSE    = "Connection: close";
    const ERROR_CODE_NAME = "errorCode";
    const TOKEN_NAME      = "parameter";
    const SERIES_FLAG     = 'V_S';

    function __construct($format, $server, $password)
    {
        $this->password = $password;
        $this->format = $format;
        $this->client = new Client( array('base_url' => $server) );
        $this->headers = array('headers' => array(self::HEADER_LENGTH,
                                                  self::HEADER_CLOSE,
                                                  'Accept' => 'application/json'));
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

        $response = $this->client->get($this->server.'/cds/application');
        
        return $response->{$this->format}();
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
        
        if($response->getStatusCode() != 200 or !$this->checkErrorCode($response))
            return false;
      
        return $this->getSimpleParameter($response,self::TOKEN_NAME);
    }

    public function logout($token)
    {
        $response = $this->client->post('/cds/logout/?authToken='.$token,
                                        $this->headers
                                    );
        if($response->getStatusCode() == 200)
            return true;

        return $this->checkErrorCode($response);            
    }
    // TODO : tester les erreurs
    public function browse($token, $object_id, $browse_method ='BrowseDirectChildren', $filer='all', $start_index=0, $requested_count=0)
    {       
         $response = $this->client->get('/cds/browse/1/'.$object_id.'/'.$browse_method.'/'.$filer.'/'.$start_index.'/'.$requested_count.'?authToken='.$token,
             $this->headers);
        
        return $response->json();
    }
	
    private function recursiveBrowse($token, $object_id)
    {
		$ret = array();
		$res = $this->browse($token, $object_id);
		
        foreach ($res['objects'] as $ressource ) {
            if($ressource['type'] == 'CONTAINER')
                $ret[$ressource['id'].','.$ressource['title']]= self::recursiveBrowse($token,$ressource['id']);
            elseif ($ressource['fileType'] == 'VIDEO' && $ressource['contentType'] == 'EPISODE')
                foreach($ressource['onlineIdentifiers'] as $identifier)
                    if($identifier['type'] == 'TVDB')
                        $ret[] = $identifier['id'];
        }
        return $ret;
    }
	
    public function getAllSeriesId()
    {
        if(!$this->ping())
            return false;

        $token = $this->login();

        if(!$token)
            return false;

        $res = $this->recursiveBrowse($token, self::SERIES_FLAG);

        $this->logout($token);

        return $res;
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
    
    private function checkErrorCode(\GuzzleHttp\Message\Response $response)
    {
        $errorCode = $this->getSimpleParameter( $response, self::ERROR_CODE_NAME);
        
        if($errorCode == 0)
            return true;
        else
            throw new Exception ($this->getErrorMessage[$errorCode]);
    }
    
    private function getSimpleParameter(\GuzzleHttp\Message\Response $response, $parameter)
    {
        $res = $response->{$this->format}();

        return isset($res[$parameter]) ? $res[$parameter][0] : false;
    }
}