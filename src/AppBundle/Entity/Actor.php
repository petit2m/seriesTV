<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Actor
 *
 * @ORM\Table(name="Actor")
 * @ORM\Entity
 */
class Actor
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
     * @return Actor
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
}
