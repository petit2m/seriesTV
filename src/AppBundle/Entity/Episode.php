<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Episode
 *
 * @ORM\Table(name="Episode")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\EpisodeRepository")
 */
class Episode
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tvdb", type="integer", nullable=true)
     */
    private $idTvdb;

    /**
     * @var string
     *
     * @ORM\Column(name="id_serviio", type="string", length=25, nullable=true)
     */
    private $idServiio;

    /**
     * @var boolean
     *
     * @ORM\Column(name="number", type="boolean", nullable=true)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="overview", type="text", nullable=true)
     */
    private $overview;

    /**
     * @var string
     *
     * @ORM\Column(name="first_aired", type="string", length=45, nullable=true)
     */
    private $firstAired;

    /**
     * @var boolean
     *
     * @ORM\Column(name="downloaded", type="boolean",options={"default":0} )
     */
    private $downloaded=0;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="watched", type="boolean", options={"default":0})
     */
    private $watched=0;

    /**
     * @var \Season
     *
     * @ORM\ManyToOne(targetEntity="Season")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Season_id", referencedColumnName="id")
     * })
     */
    private $season;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idTvdb
     *
     * @param integer $idTvdb
     * @return Episode
     */
    public function setIdTvdb($idTvdb)
    {
        $this->idTvdb = $idTvdb;
    
        return $this;
    }

    /**
     * Get idTvdb
     *
     * @return integer 
     */
    public function getIdTvdb()
    {
        return $this->idTvdb;
    }

    /**
     * Set idServiio
     *
     * @param string $idServiio
     * @return Episode
     */
    public function setIdServiio($idServiio)
    {
        $this->idServiio = $idServiio;
    
        return $this;
    }

    /**
     * Get idServiio
     *
     * @return string 
     */
    public function getIdServiio()
    {
        return $this->idServiio;
    }

    /**
     * Set number
     *
     * @param boolean $number
     * @return Episode
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return boolean 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Episode
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set overview
     *
     * @param string $overview
     * @return Episode
     */
    public function setOverview($overview)
    {
        $this->overview = $overview;
    
        return $this;
    }

    /**
     * Get overview
     *
     * @return string 
     */
    public function getOverview()
    {
        return $this->overview;
    }

    /**
     * Set firstAired
     *
     * @param string $firstAired
     * @return Episode
     */
    public function setFirstAired($firstAired)
    {
        $this->firstAired = $firstAired;
    
        return $this;
    }

    /**
     * Get firstAired
     *
     * @return string 
     */
    public function getFirstAired()
    {
        return $this->firstAired;
    }

    /**
     * Set lastUpdated
     *
     * @param integer $lastUpdated
     * @return Episode
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    
        return $this;
    }

    /**
     * Get lastUpdated
     *
     * @return integer 
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * Set season
     *
     * @param \Samsung\ServiioAppBundle\Entity\Season $season
     * @return Episode
     */
    public function setSeason(Season $season = null)
    {
        $this->season = $season;
    
        return $this;
    }

    /**
     * Get season
     *
     * @return \Samsung\ServiioAppBundle\Entity\Season 
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set downloaded
     *
     * @param boolean $downloaded
     * @return Episode
     */
    public function setDownloaded($downloaded)
    {
        $this->downloaded = $downloaded;

        return $this;
    }

    /**
     * Get downloaded
     *
     * @return boolean 
     */
    public function getDownloaded()
    {
        return $this->downloaded;
    }

    /**
     * Set watched
     *
     * @param boolean $watched
     * @return Episode
     */
    public function setWatched($watched)
    {
        $this->watched = $watched;

        return $this;
    }

    /**
     * Get watched
     *
     * @return boolean 
     */
    public function getWatched()
    {
        return $this->watched;
    }
}
