<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Serie
 *
 * @ORM\Table(name="Serie")
 * @ORM\Entity
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
     * @ORM\Column(name="id_serviio", type="string", length=25, nullable=true)
     */
    private $idServiio;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=true)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="first_aired", type="datetime", nullable=true)
     */
    private $firstAired;

    /**
     * @var string
     *
     * @ORM\Column(name="network", type="string", length=45, nullable=true)
     */
    private $network;



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
     * Set firstAired
     *
     * @param \DateTime $firstAired
     * @return Serie
     */
    public function setFirstAired($firstAired)
    {
        $this->firstAired = $firstAired;
    
        return $this;
    }

    /**
     * Get firstAired
     *
     * @return \DateTime 
     */
    public function getFirstAired()
    {
        return $this->firstAired;
    }

    /**
     * Set network
     *
     * @param string $network
     * @return Serie
     */
    public function setNetwork($network)
    {
        $this->network = $network;
    
        return $this;
    }

    /**
     * Get network
     *
     * @return string 
     */
    public function getNetwork()
    {
        return $this->network;
    }
}