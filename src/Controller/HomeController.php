<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Twig\Attribute\Template;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Template('home/index.html.twig')]
    /**
     * @return array<string, string>
     */
    public function index(): array
    {
        return [
            'controller_name' => 'HomeController',
        ];
    }

    #[Route('/about', name: 'app_about')]
    #[Template('home/about.html.twig')]
    public function about(): array
    {
        return [
            'controller_name' => 'HomeController',
        ];
    }
}
