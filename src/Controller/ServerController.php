<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServerController extends AbstractController
{
    #[Route('/serveur', 'server.index', methods: ['Get'])]
    public function index(): Response
    {
        return $this->render('pages/server/index.html.twig');
    }
}