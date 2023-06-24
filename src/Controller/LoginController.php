<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{

    private AuthenticationUtils $autenticationUtils;
    
    public function __construct(AuthenticationUtils $autenticationUtils)
    {
        $this->autenticationUtils = $autenticationUtils;
    }
    
    public function login(): Response
    {
        $error = $this->autenticationUtils->getLastAuthenticationError();
        
        $lastUsername = $this->autenticationUtils->getLastUsername();
        
        return $this->render('login/index.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername
        ]);
    }
}
