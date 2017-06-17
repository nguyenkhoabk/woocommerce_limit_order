(function ( $ )
{
    // Load select2 for combo box displays user roles
    "use strict";
    $( '.wlo-list-roles, .wlo-list-users, .wlo-list-product-cat, .wlo-list-product-cat' ).select2(
        {
            tags: true
        }
    );

	// Show/Hide list users, roles, field's description when click switch button
	$( '.mlo-switch' ).on( 'click', function ( e )
	{
		var currentElement = $( this );
		var parent = currentElement.parents( '.wlo-rows' );
		if( currentElement.hasClass( 'mlo-switch-role' ) )
		{
			parent.next( '.wlo-list-roles-container' ).slideToggle();
		}
		else if( currentElement.hasClass( 'mlo-switch-user' ) )
		{
			parent.next( '.wlo-list-users-container' ).slideToggle();
		}
        else if( currentElement.hasClass( 'mlo-display-description' ) )
        {
            parent.next( '.wlo-field-description' ).slideToggle();
        }
	} )

	// Show datepicker on Advance option
	$( '#wlo-date-picker' ).datepick(
		{
            firstDay: 1,
            minDate: 0,
			multiSelect: 999,
			onSelect: function ( date )
			{
                var dates = $( '#wlo-date-picker' ).datepick('getDate');
                var value = '';
                for ( var i = 0; i < dates.length; i++ )
                {
                    value += ( i == 0 ? '' : ';' ) + $.datepick.formatDate( dates[i] );
                }
                $( '.wlo-selected-dates' ).val( value || '' );
            }
		});
    // Clear selected date
    $( '#wlo_clear_selected_dates').on( 'click', function ()
    {
        $( '#wlo-date-picker' ).datepick('clear');
        $( '#wlo-date-picker' ).datepick('setDate');
    } );

	// Set selected dates
    var selectedDates = $( '.wlo-selected-dates' ).val();
    if( typeof selectedDates !== 'undefined' )
    {
        selectedDates = selectedDates.split( ';' );
        $( '#wlo-date-picker' ).datepick( 'setDate', selectedDates );
    }

    // Range time slider
    $( "#wlo-slider-range" ).slider(
    {
        range: true,
        min: 0,
        max: 1440,
        step: 60,
        values: [WLO.advance.from_time, WLO.advance.to_time],
        slide: function ( e, ui ) 
        {
            var hours1 = Math.floor( ui.values[0] / 60 );
            var minutes1 = ui.values[0] - ( hours1 * 60 );

            if ( hours1.length == 1 ) hours1 = '0' + hours1;
            if ( minutes1.length == 1 ) minutes1 = '0' + minutes1;
            if ( minutes1 == 0 ) minutes1 = '00';

            $( '.wlo-slider-time' ).html( hours1 + ':' + minutes1 );

            var hours2 = Math.floor( ui.values[1] / 60 );
            var minutes2 = ui.values[1] - ( hours2 * 60 );

            if ( hours2.length == 1 ) hours2 = '0' + hours2;
            if ( minutes2.length == 1 ) minutes2 = '0' + minutes2;
            if ( minutes2 == 0 ) minutes2 = '00';

            $('.wlo-slider-time2').html( hours2 + ':' + minutes2 );

            var timeOnSlider = hours1 + ':' + minutes1 + '-' + hours2 + ':' + minutes2;
            $( '#wlo-time-selected' ).val( timeOnSlider );
        }
    }); 
    // Load time slider
    var minutes = '00';
    $( '.wlo-slider-time' ).html( WLO.advance.from_time / 60 + ':' + minutes );
    $( '.wlo-slider-time2' ).html( WLO.advance.to_time / 60 + ':' + minutes );

    // add new row in limit product category
    $( '.btn-action' ).on( 'click', function ( e )
    {
        e.preventDefault();
        // disable select 2;
        $( '.wlo-list-product-cat' ).select2( 'destroy' );
        $( '.wlo-list-roles' ).select2( 'destroy' );
        var current = $( this ),
            parentRow = current.parents('.limit-rule'),
            tableContainer = parentRow.parents( '#list-limit-product-category-rule' ),
            countRowElement = tableContainer.next( '.total-rows' ),
            currentRowNumber = countRowElement.val();
        if ( '+' == current.text() )
        {
            var newRow = parentRow.clone( true ).removeData();
            // change button action text
            newRow.find( '.btn-action' ).text('-');
            // clear clone data on new row
            newRow.find(':checkbox').prop( 'checked', false );
            newRow.find( 'input[type=text]' ).val('');
            newRow.find( 'input[type=number]' ).val('');
            newRow.find( 'textarea' ).val('');
            newRow.find( 'button' ).removeClass('btn-add-rule');
            var inputFields = newRow.find( '.row-element' );
            inputFields.each( function ( index )
            {
                var currentName = $( this ).attr( 'name' );
                var replaceName = currentName.replace( 'row_0', 'row_' + currentRowNumber );
                $( this ).attr( 'name', replaceName );
            } );
            parentRow.parent().append( newRow );
            // update total row
            countRowElement.val( parseInt( currentRowNumber ) + 1 );

        }
        else if( '-' == current.text() )
        {
            var nextRows = parentRow.nextAll();
            nextRows.each( function ( index )
            {
                var current = $( this ),
                    currentIndex = current.data( 'rowIndex' );
                var inputFields = current.find( '.row-element' );
                inputFields.each( function ( index2 )
                {
                    var currentName = $( this ).attr( 'name' );
                    var replaceName = currentName.replace( 'row_' + currentIndex, 'row_' + ( parseInt( currentIndex ) - 1 ) );
                    $( this ).attr( 'name', replaceName );
                } );
                // update current data index
                current.data( 'rowIndex', ( parseInt( currentIndex ) - 1 ) );
            } );
            parentRow.remove();
            countRowElement.val( parseInt( currentRowNumber ) - 1 );
        }
        $( '.wlo-list-product-cat' ).select2(
            {
                tags: true
            }
        );
        $( '.wlo-list-roles' ).select2(
            {
                tags: true
            }
        );
    } )
} )( jQuery );
