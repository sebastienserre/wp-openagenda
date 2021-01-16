jQuery(document).ready( function($) {
    $( "#datepicker" ).datepicker(
        {
            dateFormat: 'yy-mm-dd',
            minDate: 0,
            changeMonth: true,
            changeYear: true
        }
    );
    $( "#datepicker2" ).datepicker(
        {
            dateFormat: 'yy-mm-dd',
            minDate: 0,
            changeMonth: true,
            changeYear: true
        }
    );
} );



