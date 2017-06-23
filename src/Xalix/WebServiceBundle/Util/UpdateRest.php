<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Xalix\WebServiceBundle\Util;

/**
 * Description of newPHPClass
 *
 * @author Leox
 */
class UpdateRest {


    public static function updatePublishRestServices($em, $dir) {
        /* Aqui se debe implementar la parte de publicar todos los servicios 
          Rest que esten publicos.
          Esto tiene como sentido actualizar los servicios rest publicados.
          si se desactiva uno se debe actualizar el fichero routes.yml con este método al
          igual que si se activa. */
        $entities = $em->getRepository('WebServiceBundle:WebService')->findRestServicesPublish();
        $return = "";
        $i = 0;
        if (!$entities) {
            $return .= 'xalix_ws_notavailable:' . "\n";
            $return .= "    " . 'pattern: /notavailable' . "\n";
            $return .= "    " . "defaults: { _controller: 'WebServiceBundle:WebService:notavailable' }";
        } else {
            foreach ($entities as $e) {
                $resource = 'Xalix\WebServiceBundle\WebServices\RESTControllers\\' . $e->getSlug() . 'Controller';
                $return .= 'xalix_ws_' . $e->getSlug() . ':' . "\n";
                $return .= "    " . 'type: rest' . "\n";
                $return .= "    " . 'prefix: /webservices/' . $e->getSlug() . "\n";
                $return .= "    " . "resource: " . "'" . $resource . "'" . "\n";
                $return .= "    " . 'name_prefix: api_' . $i . '_' . "\n";
                $return .= "\n";
                $i += 1;
            }
        }
        //$return = "";
        try {
            file_put_contents(__DIR__ . '/../Resources/config/routes.yml', $return);
        } catch (\Exception $e) {
            throw new \Exception('Permisions denied in /Resources/config/ folder in WebServiceBundle');
        }
        
            /* Esta línea es necesaria, porque en el entorno de producción este archivo genera las rutas
            determinadas en los yml, y como esta clase actualiza el fichero de rutas es necesario que symfony
            actualize esas rutas en la cache. En otras palabras que reconstruya este archivo cuando se produzca 
            petición. */
            @unlink($dir.'prod/appProdUrlMatcher.php');
    }

}

?>
