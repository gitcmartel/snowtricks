<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use App\Service\ToastService;

class IdentificationSubscriber implements EventSubscriberInterface
{
    private ToastService $toastService;

    public function __construct(RequestStack $requestStack)
    {
        $this->toastService = new ToastService($requestStack);
    }

    public function onLogout(LogoutEvent $event)
    {
        $this->toastService->setMessage('Disconnection success !', 'success');
    }

    public function onLogin(LoginSuccessEvent $event)
    {
        $this->toastService->setMessage('Connection success !', 'success');
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogout',
            LoginSuccessEvent::class => 'onLogin'
        ];
    }
}
