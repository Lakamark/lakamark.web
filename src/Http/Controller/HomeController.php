<?php

namespace App\Http\Controller;

use App\Domain\Blog\Repository\PostRepository;
use App\Domain\Project\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly PostRepository    $postRepository,
        private readonly ProjectRepository $projectRepository
    ) {
    }

    #[Route(path: '/', name: 'home')]
    public function index(): Response
    {
        $posts = $this->postRepository->findRecentPosts(3);
        $projects = $this->projectRepository->findRecentProjects(3);

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'projects' => $projects,
        ]);
    }
}
