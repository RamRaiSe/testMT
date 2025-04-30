<?php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Contracts\Service\Attribute\Required;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    #[Required]
    public EntityManagerInterface $entityManager;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function supports(Request $request): ?bool
    {
        return $request->getMethod() === 'POST' && $request->getPathInfo() === '/api/login';
    }

    public function authenticate(Request $request): Passport
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password'])) {
            throw new AuthenticationException('Email и пароль обязательны');
        }

        $email = $data['email'];
        $password = $data['password'];

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw new AuthenticationException('Пользователь не найден');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Неверный пароль');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?\Symfony\Component\HttpFoundation\Response
    {
        return new JsonResponse(['error' => $exception->getMessage()], 401);
    }

    public function onAuthenticationSuccess(Request $request, \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, $providerKey): ?\Symfony\Component\HttpFoundation\Response
    {
        return new JsonResponse(['success' => 'OK'], 200);
    }
}
