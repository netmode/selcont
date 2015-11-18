(function( $ ) {
	'use strict';

    $(function() {

        if( 0 < $('#slide_image_meta_box').length ) {
            $('form').attr('enctype', 'multipart/form-data');
        }

        if( 0 < $('#xml_file_meta_box').length ) {
            $('form').attr('enctype', 'multipart/form-data');
        }

        var count=0;
        $("#slide-table").attr("id", "slide-table0");
        $("#stt_meta_box_title").attr("id", "stt_meta_box_title0");
        $("#stt_meta_box_slide").attr("id", "stt_meta_box_slide0");
        $("#stt_meta_box_time").attr("id", "stt_meta_box_time0");
        $("#add-slide").click(function(evt){
            evt.preventDefault();

            count = ++count;
            console.log('start:' + count);
            var klon = $("table[id^='slide-table']:last").clone();
            klon.appendTo("#slide_title_time_meta_box .inside");
            klon.attr("id", "slide-table"+count);
            console.log('cloned - appended - new id');
            var last = $("table[id^='slide-table']:last");
            last.find("input[id^='stt_meta_box_title']").attr("id", "stt_meta_box_title"+count);
            last.find("input[id^='stt_meta_box_slide']").attr("id", "stt_meta_box_slide"+count);
            last.find("input[id^='stt_meta_box_time']").attr("id", "stt_meta_box_time"+count);
            console.log('end:' + count);
        });


        $( '#add-row' ).click(function(evt) {
            evt.preventDefault();

            var rowCount = $('#repeatable-fieldset-one').find('.single-movie-row').not(':last-child').size();
            var newRowCount = rowCount + 1;

            var row = $( '.empty-row' ).clone(true);

            // Loop through all inputs
            row.find('input, textarea, label').each(function(){

                if ( !! $(this).attr('id') )
                    $(this).attr('id',  $(this).attr('id').replace('[%s]', '[' + newRowCount + ']') );

                if ( !! $(this).attr('name') )
                    $(this).attr('name',  $(this).attr('name').replace('[%s]', '[' + newRowCount + ']') );

                if ( !! $(this).attr('for') )
                    $(this).attr('for',  $(this).attr('for').replace('[%s]', '[' + newRowCount + ']') );

            });

            row.removeClass( 'empty-row screen-reader-text' ).find('.movie_rank_number').val(newRowCount);
            row.insertBefore( '.empty-row' );

            return false;
        });

        $( '.remove-row' ).on('click', function() {
            var rowCount = $('#repeatable-fieldset-one').find('.single-movie-row').not(':last-child').size();
            var newRowCount = rowCount - 1;

            $(this).parents('tr').fadeOut('fast',function() {
                $(this).remove();
                // iterate over each single-row
                // and update the movie rank number
                $('.single-movie-row').each(function() {
                    var thisIndex = ($(this).index() + 1);
                    $(this).find('.movie_rank_number').val(thisIndex);
                    console.log(thisIndex);
                });
            });

            return false;
        });

    });

})( jQuery );

