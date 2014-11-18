<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DownloadList
 *
 * @ORM\Table(name="Download")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\DownloadRepository")
 */
class DownloadList
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
     * @ORM\Column(name="name", type="string", length=150, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="download_url", type="string", length=255, nullable=true)
     */
    private $downloadUrl;
    
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
     * @return DownloadList
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
     * Set name
     *
     * @param string $name
     * @return DownloadList
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
     * Set downloadUrl
     *
     * @param string $downloadUrl
     * @return DownloadList
     */
    public function setDownloadUrl($downloadUrl)
    {
        $this->downloadUrl = $downloadUrl;

        return $this;
    }

    /**
     * Get downloadUrl
     *
     * @return string 
     */
    public function getDownloadUrl()
    {
        return $this->downloadUrl;
    }

    /**
     * Set serie
     *
     * @param \AppBundle\Entity\Serie $serie
     * @return DownloadList
     */
    public function setSerie(\AppBundle\Entity\Serie $serie = null)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return \AppBundle\Entity\Serie 
     */
    public function getSerie()
    {
        return $this->serie;
    }
}
