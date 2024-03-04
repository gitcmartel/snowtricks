<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class IdentificationSubscriber implements EventSubscriberInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onLogout(LogoutEvent $event)
    {
        $session = $this->requestStack->getSession();
        $message = 'Disconnection success !';
        
        $session->set('toastMessage', $message);
        $session->save();
    }

    public function onLogin(LoginSuccessEvent $event)
    {
        $session = $this->requestStack->getSession();
        $message = 'Connection success !';
        
        $session->set('toastMessage', $message);
        $session->save();
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogout',
            LoginSuccessEvent::class => 'onLogin'
        ];
    }
}
