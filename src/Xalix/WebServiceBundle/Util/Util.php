<?php

namespace Xalix\WebServiceBundle\Util;

class Util {

    static public function getSlug($cadena, $separador = '_') {
// Código copiado de http://cubiq.org/the-perfect-php-clean-url-generator
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $cadena);
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = strtolower(trim($slug, $separador));
        $slug = preg_replace("/[\/_|+ -]+/", $separador, $slug);
        $slug = Util::to_camel_case($slug, $capitalise_first_char = false);
        return $slug;
    }

    static private function to_camel_case($str, $capitalise_first_char = false) {
        // para convertir una cadena a CamelCase
        if ($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

}

?>
