<?php

namespace App\Controller\Api;

use App\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints;

/**
 * Class ArticleController
 * @package App\Controller\Api
 *
 * @Route("/api/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @var Request|null
     */
    private ?Request $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    /**
     * List articles
     *
     * @Route("/", name="api_article_list", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Articles list",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Article::class, groups={"list"}))
     *      ),
     * )
     *
     * @OA\Tag(name="article")
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $items = $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->json($items, Response::HTTP_OK, [], ['groups' => ['list']]);
    }

    /**
     * Retrieve article by id
     *
     * @Route("/{id}", name="api_article", methods={"GET"}, requirements={"id"="\d+"})
     * @OA\Response(
     *     response=200,
     *     description="Article",
     *     @Model(type=Article::class, groups={"article"})
     * )
     *
     * @OA\Tag(name="article")
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getArticle(int $id): JsonResponse
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        if ($article === null) {
            return $this->json(['error' => 'Object not found'], 404);
        }

        return $this->json($article, Response::HTTP_OK, [], ['groups' => ['article']]);
    }

    /**
     * Create article
     *
     * @Route("/", name="api_article_create", methods={"POST"})
     * @OA\RequestBody(
     *      description="Create article",
     *      @Model(type=Article::class, groups={"set"}),
     * )
     * @OA\Response(
     *     response=201,
     *     description="Article created",
     *     @Model(type=Article::class)
     * )
     *
     * @OA\Tag(name="article")
     *
     * @IsGranted("ROLE_ADMIN")
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = $this->validate();

        if (is_array($data) === false) {
            return $this->json(['error' => (string)$data], 500);
        }

        $article = new Article();
        $article
            ->setTitle($data['title'])
            ->setContent($data['content']);

        try {
            $em->persist($article);
            $em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json($article, 201);
    }

    /**
     * Update article
     *
     * @Route("/{id}/", name="api_article_update", methods={"PUT"}, requirements={"id"="\d+"})
     *
     * @OA\RequestBody(
     *      description="Update article",
     *      @Model(type=Article::class, groups={"set"}),
     * )
     * @OA\Response(
     *     response=200,
     *     description="Article updated",
     *     @Model(type=Article::class)
     * )
     *
     * @OA\Tag(name="article")
     *
     * @IsGranted("ROLE_ADMIN")
     * @param int $id
     * @return JsonResponse
     */
    public function update(int $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        if ($article === null) {
            return $this->json(['error' => 'Object not found'], 404);
        }

        $data = $this->validate();

        if (is_array($data) === false) {
            return $this->json(['error' => (string)$data], 500);
        }

        $article
            ->setTitle($data['title'])
            ->setContent($data['content']);

        try {
            $em->persist($article);
            $em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json($article);
    }

    /**
     * Delete article
     *
     * @Route("/{id}/", name="api_article_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @OA\Response(
     *     response=204,
     *     description="Article deleted",
     * )
     *
     * @OA\Tag(name="article")
     *
     * @IsGranted("ROLE_ADMIN")
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        if ($article === null) {
            return $this->json(['error' => 'Object not found'], 404);
        }

        try {
            $em->remove($article);
            $em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json(null, 204);
    }

    /**
     * @return ConstraintViolationListInterface|array
     */
    private function validate(): ?array
    {
        $data = json_decode($this->request->getContent(), true);
        $validator = Validation::createValidator();
        $constraint = new Constraints\Collection([
            'title' => new Constraints\Length(['min' => 1]),
            'content' => new Constraints\Length(['min' => 1]),
        ]);
        $violations = $validator->validate($data, $constraint);

        if ($violations->count() > 0) {
            return $violations;
        }

        return $data;
    }
}
