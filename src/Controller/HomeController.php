<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Template('home/index.html.twig')]
    /**
     * @return array<string, string>
     */
    public function index(): array
    {
        return [];
    }

    #[Route('/about', name: 'app_about')]
    #[Template('home/about.html.twig')]
    /**
     * @return array<string, string>
     */
    public function about(): array
    {
        return [];
    }
}
