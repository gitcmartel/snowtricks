<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class ToastService
{
   
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function setMessage(string $message) {
        $session = $this->requestStack->getSession();
        
        $session->set('toastMessage', $message);
        $session->save();
    }
}