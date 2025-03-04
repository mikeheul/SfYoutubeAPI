<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class RemoveNoIndexListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        dd($response);
        if ($response->headers->has('X-Robots-Tag')) {
            $response->headers->remove('X-Robots-Tag');
        }
    }
}
