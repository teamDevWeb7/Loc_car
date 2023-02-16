<?php

namespace Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="marque")
 * @ORM\Entity
 */

class Marque{

    // marque a cmb de vehicule ?, mappedBy -> renseignée dans l'autre table sous quelle var ?     * @ORM\OneToMany(targetEntity="Vehicule", mappedBy(="marque"))
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")

     * @var integer
     */
    private int $id;


    /**
     * @ORM\Column(type="string", name="name", length="55")
     * @var string
     */

     private string $name;

    /**
     * @ORM\OneToMany(targetEntity="Vehicule", mappedBy="marque")
     *
     */
     private $vehicules;


    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Marque
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Marque
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->vehicules = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add vehicule.
     *
     * @param \Model\Entity\Vehicule $vehicule
     *
     * @return Marque
     */
    public function addVehicule(\Model\Entity\Vehicule $vehicule)
    {
        $this->vehicules[] = $vehicule;

        return $this;
    }

    /**
     * Remove vehicule.
     *
     * @param \Model\Entity\Vehicule $vehicule
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVehicule(\Model\Entity\Vehicule $vehicule)
    {
        return $this->vehicules->removeElement($vehicule);
    }

    /**
     * Get vehicules.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVehicules()
    {
        return $this->vehicules;
    }
}
