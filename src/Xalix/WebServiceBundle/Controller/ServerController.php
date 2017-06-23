<?php

namespace Xalix\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ServerController extends Controller {

    public function soapAction($name, Request $request) {

        $em = $this->getDoctrine()->getManager();

        $service = null;

        try {
            $service = $em->getRepository('WebServiceBundle:WebService')->findOneWebServiceByProtocol($name, 'SOAP');
        } catch (\Exception $e) {
            
        }

        if (!$service) {
            throw $this->createNotFoundException('Unable to find Web Service.');
        }

        $protocol = $em->getRepository('WebServiceBundle:Protocol')->findOneBy(array(
            'name' => 'SOAP',
        ));
        if ($protocol->getIsActive() && $service->getIsActive()) {
            
            $result = preg_match("/wsdl$/i", $request->getRequestUri());
            file_put_contents('fulano.http', $request->headers);
            if ($result && $request->getMethod()=='GET') {
                $response = new Response();
                $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');
                $response->setContent(file_get_contents(__DIR__ . '/../WebServices/Contrates/soap/'.$name.'.wsdl'));
                return $response;
            }

            $this->container->get('service.security')->setEntity('SOAP', $service)->isCorrect();

            /* Una simple prueba de como tratar con SOAP service */
            $soap_server = new \SoapServer(__DIR__ . '/../WebServices/Contrates/soap/' . $name . '.wsdl');
            //eval('$obj = new WebServiceBundle\$name();');
            //$name = 'WebServiceBundle\\'.$name;
            // include_once(dirname(__FILE__) . "/../$name.php");
            // eval('$obj = new \\$name();');
            $name = "Xalix\\WebServiceBundle\\WebServices\\ServicesClass\\$name";
            $obj = new $name();
            // echo $obj->lasuma(7, 3);
            // $obj = new \Mio();
            $soap_server->setObject($obj);

            /*
             * ob_start() y ob_get_clean(). 
             * Estos métodos controlan la salida almacenada temporalmente lo cual te 
             * permite «atrapar» la salida difundida por $server->handle(). 
             * Esto es necesario porque Symfony espera que el controlador devuelva un 
             * objeto Respuesta con la salida como «contenido». También debes recordar 
             * establecer la cabecera Content-Type a text/xml, ya que esto es lo que espera 
             * el cliente. Por lo tanto, utiliza ob_start() para empezar a almacenar la 
             * STDOUT y usa ob_get_clean() para volcar la salida difundida al contenido 
             * de la Respuesta y limpiar la memoria de salida. Por último, estás listo 
             * para devolver la Respuesta.
             */
            $response = new Response();
            $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');
            ob_start();
            $soap_server->handle();
            $response->setContent(ob_get_clean());
            return $response;
        } else {

            //Debo buscar que código colocar en el soapfault para
            //devolverle al cliente un error decente.
            return $this->render('WebServiceBundle:WebService:notavailable.html.twig');
        }
        // die;
        //  echo call_user_func_array(array('Mio','lasuma'), array('num1'=>1,'num2'=>2));
    }

}
