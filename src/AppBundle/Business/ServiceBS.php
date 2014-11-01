<?php

namespace AppBundle\Business;
    
use GuzzleHttp\Client;

/**
* Classe to use BetaSeries API
*/
class ServiceBS
{
    
    private $client;

    private $format;
    
    private $headers;
    
    //badge, banner, bugs, character, commentaire, dons, episode, facebook, film, forum, friend, message, quizz, recommend, site, subtitles, video
    const TYPE_NOTIF ='episode'; 
    /*
        TODO Supprimer le format paramètrable
    */    
    function __construct($format, $server, $key)
    {
        $this->format = $format;
        $this->client = new Client( array('base_url' => $server, 'defaults'=>array('exceptions' => false) ) );
        //TODO à renommer car c'est plus que des headers, option ?
        $this->headers = array('headers' => array(
                                    'Accept' => 'application/'.$format,
                                    'X-BetaSeries-Key'=> $key,
                                    'X-BetaSeries-Version' => '2.3'            
                                )
                             );
                             
    }
    // TODO: pas d'exception si 400 comme ça je peux tester le résultat
    public function login($user, $password) 
    {
        $this->headers['body'] = array('login' => $user,
                                       'password' => $password);
                          
        $response = $this->client->post('/members/auth', $this->headers);
        
        $res = $response->{$this->format}();
        
        if ($response->getStatusCode() !== 200 && !empty($res['errors']))
            $this->checkError($res['errors']);
    
        if(isset($res['token']))
            $token = $res['token'];
        else // alors la y a un problème...
            return false;
                
        $this->headers['headers']['X-BetaSeries-Token'] = $token; 
        
        return true; 
    }
    
    public function logout()
    {
        $response = $this->client->post('/members/destroy', $this->headers);        
        
        return $response->getStatusCode() == 200 ? true : false;
    }
    
    private function isActive()
    {       
         $response = $this->client->get('/members/is_active', $this->headers);  
        
         return $response->getStatusCode() == 200 ? true : false;
    }
    
    public function getInfos($media_type = 'shows')
    {
        $this->headers['body'] = array('only' => $media_type);
        $response = $this->client->get('/members/infos', $this->headers);
        $res = (array)$response->{$this->format}();
       
        return $res;
    }
    
    public function getNotifications($number, $since_id=0, $sort='DESC')
    {
        $this->headers['body'] = array('number' => $number,
                                       'sort' => $sort,
                                       'types' => self::TYPE_NOTIF);
        if($since_id != 0)
            $this->headers['body']['since_id'] = $since_id;
        
         $response = $this->client->get('/members/notifications',  $this->headers);
         
         $res = $response->{$this->format}();
       
         return $res;
    }
    
    public function setDownloaded($tvdb_id)
    {
        $this->headers['body']['thetvdb_id'] = $tvdb_id;
        $response = $this->client->get('/episodes/downloaded', $this->headers);
        
        return $response->getStatusCode() == 200 ? true : false;
    }
    
    public function setWatched($tvdb_id, $bulk='true')
    {
        $this->headers['body'] = array('thetvdb_id' => $tvdb_id,
                                       'bulk' => $bulk);
        $response = $this->client->get('/episodes/watched', $this->headers);
        
        return $response->getStatusCode() == 200 ? true : false;
    }
    
    private function checkError($tab_error)
    {
        $errorMessage = '';
        
        foreach ($tab_error as $error) {
            $errorMessage.=$error['text'].' ';
        }
        
        /*
            TODO voir ce qu'on fait des messages d'erreur 
        */
        // throw new \Exception($errorMessage);
    }
    
}
?>