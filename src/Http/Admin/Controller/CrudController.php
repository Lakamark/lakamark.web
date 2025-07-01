<?php

namespace App\Http\Admin\Controller;

use App\Domain\Application\Entity\Content;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Admin\Data\CrudDataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * The master CRUD class all the child CRUD controller extends
 */
class CrudController extends BaseController
{
    /**
     * To define the entity the CrudController
     * We need this property to interact with the EntityManagerInterface
     *
     * @var string
     */
    protected string $entityClass = Content::class;

    /**
     * The directory where your template
     * e.g., admin/blog/index.html.twig
     *
     * @var string
     */
    protected string $template = 'blog';

    /**
     * The name will display in the administration menu
     *
     * @var string
     */
    protected string $menuLabel = '';

    /**
     * The url prefix
     * e.g.. admin_blog
     *
     * @var string
     */
    protected string $routePrefix = '';

    /**
     * To define which field, you want to apply some filters
     *
     * @var string
     */
    protected string $searchableColumn = 'title';

    /**
     * By default, when the entity manger flushed your data,
     * we redirect to the index page.
     * If you want to disable the default redirection,
     * when you're saved your content.
     *
     * @var bool
     */
    protected bool $indexOnSave = true;

    /**
     * The event list where you can add some logic when the event is emitted,
     * For example, to edit the createdAt field
     * To delete some file before to delete the entity
     * @var array|null[]
     */
    protected array $crudEvents = [
        'onUpdate'=> null,
        'onDelete'=> null,
        'onCreate'=> null,
    ];

    /**
     * The default confirmation message
     * when you are successfully safe your data.
     * You can override this array on the child controller class
     * @var array|string[]
     */
    protected array $confirmationMessages = [
        'editMessage' => 'Your changes have been updated.',
        'createdMessage' => 'Your changes have been saved.',
        'deleteMessage' => 'Your content has been deleted.',
    ];

    public function __construct(
        protected EntityManagerInterface $em,
        protected PaginatorInterface $paginator,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly RequestStack $requestStack,
    ) {

    }

    /**
     * To display the listing contents
     *
     * @param QueryBuilder|null $query
     * @param array $combinedParams
     * @return Response
     */
    public function actionCrudIndex(?QueryBuilder $query = null, array $combinedParams = []): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $query = $query ?: $this->getRepository()
            ->createQueryBuilder('row')
            ->orderBy('row.createdAt', 'DESC');

        // If thy are a search (q) params in the url,
        // we edit the query
        if ($request->get('q')) {
            $criteria = trim((string) $request->get('q'));
            $query = $this->addSearch($criteria, $query);
        }

        // Paginate the rows
        $this->paginator->allowShort('row.id', 'row.title');
        $rows = $this->paginator->paginate($query->getQuery());

        return $this->render("admin/$this->template/index.html.twig", [
            'rows' => $rows,
            'searchableColumn' => true,
            'menuLabel' => $this->menuLabel,
            'prefix' => $this->routePrefix,
            ...$combinedParams,
        ]);
    }

    /**
     * To update a content
     *
     * @param CrudDataInterface $crudData
     * @param array $extraData
     * @return Response
     */
    public function actionCrudEdit(CrudDataInterface $crudData, array $extraData = []): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $form = $this->createForm($crudData->getFormObject(), $crudData);
        $form->handleRequest($request);

        // Verify the entity data
        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $crudData->getEntity();
            $oldEntity = clone $entity;
            $crudData->objectHydrator();
            $this->em->flush();

            // We dispatch onUpdate event
            if ($this->crudEvents['onUpdate'] ?? null) {
                $this->dispatcher->dispatch(new $this->crudEvents['onUpdate']($entity, $oldEntity));
            }

            $this->addFlash('success', $this->confirmationMessages['editMessage']);

            return $this->redirectAfterSave($entity);
        }

        return $this->render("admin/$this->template/edit.html.twig", [
            'form' => $form->createView(),
            'entity' => $this->getRepository(),
            'menuLabel' => $this->menuLabel,
            ...$extraData,
        ]);
    }

    /**
     * Create a new content
     *
     * @param CrudDataInterface $crudData
     * @param array $extraData
     * @return Response
     */
    public function actionCrudNew(CrudDataInterface $crudData, array $extraData = []): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $form = $this->createForm($crudData->getFormObject(), $crudData);
        $form->handleRequest($request);

        // Verify the entity data
        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $crudData->getEntity();
            $crudData->objectHydrator();
            $this->em->persist($entity);
            $this->em->flush();

            // We dispatch onCreate event
            if ($this->crudEvents['onCreate'] ?? null) {
                $this->dispatcher->dispatch(new $this->crudEvents['onCreate']($crudData->getEntity()));
            }

            $this->addFlash('success', $this->confirmationMessages['createdMessage']);

            return $this->redirectAfterSave($entity);
        }

        return $this->render("admin/$this->template/new.html.twig", [
            'form' => $form->createView(),
            'entity' => $this->getRepository(),
            'menuLabel' => $this->menuLabel,
            ...$extraData,
        ]);
    }

    public function actionCrudDelete(object $entity, ?string $redirectRoute = null): RedirectResponse
    {
        // We delete from the EntityMangerInterface the entity
        $this->em->remove($entity);

        // Before to flush, we emit an event OnDelete,
        // If you want to delete some files or any media before to delete this entity.
        if ($this->entityEvents['onDelete'] ?? null) {
            $this->dispatcher->dispatch(new $this->crudEvents['onDelete']($entity));
        }

        // We delete the entity of the database
        $this->em->flush();
        $this->addFlash('success', $this->confirmationMessages['deleteMessage']);

        // We redirect the user to the index page.
        return $this->redirectToRoute($redirectRoute ?: ($this->routePrefix.'_index'));
    }

    /**
     * Get the repository from the entity property
     *
     * @return EntityRepository
     */
    public function getRepository(): EntityRepository
    {
        /* @var EntityRepository */
        return $this->em->getRepository($this->entityClass);
    }

    /**
     * To add a search conditions in the current query
     *
     * @param string $search
     * @param QueryBuilder $query
     * @return QueryBuilder
     */
    protected function addSearch(string $search, QueryBuilder $query): QueryBuilder
    {
        return $query
            ->where("LOWER(row.$this->searchableColumn) LIKE :search")
            ->setParameter('search', '%'.strtolower($search).'%');
    }

    /**
     * If the property $indexOnSave is true,
     * we redirect to the index page
     *
     * Else,
     * We redirect to edit page we transmit the entity to the view
     * @param $entity
     * @return RedirectResponse
     */
    protected function redirectAfterSave($entity): RedirectResponse
    {
        if ($this->indexOnSave) {
            return $this->redirectToRoute($this->routePrefix.'index');
        }

        return $this->redirectToRoute($this->routePrefix.'_edit', ['id' => $entity->getId()]);
    }
}
