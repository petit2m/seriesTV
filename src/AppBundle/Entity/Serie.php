<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Serie
 *
 * @ORM\Table(name="Serie")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SerieRepository")
 */
class Serie
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
     * @ORM\Column(name="id_serviio", type="string", length=25, nullable=true, unique=true)
     */
    private $idServiio;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_season", type="integer", options={"default":1})
     */
    private $nbSeason=1;

    /**
     * @var integer
     *
     * @ORM\Column(name="remaining", type="integer", nullable=true)
     */
    private $remaining;

    /**
     * @var boolean
     *
     * @ORM\Column(name="archived", type="boolean", options={"default":0})
     */
    private $archived=0;



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
     * @return Serie
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
     * @return Serie
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
     * @return Serie
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
     * Set nbSeason
     *
     * @param integer $nbSeason
     * @return Serie
     */
    public function setNbSeason($nbSeason)
    {
        $this->nbSeason = $nbSeason;

        return $this;
    }

    /**
     * Get nbSeason
     *
     * @return integer 
     */
    public function getNbSeason()
    {
        return $this->nbSeason;
    }

    /**
     * Set archived
     *
     * @param boolean $archived
     * @return Serie
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * Get archived
     *
     * @return boolean 
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * Set remaining
     *
     * @param integer $remaining
     * @return Serie
     */
    public function setRemaining($remaining)
    {
        $this->remaining = $remaining;

        return $this;
    }

    /**
     * Get remaining
     *
     * @return integer 
     */
    public function getRemaining()
    {
        return $this->remaining;
    }
}
