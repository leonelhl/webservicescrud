<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\RestBundle\EventListener;

use FOS\RestBundle\Decoder\DecoderProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * This listener handles Request body decoding.
 *
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
class BodyListener {

    /**
     * @var DecoderProviderInterface
     */
    private $decoderProvider;

    /**
     * @var boolean
     */
    private $throwExceptionOnUnsupportedContentType;

    /**
     * Constructor.
     *
     * @param DecoderProviderInterface $decoderProvider Provider for fetching decoders
     * @param boolean $throwExceptionOnUnsupportedContentType
     */
    public function __construct(DecoderProviderInterface $decoderProvider, $throwExceptionOnUnsupportedContentType = false) {
        $this->decoderProvider = $decoderProvider;
        $this->throwExceptionOnUnsupportedContentType = $throwExceptionOnUnsupportedContentType;
    }

    /**
     * Core request handler
     *
     * @param GetResponseEvent $event The event
     * @throws BadRequestHttpException
     * @throws UnsupportedMediaTypeHttpException
     */
    public function onKernelRequest(GetResponseEvent $event) {
        $request = $event->getRequest();

        /*
         * @by LeoX
         * Lineas que excluyen si la petici�n es SOAP, pues el RESTBundle interfiere
         * cuando le llega una peticion SOAP por el POST, con esta condici�n se evita esto.
         */
        $SOAPAction = $request->headers->get('SOAPAction');
        if (!$SOAPAction) {

            if (!count($request->request->all()) && in_array($request->getMethod(), array('POST', 'PUT', 'PATCH', 'DELETE'))
            ) {
                $contentType = $request->headers->get('Content-Type');

                $format = null === $contentType ? $request->getRequestFormat() : $request->getFormat($contentType);

                if (!$this->decoderProvider->supports($format)) {
                    if ($this->throwExceptionOnUnsupportedContentType) {
                        throw new UnsupportedMediaTypeHttpException("Request body format '$format' not supported");
                    }

                    return;
                }

                $decoder = $this->decoderProvider->getDecoder($format);
                $content = $request->getContent();

                if (!empty($content)) {
                    $data = $decoder->decode($content, $format);
                    if (is_array($data)) {
                        $request->request = new ParameterBag($data);

                        // Reset the method in the current request to support method-overriding
                        $request->setMethod($request->getRealMethod());
                    } else {
                        throw new BadRequestHttpException('Invalid ' . $format . ' message received');
                    }
                }
            }
        }
    }

}
