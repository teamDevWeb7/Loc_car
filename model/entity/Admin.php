<?php
namespace Model\Entity;

// pr use le mapping
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\table(name="administrateur")
 */
class Admin{
    /**
     * on dit que c'est clé primaire
     * @ORM\Id
     * on définit le type
     * @ORM\Column(type="integer", name="id")
     * pour que doctrine génère tout seul les id 
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private int $id;


    /**
     * @ORM\Column(type="string", length=50)
     *
     * @var string
     */
    private string $name;


    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private string $password;


    /**
     * @ORM\Column(type="string", length=100)
     *
     * @var string
     */
    private string $mail;


    /**
     * @ORM\Column(type="string", length=50)
     *
     * @var string
     */
    private string $phone;



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
     * @return Admin
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
     * Set password.
     *
     * @param string $password
     *
     * @return Admin
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set mail.
     *
     * @param string $mail
     *
     * @return Admin
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail.
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return Admin
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
