<?php

namespace Xalix\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller {

    public function indexAction() {

        return $this->render('WebServiceBundle:Default:index.html.twig');
    }

    public function setLocaleAction(Request $request) {
        $locale = $request->getLocale();
        $setter = ($locale == 'es') ? 'en' : 'es';
        $route = $request->server->get('HTTP_REFERER');
        if ($setter == 'en') {
            $route = str_replace('/es/', '/en/', $route);
        } else {
            $route = str_replace('/en/', '/es/', $route);
        }
        return new RedirectResponse($route, 302);
    }

    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
                        'WebServiceBundle:Default:login.html.twig', array(
                    // last username entered by the user
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error' => $error,
                        )
        );
    }

}
