<?php

namespace App\Http\Controller;

use App\Domain\Project\Entity\Project;
use App\Domain\Project\Repository\ProjectRepository;
use App\Http\Requirements;
use Doctrine\ORM\Query;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    #[Route(path: '/project', name: 'project_index')]
    public function index(Request $request): Response
    {
        $title = "Projects";
        $query = $this->projectRepository->queryAll();
        return $this->listingRender($title, $query, $request);
    }

    #[Route(path: '//project/{slug:project}', name: 'project_show', requirements: ['slug' => Requirements::SLUG])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    private function listingRender(string $title, Query $query, Request $request, array $params = []): Response
    {
        $page = $request->query->getInt('page', 1);
        $projects = $this->paginator->paginate(
            $query,
            $page,
            10
        );
        if ($page > 1) {
            $title .= ", page $page";
        }

        if (0 === $projects->count()) {
            throw new NotFoundHttpException('No project found.');
        }


        return $this->render('project/index.html.twig', array_merge([
            'projects' => $projects,
            'title' => $title,
            'page' => $page,
        ], $params));
    }
}
