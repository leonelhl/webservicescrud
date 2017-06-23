  $(function() {
        $(".list").addClass("active");
        $('.submit').html('<span style="font-size: 15px" class="glyphicon glyphicon-ok"></span>');
        // Get the ul that holds the collection of tags
        var collectionHolder = $('div.tags');
        // setup an "add a tag" link
        var $addTagLink = $('.add_tag_link');
        var $newLinkLi = $('<div></div>');
        jQuery(document).ready(function() {
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
            });
        });

        function addTagForm(collectionHolder, $newLinkLi) {
            // Get the data-prototype explained earlier
            var prototype = collectionHolder.data('prototype');
            // get the new index
            var index = collectionHolder.data('index');
            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have
            var newForm = prototype.replace(/__name__/g, index);
            // increase the index with one for the next item
            collectionHolder.data('index', index + 1);
            // Display the form in the page in an li, before the "Add a tag" link li
            var $newFormLi = $('<div class="col-lg-3 well atrib"> <button type="button" class="close">&times;</button></div>').append(newForm);
            $newLinkLi.before($newFormLi);
        }
        $(document).on('click', '.close', function() {
            $(this).closest('.atrib').fadeOut(500, function() {
                $(this).remove();
            });
        });
    });