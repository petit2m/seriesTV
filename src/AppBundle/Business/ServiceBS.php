<?php

namespace AppBundle\Business;
    
use GuzzleHttp\Client;

/**
* Classe to use BetaSeries API
*/
class ServiceBS
{
    
    private $client;

    private $headers;
    
    private $user;
    
    private $password;
    
    
    function __construct( $server, $key, $user, $password)
    {
        $this->user     = $user;
        $this->password = $password;
        $this->client   = new Client( array('base_url' => $server, 'defaults'=>array('exceptions' => false) ) );
        $this->options = array('headers' => array(
                                    'Accept' => 'application/json',
                                    'X-BetaSeries-Key'=> $key,
                                    'X-BetaSeries-Version' => '2.3'            
                                )
                             );
                             
    }
    /**
     * Permet de s'authentifier à l'API de betaseries   
     * Créée un token de session 
     *
     * @return boolean Le résultat de l'opération
     * @author Niko
     */
    public function login() 
    {
         $this->options['body'] = array('login' => $this->user,
                                       'password' => $this->password);
                          
        $response = $this->client->post('/members/auth', $this->options);
        unset($this->options['body']);
        $res = $response->json();
        
        if ($response->getStatusCode() !== 200 && !empty($res['errors']))
            $this->checkError($res['errors']);
    
        if(isset($res['token']))
            $token = $res['token'];
        else // alors la y a un problème...
            return false;
                
        $this->options['headers']['X-BetaSeries-Token'] = $token; 
       
        return true; 
    }
    
    /**
     * Permet de se déconnecter en détruisant le token 
     *
     * @return void
     * @author Niko
     */
    public function logout()
    {
        $response = $this->client->post('/members/destroy', $this->options);        
        
        return $response->getStatusCode() == 200 ? true : false;
    }
    
    /**
     * Permet de savoir si le token de connexion est toujours valide
     *
     * @return void
     * @author Niko
     */
    private function isActive()
    {       
         $response = $this->client->get('/members/is_active', $this->options);  
        
         return $response->getStatusCode() == 200 ? true : false;
    }
    
    /**
     * Retoune les infos de la personne connectée
     *
     * @param string $media_type Le type de media
     * @return array Le résultat sour forme d'un tableau
     * @author Niko
     */
    public function getMemberInfo($media_type = 'shows')
    {
        $response = $this->client->get('/members/infos?only='.$media_type, $this->options);
        
        if ($response->getStatusCode() != 200)
            return false;
        
        $res = $response->json();
       
        return $res;
    }
    /**
     * retourne toutes les séries de l'utilisateur connecté
     *
     * @return array tableau de séries avec en clé les id Tvdbs 
     * @author Niko
     */
    public function getMemberTvdBSeries()
    {
        $tabSeries = array();
        $res = $this->getMemberInfo();
        if($res){
            foreach ($res['member']['shows'] as $serie) {
                $tabSeries[$serie['thetvdb_id']] = $serie;
            }
        }
        
        return $tabSeries;
    }
    
    public function getSerieInfo($tvdb_id)
    {
        $options['body']['thetvdb_id'] = $tvdb_id;
        $response = $this->client->get('/shows/display?thetvdb_id='.$tvdb_id, $this->options);
        
        if ($response->getStatusCode() != 200)
            return false;
            
        return $response->json();

    }
    
    public function getEpisodeToDownload()
    {
        $response = $this->client->get('/episodes/list?limit=1', $this->options);
        
        if ($response->getStatusCode() != 200)
            return false;
            
        $series = $response->json();

        if(!empty($series)){
            foreach ($series['shows'] as $serie) {
                if(!$serie['unseen'][0]['user']['downloaded']){
                    $downloads[str_replace(' ','.',$serie['title']).'.'.str_replace(' ','.',$serie['unseen'][0]['code'])]='';
                }
            }
        }
        return $downloads;
    }
    
    public function addSerie($id_tvdb)
    {
        $options['body']['thetvdb_id'] = $tvdb_id;
        $response = $this->client->post('/shows/show', array_merge($this->options,$options));
        
        return $response->getStatusCode() == 200 ? true : false;
    }
    
    /**
     * Renvoie les dernières notifications de l'utilisateur connecté
     *
     * @param integer $number   nombre maximum de résultats (10 par défaut, 100 max)
     * @param string $since_id  TODO voir comment ça marche
     * @param string $episode      type de notifications attendues (badge, banner, bugs, character, commentaire, dons, episode, facebook, film, forum,                                                                         friend, message, quizz, recommend, site, subtitles ou video)
     * @param string $sort         ordre du tri DESC ou ASC
     * @param boolean $auto_delete supprimer les notifications automatiquement  
     * @return array            tableau des notifications
     * @author Niko
     */
    public function getNotifications($number=100, $since_id=0, $types='episode', $sort='DESC', $auto_delete=false)
    {
        $uriParams = '?number='.$number.'&sort='.$sort.'&types='.$types;    
        if($since_id != 0)
             $uriParams.='&since_id='.$since_id;
  
        if($auto_delete)
            $uriParams.='&auto_delete';
        
         $response = $this->client->get('/members/notifications'.$uriParams,$this->options);
         $res = $response->json();
       
         return $res;
    }
    /**
     * Marque un épisode comme téléchargé
     *
     * @param int $tvdb_id  l'indentifiant TVDB de l'épisode 
     * @return boolean      résultat de l'opération      
     * @author Niko
     */
    public function setDownloaded($tvdb_id)
    {
        $this->options['body']['thetvdb_id'] = $tvdb_id;
        $response = $this->client->post('/episodes/downloaded', $this->options);
        unset($this->options['body']);
        
        if ($response->getStatusCode() != 200){
             $res = $response->json();
             echo $this->checkError($res['errors']);
             return false;
        }
      
        return true;
    }
    
    /**
     * Marque un épisode comme vu
     *
     * @param string $tvdb_id l'identifiant TVDB de l'épisode
     * @param boolean $bulk marquer tous les épisodes précédents comme vu //TODO tester les booléens car résultat peu convainquant
     * @return le résultat de l'opération
     * @author Niko
     */
    public function setWatched($tvdb_id, $bulk=true)
    {
        $this->options['body'] = array('thetvdb_id' => $tvdb_id,
                                       'bulk' => $bulk);
        $response = $this->client->post('/episodes/watched', $this->options);
        unset($this->options['body']);
         
        return $response->getStatusCode() == 200 ? true : false;
    }
    
    /**
     * Trace les éventuelles erreurs
     *
     * @param string $tab_error les erreurs reçues (voir si on peut en avoir plusieurs pas l'impression)
     * @return void
     * @author Niko
     */
    private function checkError($tab_error)
    {
        $errorMessage = '';
        
        foreach ($tab_error as $error) {
            $errorMessage.=$error['text'].' ';
        }
        return $errorMessage;
        /*
            TODO voir ce qu'on fait des messages d'erreur 
        */
        // throw new \Exception($errorMessage);
    }
    
}
?>