<?php
declare(strict_types = 1);

namespace App\Core\Infrastructure\Action\Api\User;

use App\Core\Application\Exceptions\InvalidEntityException;
use App\Core\Application\Services\UserService;
use App\Core\Application\Services\ValidationService;
use App\Core\Domain\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class CreateAction
 * @package App\Core\Infrastructure\Action\Api\User
 * @author  Henrique Vasconcelos <henriquenvasconcelos@gmail.com>
 *
 * @Route("/user", name="createUser", methods={"POST"})
 */
class CreateAction
{

    /**
     * @var \App\Core\Application\Services\UserService
     */
    private UserService $userService;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * CreateAction constructor.
     *
     * @param \App\Core\Application\Services\UserService $userService
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     */
    public function __construct(
        UserService $userService,
        SerializerInterface $serializer
    ) {
        $this->userService = $userService;
        $this->serializer  = $serializer;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $content  = $request->getContent();
        $userData = $this->serializer->deserialize($content, User::class, 'json');

        try {
            $user = $this->userService->create($userData);
        } catch (InvalidEntityException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'errors'  => $e->errors
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        return new JsonResponse($user, Response::HTTP_CREATED);
    }

}
