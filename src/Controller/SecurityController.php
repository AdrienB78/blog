<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {

    $user = new User;
    $formUser = $this->createForm(RegistrationType::class, $user, [
        'validation_groups' => ['registration']
    ]);
    $formUser->handleRequest($request);

    if($formUser->isSubmitted() && $formUser->isValid())
    {
        $hash = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($hash);
        $user->setRoles(["ROLE_USER"]);
        
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', "Félicitations ! Votre compte est crée, vous pouvez dès à présent vous connecter");
        return $this->redirectToRoute("security_login");
    }

    return $this->render('security/registration.html.twig', [ 'formUser' => $formUser->createView() ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUserName = $authenticationUtils->getLastUserName();
        
        return $this->render('security/login.html.twig', [
            'error' => $error,
            'lastUserName' => $lastUserName
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
        
    }
    
}
