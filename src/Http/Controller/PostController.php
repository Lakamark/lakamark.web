<?php

namespace App\Http\Controller;

use App\Domain\Blog\Entity\Category;
use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Repository\CategoryRepository;
use App\Domain\Blog\Repository\PostRepository;
use App\Http\Requirements;
use Doctrine\ORM\Query;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    #[Route(path: '/blog', name: 'blog_index')]
    public function index(Request $request): Response
    {
        $title = 'Blog';
        $query = $this->postRepository->queryAll();

        return $this->listingRender($title, $query, $request);
    }

    #[Route(path: '/blog/category/{slug:category}', name: 'blog_category', requirements: ['slug' => Requirements::SLUG])]
    public function category(Category $category, Request $request): Response
    {
        $title = $category->getName();
        $query = $this->postRepository->queryAll($category);

        return $this->listingRender($title, $query, $request, ['category' => $category]);
    }

    #[Route(path: '/blog/{slug:post}', name: 'blog_show', requirements: ['slug' => Requirements::SLUG])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    private function listingRender(string $title, Query $query, Request $request, array $params = []): Response
    {
        $page = $request->query->getInt('page', 1);
        $posts = $this->paginator->paginate(
            $query,
            $page,
            10
        );
        if ($page > 1) {
            $title .= ", page $page";
        }

        if (0 === $posts->count()) {
            throw new NotFoundHttpException('No articles found.');
        }

        $categories = $this->categoryRepository->findWithCount();

        return $this->render('post/index.html.twig', array_merge([
            'posts' => $posts,
            'categories' => $categories,
            'title' => $title,
            'page' => $page,
        ], $params));
    }
}
