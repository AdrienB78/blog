<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity( fields = {"email"}, message = "Un compte est déjà existant à cette adresse mail" )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank( message = "Veuillez renseigner une adresse email !" )
     * @Assert\Email( message = "Veuillez saisir une adresse email valide !" )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank( message = "Veuillez renseigner un nom d'utilisateur !" )
     * @Assert\Length( min =2, max =50, minMessage = "Nom d'utilisateur trop court", maxMessage = "Nom d'utilisateur trop long")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank( message = "Veuillez renseigner un mot de passe !", groups = {"registration"} )
     * @Assert\EqualTo( propertyPath = "confirm_password", message = "Les mots de passes ne correspondent pas !", groups = {"registration"} )
     */
    private $password;

    /**
     * @Assert\NotBlank( message = "Veuillez renseigner un mot de passe de confirmation !", groups = {"registration"} )
     * @Assert\EqualTo( propertyPath = "password", message = "Les mots de passes ne correspondent pas !", groups = {"registration"} )
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials()
    {
    
    }

    public function getSalt()
    {

    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
