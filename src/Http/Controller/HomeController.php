<?php

namespace App\Http\Controller;

use App\Domain\Blog\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'home')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findRecentPosts(3);

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
