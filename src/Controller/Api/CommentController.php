<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\Comment;
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
 * Class CommentController
 * @package App\Controller\Api
 */
class CommentController extends AbstractController
{
    /**
     * @var ?Request $request
     */
    private ?Request $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }


    /**
     * List comments
     *
     * @Route("/api/article/{article_id}/comment/", name="api_comment_list", methods={"GET"}, requirements={"article_id"="\d+"})
     * @OA\Response(
     *     response=200,
     *     description="Comments list",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Comment::class))
     *      ),
     * )
     *
     * @OA\Tag(name="comment")
     * @return JsonResponse
     */
    public function list(int $article_id): JsonResponse
    {
        $items = $this
            ->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(['article_id' => $article_id]);

        return $this->json($items);
    }

    /**
     * Create comment
     *
     * @Route("/api/article/{article_id}/comment/", name="api_comment_create", methods={"POST"}, requirements={"article_id"="\d+"})
     * @OA\RequestBody(
     *      description="Create comment",
     *      @Model(type=Comment::class, groups={"set"}),
     * )
     * @OA\Response(
     *     response=201,
     *     description="Comment created",
     *     @Model(type=Comment::class, groups={"article"})
     * )
     *
     * @OA\Tag(name="comment")
     * @Security(name="bearer")
     *
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param int $article_id
     * @return JsonResponse
     */
    public function create(int $article_id): JsonResponse
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($article_id);
        if ($article === null) {
            return $this->json(['error' => 'Article not found'], 404);
        }

        $data = $this->validate();

        if (is_array($data) === false) {
            return $this->json(['error' => (string)$data], 500);
        }

        $comment = new Comment();
        $comment
            ->setUser($user)
            ->setArticle($article)
            ->setText($data['text']);

        try {
            $em->persist($comment);
            $em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json($comment, Response::HTTP_CREATED, [], ['groups' => ['article']]);
    }

    /**
     * Update comment
     *
     * @Route("/api/comment/{id}/", name="api_comment_update", methods={"PUT"}, requirements={"id"="\d+"})
     *
     * @OA\RequestBody(
     *      description="Update comment",
     *      @Model(type=Comment::class, groups={"set"}),
     * )
     * @OA\Response(
     *     response=200,
     *     description="Comment updated",
     *     @Model(type=Comment::class, groups={"article"})
     * )
     *
     * @OA\Tag(name="comment")
     *
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param int $id
     * @return JsonResponse
     */
    public function update(int $id): JsonResponse
    {
        $data = $this->validate();

        if (is_array($data) === false) {
            return $this->json(['error' => (string)$data], 500);
        }

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);
        if ($comment === null) {
            return $this->json(['error' => 'Object not found'], 404);
        }
        if ($this->isGranted('ROLE_ADMIN') === false && $comment->getUser() !== $user) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        $comment->setText($data['text']);

        try {
            $em->persist($comment);
            $em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json($comment, Response::HTTP_CREATED, [], ['groups' => ['article']]);
    }

    /**
     * Delete comment
     *
     * @Route("/api/comment/{id}/", name="api_comment_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @OA\Response(
     *     response=204,
     *     description="Comment deleted",
     * )
     *
     * @OA\Tag(name="comment")
     *
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);
        if ($comment === null) {
            return $this->json(['error' => 'Object not found'], 404);
        }
        if ($this->isGranted('ROLE_ADMIN') === false && $comment->getUser() !== $user) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        try {
            $em->remove($comment);
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
            'text' => new Constraints\Length(['min' => 1]),
        ]);
        $violations = $validator->validate($data, $constraint);

        if ($violations->count() > 0) {
            return $violations;
        }

        return $data;
    }
}
