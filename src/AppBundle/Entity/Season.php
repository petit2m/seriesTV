<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Season
 *
 * @ORM\Table(name="Season")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SeasonRepository")
 */
class Season
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
     * @ORM\Column(name="nb_episode", type="integer",nullable=true)
     */
    private $nbEpisode;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_downloaded_episode", type="integer", nullable=true)
     */
    private $nbDownloadedEpisode;
    
    /**
     * @var string
     *
     * @ORM\Column(name="id_serviio", type="string", length=25, nullable=true)
     */
    private $idServiio;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     */
    private $name;

    /**
     * @var \Serie
     *
     * @ORM\ManyToOne(targetEntity="Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Serie_id", referencedColumnName="id")
     * })
     */
    private $serie;



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
     * @return Season
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
     * @return Season
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
     * Set name
     *
     * @param string $name
     * @return Season
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
     * Set serie
     *
     * @param \Samsung\ServiioAppBundle\Entity\Serie $serie
     * @return Season
     */
    public function setSerie(Serie $serie = null)
    {
        $this->serie = $serie;
    
        return $this;
    }

    /**
     * Get serie
     *
     * @return \Samsung\ServiioAppBundle\Entity\Serie 
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set nbEpisode
     *
     * @param integer $nbEpisode
     * @return Season
     */
    public function setNbEpisode($nbEpisode)
    {
        $this->nbEpisode = $nbEpisode;

        return $this;
    }

    /**
     * Get nbEpisode
     *
     * @return integer 
     */
    public function getNbEpisode()
    {
        return $this->nbEpisode;
    }

    /**
     * Set nbDownloadedEpisode
     *
     * @param integer $nbDownloadedEpisode
     * @return Season
     */
    public function setNbDownloadedEpisode($nbDownloadedEpisode)
    {
        $this->nbDownloadedEpisode = $nbDownloadedEpisode;

        return $this;
    }

    /**
     * Get nbDownloadedEpisode
     *
     * @return integer 
     */
    public function getNbDownloadedEpisode()
    {
        return $this->nbDownloadedEpisode;
    }
}
