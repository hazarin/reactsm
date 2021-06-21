<?php

namespace App\Controller\Api;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AuthController
 * @package App\Controller\Api
 *
 * @Route("/api/auth")
 */
class AuthController extends AbstractController
{
    /**
     * @var Request|null
     */
    private ?Request $request;

    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(RequestStack $request, UserPasswordHasherInterface $passwordHasher)
    {
        $this->request = $request->getCurrentRequest();
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Register new user.
     *
     * @Route("/register", name="api_auth_register", methods={"POST"})
     * @OA\Response(
     *     response=200,
     *     description="Registration success",
     * )
     * @OA\RequestBody(
     *      description="Registered user info",
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              type="object",
     *              required={"email", "password"},
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="last_name",
     *                  type="string",
     *              ),
     *          ),
     *      ),
     * )
     *
     * @OA\Tag(name="auth")
     *
     * @return JsonResponse
     */
    public function register(): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($this->request->getContent(), true);
        $validator = Validation::createValidator();
        $constraint = new Constraints\Collection([
            'password' => new Constraints\Length(['min' => 1]),
            'email' => new Constraints\Email(),
        ]);
        $violations = $validator->validate($data, $constraint);

        if ($violations->count() > 0) {
            return $this->json(["error" => (string)$violations], 500);
        }

        $user = new User();
        $user
            ->setEmail($data['email'])
            ->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

        if (array_key_exists('name', $data)) {
            $user->setName($data['name']);
        }

        if (array_key_exists('last_name', $data)) {
            $user->setLastName($data['last_name']);
        }

        try {
            $em->persist($user);
            $em->flush();
        } catch (\Exception $e) {
            return $this->json(["error" => $e->getMessage()], 500);
        }

        return $this->json(['success' => $user->getUserIdentifier().' registered']);
    }

    /**
     * Retrieve user profile .
     *
     * @Route("/profile", name="api_auth_profile", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="User profile",
     *     @Model(type=User::class, groups={"article"})
     * )
     *
     * @OA\Tag(name="auth")
     *
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return JsonResponse
     */
    public function profileAction(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json($user, Response::HTTP_OK, [], ['groups' => ['article']]);
    }
}
