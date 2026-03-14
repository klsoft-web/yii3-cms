<?php

namespace App\Web\Auth;

use App\Data\Auth\AuthKeyRepositoryInterface;
use App\Data\Auth\AuthRepositoryInterface;
use App\Data\Auth\CookieLoginIdentity;
use App\Domain\Auth\AuthManagerInterface;
use Klsoft\Yii3User\Login\Cookie\CookieLogin;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Status;
use Yiisoft\Translator\TranslatorInterface;
use App\Messages\App;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final readonly class AuthController
{
    public function __construct(
        private AuthManagerInterface       $authManager,
        private AuthRepositoryInterface    $authRepository,
        private AuthKeyRepositoryInterface $authKeyRepository,
        private CurrentUser                $currentUser,
        private FormHydrator               $formHydrator,
        private CookieLogin                $cookieLogin,
        private TranslatorInterface        $translator,
        private ResponseFactoryInterface   $responseFactory,
        private WebViewRenderer            $viewRenderer)
    {
    }

    public function login(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->currentUser->isGuest()) {
            return $this->homePageResponse();
        }

        $form = new LoginForm();
        if (!$this->formHydrator->populateFromPostAndValidate($form, $request)) {
            return $this->renderLoginForm($request, $form);
        }

        $authResult = $this->authManager->validateCredentialsThenFindIdentity($form->login, $form->password);
        if ($authResult->identity === null ||
            $authResult->user === null) {
            foreach ($authResult->errors as $error) {
                $form->addError($error);
            }
            return $this->renderLoginForm($request, $form);
        } else if (!$this->currentUser->login($authResult->identity)) {
            $form->addError($this->translator->translate(App::SIGN_IN_FAILED));
            return $this->renderLoginForm($request, $form);
        }

        $response = $this->redirectResponse($request);
        if ($form->rememberMe) {
            $this->authManager->refreshAuthKey($authResult->user);
            $response = $this->cookieLogin->addCookie(
                new CookieLoginIdentity(
                    $authResult->identity,
                    $authResult->user,
                    $this->authKeyRepository),
                $response);
        }

        return $response;
    }

    private function homePageResponse(): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse(Status::FOUND)
            ->withHeader('Location', '/');
    }

    private function redirectResponse(ServerRequestInterface $request): ResponseInterface
    {
        $redirectUri = $this->getRedirectUri($request);
        if ($redirectUri !== null) {
            return $this->responseFactory->createResponse()
                ->withStatus(Status::FOUND)
                ->withHeader('Location', $redirectUri);
        }
        return $this->homePageResponse();
    }

    private function getRedirectUri(ServerRequestInterface $request): ?string
    {
        $redirectQueryParameterName = $this->authRepository->getRedirectQueryParameterName();
        $queryParams = $request->getQueryParams();
        if (isset($queryParams[$redirectQueryParameterName])) {
            return $queryParams[$redirectQueryParameterName];
        }
        return null;
    }

    private function renderLoginForm(ServerRequestInterface $request, LoginForm $form): ResponseInterface
    {
        return $this->viewRenderer->render(
            __DIR__ . '/login_template',
            [
                'form' => $form
            ]
        );
    }

    public function logout(): ResponseInterface
    {
        $this->currentUser->logout();
        return $this->cookieLogin->expireCookie($this->homePageResponse());
    }
}
