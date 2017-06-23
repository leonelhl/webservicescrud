$(function() {

    var SOAPselec;
    var RESTselec;

    if ($('h1').html() === 'Adicionar Servicio Web') {
        if ($('#webservice_protocol').children().eq(1).attr('selected')) {
            var SOAPselec = false;
            var RESTselec = true;
        } else {
            if ($('#webservice_protocol').children().eq(0).attr('selected')) {
                var SOAPselec = true;
                var RESTselec = false;
            } else {
                if ($('#webservice_protocol').children().eq(0).attr('selected') && $('#webservice_protocol').children().eq(1).attr('selected')) {
                    var SOAPselec = true;
                    var RESTselec = true;
                } else {
                    var SOAPselec = false;
                    var RESTselec = false;
                }
            }
        }
    } else {
        if ($('#webservice_protocol').children().eq(1).attr('selected')) {
            var SOAPselec = false;
            var RESTselec = true;
        } else {
            if ($('#webservice_protocol').children().eq(0).attr('selected')) {
                var SOAPselec = true;
                var RESTselec = false;
            } else {
                var SOAPselec = true;
                var RESTselec = true;
            }
        }
    }

    multiselec('protocol');

    function multiselec(clas) {
        $('.' + clas).multiselect({
            buttonClass: 'mybutton btn btn-primary btn-small',
            buttonText: function(options, select) {
                if (options.length == 0) {
                    return 'Seleccione.. <b class="caret"></b>';
                }
                else {
                    if (options.length > this.numberDisplayed) {
                        return options.length + ' ' + this.nSelectedText + ' <b class="caret"></b>';
                    }
                    else {
                        var selected = '';
                        options.each(function() {
                            var label = ($(this).attr('label') !== undefined) ? $(this).attr('label') : $(this).html();

                            selected += label + ', ';
                        });
                        return selected.substr(0, selected.length - 2) + ' <b class="caret"></b>';
                    }
                }
            },
            buttonTitle: function(options, select) {
                var selected = '';
                options.each(function() {
                    selected += $(this).text() + ', ';
                });
                return selected.substr(0, selected.length - 2);
            },
            onChange: function(element, checked) {
                if ($(element).html() === 'SOAP' && checked) {
                    SOAPselec = true;
                    $('.mybutton').eq(0).removeClass('btn-danger');
                    $('.mybutton').eq(0).addClass('btn-primary');
                } else {
                    if ($(element).html() === 'SOAP' && !checked) {
                        SOAPselec = false;
                    } else {
                        if ($(element).html() === 'REST' && checked) {
                            RESTselec = true;
                            $('.mybutton').eq(0).removeClass('btn-danger');
                            $('.mybutton').eq(0).addClass('btn-primary');
                            $('.multiselect').multiselect('destroy');
                            multiselec('multiselect');
                            $('.method').parent().css('display', '').css('margin-top', '1%');
                            $('.mybutton').css('margin-left', '6%');
                            $('.mybutton').eq(0).css('margin-left', '');
                        } else {
                            if ($(element).html() === 'REST' && !checked) {
                                RESTselec = false;
                                $('.multiselect').multiselect('destroy');
                                multiselec('protocol');
                                $('.method').parent().css('display', 'none');

                            } else {
                                if (checked) {
                                    $node = $(element).parent().parent();
                                    $c = $($node).children()[2];
                                    $a = $($c).children()[0];
                                    $($a).removeClass('btn-danger');
                                    $($a).addClass('btn-primary');
                                }
                            }
                        }
                    }
                }
            }
        });
    }

    $('.contrate').click(function(e) {
        e.preventDefault();
        if (!SOAPselec && !RESTselec) {
            $('.protocol').attr('required', 'true');
            $('.mybutton').eq(0).removeClass('btn-primary');
            $('.mybutton').eq(0).addClass('btn-danger');
        }
        $('.mybutton').each(function(i) {
            if (i > 0) {
                if ($(this).attr('title') === '') {
                    $('.method').attr('required', 'true');
                    $(this).removeClass('btn-primary');
                    $(this).addClass('btn-danger');
                }
            }
        });
        $('.submit').click();
    });
    menu('affix');
    //Se declara una variable global que permita tener siempre un id nuevo 
    //para los parámetros adicionados.
    var i = $('.param').length + 1;
    var j = $('.toedit').length + 1;
    //$('.submit').html('<span style="font-size: 15px" class="glyphicon glyphicon-ok"></span>');
    init();

    function menu(clas) {
        $('.' + clas + ' > a').click(function() {
            if (clas === 'affix') {
                $('.' + clas + ' .active').removeClass('active');
            } else {
                $('.' + clas + ' > .active').removeClass('active');
                $('.affix > a.active').removeClass('active');
                $('.functions').addClass('active');
            }
            $(this).addClass('active');
        });
    }

    function init() {
        // Get the ul that holds the collection of tags
        collectionHolder = $('div.tags');
        // setup an "add a tag" link
        $addTagLink = $('.add_tag_link');
        $newLinkLi = $('<div></div>');
        // add the "add a tag" anchor and li to the tags ul
        collectionHolder.append($newLinkLi);
        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        collectionHolder.data('index', collectionHolder.find(':input').length);
        $addTagLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // add a new tag form (see next code block)
            addTagForm(collectionHolder, $newLinkLi);
            $('.addparam').html('<span class="glyphicon glyphicon-plus"></span>').attr('style', 'margin-bottom:2%;margin-top: 2%');
        });
    }

    function addTagForm(collectionHolder, $newLinkLi) {
        // Get the data-prototype explained earlier
        prototype = collectionHolder.data('prototype');
        // get the new index
        index = collectionHolder.data('index');
        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        newForm = prototype.replace(/__name__/g, index);
        // increase the index with one for the next item
        collectionHolder.data('index', index + 1);
        // Display the form in the page in an li, before the "Add a tag" link li
        $newFormLi = $('<div id="function' + j + '" class="col-lg-12 well atrib"><hr/><button type="button" value="function' + j + '" class="close">&times;</button><h3>Función # ' + j + ' </h3></div>').append(newForm);

        /*
         * Si es necesario cambiar el orden de los atributos del formulario 
         * solo hay que variar los números que están a continuación
         * dependiendo del lugar del atributo y manteniendo las diferencias entre
         * los dígitos.
         */

        //obtener el id del boton adicionar parámetros
        $add = $($newFormLi.children('div').children()[4]).children()[0];
        addid = $($add).attr('id');

        //lineas para obtener el prototipo de los parámetros y crear un nuevo elemento
        // div con ese protocipo.
        $dd = $($newFormLi.children('div').children()[5]).children()[1];
        $($newFormLi.children('div').children()[5]).children()[0].remove();
        prototype2 = $($dd).data('prototype');

        x = $('<div/>').attr('class', addid).data('prototype', prototype2);
        $($dd).html(x);

        $newLinkLi.before($newFormLi);

        //Para el menú lateral
        $('.submenu').append($('<a href="#function' + j + '" class="function' + j + ' list-group-item">Función # ' + j + '</a>'));

        if ($('.submenu > a').length > 4) {
            $('.submenu').addClass('pre-scrollable').css('height', '165px');
        }
        j = j + 1;
        menu('submenu');
        //plugin para el componente de seleccionar multiple
        //es necesario destruirlo y volverlo a crear para q se actualize

        if (RESTselec) {
            $('.multiselect').multiselect('destroy');
            multiselec('multiselect');
            $('.method').parent().css('display', '').css('margin-top', '1%');
            $('.mybutton').css('margin-left', '6%');
            $('.mybutton').eq(0).css('margin-left', '');
        } else {
            $('.multiselect').multiselect('destroy');
            multiselec('protocol');
            $('.method').parent().css('display', 'none');
        }


        // desmenu();
        //metódo del nivel 2 del formulario
        init2();
    }


//level 2
    function init2() {
        // setup an "add a tag" link
        $addTagLink2 = $('.addparam');
        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)

        $addTagLink2.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            // Get the ul that holds the collection of tags
            collectionHolder2 = $('div.' + $(this).attr('id'));
            $newLinkLi2 = $('<div></div>');
            // add the "add a tag" anchor and li to the tags ul
            collectionHolder2.append($newLinkLi2);
            collectionHolder2.data('index', collectionHolder2.find(':input').length);
            // add a new tag form (see next code block)
            addTagForm2(collectionHolder2, $newLinkLi2);
            die();
        });
    }

    function addTagForm2(collectionHolder2, $newLinkLi2) {
        prototype2 = collectionHolder2.data('prototype');
        newForm2 = prototype2.replace(/\[param\]\[[0-9]+\]/g, '[param][' + i + ']');
        newForm2 = newForm2.replace(/param_./g, 'param_' + i);
        $newFormLi2 = $('<div class="col-lg-4 well atrib"> <button type="button" class="close">&times;</button></div>').append(newForm2);

        $($newFormLi2.children('div').children()[0]).remove();

        $newLinkLi2.before($newFormLi2);
        i = i + 1;
    }

    // Funcion para cerrar la ventana y eliminar el elemento.
    $(document).on('click', '.close', function() {
        $(this).closest('.atrib').fadeOut(500, function() {
            $(this).remove();
        });
        if ($('.addparam').length === 1) {
            j = 1;
        }
        $('.' + $(this).attr('value')).remove();
        if ($('.submenu > a').length < 5) {
            $('.submenu').removeClass('pre-scrollable').css('height', '');
        }
    });
});

// Código original de formulario doble by Cookbook de Symfony
//  //  $(function() {
//        
//        function init(clas){
//            var collectionHolder = $('ul.'+clas);
//            var $addTagLink = $('<a href="#" class="add_tag_link">Add a tag</a>');
//            var $newLinkLi = $('<li></li>').append($addTagLink);
//            
//            collectionHolder.append($newLinkLi);
//            collectionHolder.data('index', collectionHolder.find(':input').length);
//            $addTagLink.on('click', function(e) {
//                e.preventDefault();
//                addTagForm(collectionHolder, $newLinkLi);
//            });
//            collectionHolder.find('li').each(function() {
//                addTagFormDeleteLink($(this));
//            });
//        }
//        
//        function addTagForm(collectionHolder, $newLinkLi) {
//            var prototype = collectionHolder.data('prototype');
//            var index = collectionHolder.data('index');
//            var newForm = prototype.replace(/__name__/g, index);
//            collectionHolder.data('index', index + 1);
//            var $newFormLi = $('<li></li>').append(newForm);
//                        
//            $dd = $($newFormLi.children('div').children()[3]).children()[1];
//            prototype2 = $($dd).data('prototype');
//           // index2 = $($dd).data('index');
//          //  prototype3 = prototype2.replace(/__name__/g, index2);
//            var x=$('<ul/>').attr('class','sec').data('prototype', prototype2);
//            $($dd).html(x);
//            
//            $newLinkLi.before($newFormLi);
//            init('sec');
//            
//        }
//        function addTagFormDeleteLink($tagFormLi) {
//            var $removeFormA = $('<a href="#">delete this tag</a>');
//            $tagFormLi.append($removeFormA);
//            $removeFormA.on('click', function(e) {
//                e.preventDefault();
//                $tagFormLi.remove();
//            });
//        }
//        
//        jQuery(document).ready(function() {
//            init('tags');
//        });
//    });