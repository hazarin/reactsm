<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints;

/**
 * Class CommentController
 * @package App\Controller\Api
 *
 * @Route("/api/comment")
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
     * @Route("/", name="api_comment_list", methods={"GET"})
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
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $items = $this->getDoctrine()->getRepository(Comment::class)->findAll();

        return $this->json($items);
    }

    /**
     * Create comment
     *
     * @Route("/", name="api_comment_create", methods={"POST"})
     * @OA\RequestBody(
     *      description="Create comment",
     *      @Model(type=Comment::class, groups={"set"}),
     * )
     * @OA\Response(
     *     response=201,
     *     description="Comment created",
     *     @Model(type=Comment::class)
     * )
     *
     * @OA\Tag(name="comment")
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = $this->validate();

        if (is_array($data) === false) {
            return $this->json(['error' => (string)$data], 500);
        }

        $comment = new Comment();
        $comment->setText($data['text']);

        try {
            $em->persist($comment);
            $em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json($comment, 201);
    }

    /**
     * Update comment
     *
     * @Route("/{id}/", name="api_comment_update", methods={"PUT"}, requirements={"id"="\d+"})
     *
     * @OA\RequestBody(
     *      description="Update comment",
     *      @Model(type=Comment::class, groups={"set"}),
     * )
     * @OA\Response(
     *     response=200,
     *     description="Comment updated",
     *     @Model(type=Comment::class)
     * )
     *
     * @OA\Tag(name="comment")
     *
     * @param int $id
     * @return JsonResponse
     */
    public function update(int $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);
        if ($comment === null) {
            return $this->json(['error' => 'Object not found'], 404);
        }

        $data = $this->validate();

        if (is_array($data) === false) {
            return $this->json(['error' => (string)$data], 500);
        }

        $comment->setText($data['text']);

        try {
            $em->persist($comment);
            $em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json($comment);
    }

    /**
     * Delete comment
     *
     * @Route("/{id}/", name="api_comment_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @OA\Response(
     *     response=204,
     *     description="Comment deleted",
     * )
     *
     * @OA\Tag(name="comment")
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);
        if ($comment === null) {
            return $this->json(['error' => 'Object not found'], 404);
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
