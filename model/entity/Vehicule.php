<?php

namespace Model\Entity;

use Doctrine\ORM\Mapping  as ORM;

// entity regroupe les model des modules

// dire que ce qu'il y a en bas c'est une table, parenthèse car on peut préciser, si nom table !nom objet ca se fait llà
/**
 * @ORM\Table(name="vehicule")
 * @ORM\Entity
 */

class Vehicule{
    // strategy pr mysql identity -> dernier id +1
    /**
     * @ORM\Id
     *@ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    private int $id;


    /**
     * @ORM\Column(type="string", name="modele", length="55")
     * @var string
     */

    private string $model;


    /**
     * @ORM\Column(type="string", length="55")
     * @var string
     */

    private string $marque;


    /**
     * @ORM\Column(type="string", length="10")
     * @var string
     * 
     */

    private string $color;

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
     * Set model.
     *
     * @param string $model
     *
     * @return Vehicule
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set marque.
     *
     * @param string $marque
     *
     * @return Vehicule
     */
    public function setMarque($marque)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque.
     *
     * @return string
     */
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * Set color.
     *
     * @param string $color
     *
     * @return Vehicule
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }
}
