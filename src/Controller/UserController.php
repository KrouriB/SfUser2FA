<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Codes;
use App\Service\Mailer;
use App\Service\UserAuthCheck;
use App\Service\HashedPassword;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('user/login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/code', name: 'app_code')]
    public function code(): Response
    {
        return $this->render('user/code.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/resend', name: 'app_resend')]
    public function resend(): Response
    {
        return $this->render('user/resend.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('user/homePage.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    public function checkUser(string $email, UserRepository $userRepository, UserAuthCheck $userAuthCheck)
    {
        $users = $userRepository->findAll();
        $user = $userAuthCheck->userListCheck($email, $users);
        return $user;
    }

    public function checkPassword(string $password, User $user, UserAuthCheck $userAuthCheck, HashedPassword $hashedPassword)
    {
        $password = $hashedPassword->hashedPassword($password);
        $check = $userAuthCheck->passwordCheck($user, $password);
        return $check;
    }

    public function sendCode(User $user, Codes $codes, Mailer $mailer, Session $session)
    {
        $code = $codes->generate();
        $mailer->sendCodeTo($user, $code);
        $session->stockCode($code);
        return $this->code();
    }

    public function checkCode(int $code, UserAuthCheck $userAuthCheck, Codes $codes)
    {
        $check = $userAuthCheck->codeCheck($code, $codes->retrive());
        return $check;
    }

    public function connected(User $user)
    {
        // connecter l'utilisateur
    }

    public function firstCheck(string $email, string $password, UserRepository $userRepository, UserAuthCheck $userAuthCheck, HashedPassword $hashedPassword, Codes $codes, Mailer $mailer, Session $session)
    {
        $user = $this->checkUser($email, $userRepository, $userAuthCheck);
        if($user == false)
        {
            $session->discardUser();
            return $this->login();
        }
        $check = $this->checkPassword($password, $user, $userAuthCheck, $hashedPassword);
        if($check == false)
        {
            $session->discardUser();
            return $this->login();
        }
        $session->stockUser($user);
        $this->sendCode($user, $codes, $mailer, $session);
    }

    public function secondCheck(int $code, UserAuthCheck $userAuthCheck, Codes $codes, Session $session)
    {
        $user = $session->retriveUser();
        $check = $this->checkCode($code, $userAuthCheck, $codes);
        $session->discardCode();
        if($check == false)
        {
            return $this->resend();
        }
        $this->connected($user);
        return $this->render('user/homePage.html.twig');
    }

    public function resended(bool $response, Codes $codes, Mailer $mailer, Session $session)
    {
        $user = $session->retriveUser();
        if($response == true)
        {
            $this->sendCode($user, $codes, $mailer, $session);
        }
        else
        {
            $session->discardUser();
            return $this->login();
        }
    }
}
