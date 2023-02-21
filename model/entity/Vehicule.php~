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


    // on part de vehicule -> a cmb de marques ? many vehicle possédés par one marque
    /**
     * @ORM\ManyToOne(targetEntity="Marque", inversedBy="vehicules")
     * @ORM\JoinColumn(name="marque_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Marque
     */

    private Marque $marque;


    /**
     * @ORM\Column(type="string", length="10")
     * @var string
     * 
     */

    private string $color;

    /**
     * @ORM\Column(type="string", name="img_path")
     *
     * @var string
     */
    private string $imgPath;

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

    /**
     * Set marque.
     *
     * @param \Model\Entity\Marque|null $marque
     *
     * @return Vehicule
     */
    public function setMarque(\Model\Entity\Marque $marque = null)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque.
     *
     * @return \Model\Entity\Marque|null
     */
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * Set imgPath.
     *
     * @param string $imgPath
     *
     * @return Vehicule
     */
    public function setImgPath($imgPath)
    {
        $this->imgPath = $imgPath;

        return $this;
    }

    /**
     * Get imgPath.
     *
     * @return string
     */
    public function getImgPath()
    {
        return $this->imgPath;
    }
}
