jQuery( function( $ ) {

  $( 'body' ).on( 'change', '.menu-item-if-menu-enable', function() {
    $( this ).closest( '.if-menu-enable' ).next().toggle( $( this ).prop( 'checked' ) );

    if ( ! $( this ).prop( 'checked' ) ) {
      var firstCondition = $( this ).closest( '.if-menu-enable' ).next().find('p:first');
      firstCondition.find('.menu-item-if-menu-enable-next').val('false');
      firstCondition.nextAll().remove();
    }
  } );

  $( 'body' ).on( 'change', '.menu-item-if-menu-enable-next', function() {
    var elCondition = $( this ).closest( '.if-menu-condition' );

    if ($(this).val() === 'false') {
      elCondition.nextAll().remove();
    } else if (!elCondition.next().length) {
      elCondition.clone().appendTo(elCondition.parent()).find('option:selected').removeAttr('selected');
    }

  } );

  $( '.wrap' ).on( 'click', '.if-menu-notice button', function() {
    $.post( ajaxurl, { action: 'if_menu_hide_notice' }, function( response ) {
      if ( response != 1 ) {
        alert( 'If Menu: Error trying to hide the notice - ' + response );
      }
    } );
  } );

} );
