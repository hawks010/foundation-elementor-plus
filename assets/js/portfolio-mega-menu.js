( function() {
  function initPortfolioMegaMenu( root ) {
    if ( ! root ) {
      return;
    }

    root.querySelectorAll( '[data-foundation-portfolio-menu-popup]' ).forEach( function( trigger ) {
      trigger.addEventListener( 'click', function( event ) {
        var popupId = parseInt( trigger.getAttribute( 'data-foundation-portfolio-menu-popup' ) || '0', 10 );

        if ( ! popupId ) {
          return;
        }

        if (
          window.elementorProFrontend &&
          window.elementorProFrontend.modules &&
          window.elementorProFrontend.modules.popup &&
          typeof window.elementorProFrontend.modules.popup.showPopup === 'function'
        ) {
          event.preventDefault();
          window.elementorProFrontend.modules.popup.showPopup( { id: popupId } );
        }
      } );
    } );
  }

  document.addEventListener( 'DOMContentLoaded', function() {
    document.querySelectorAll( '[data-foundation-portfolio-mega-menu]' ).forEach( initPortfolioMegaMenu );
  } );

  if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
    window.elementorFrontend.hooks.addAction( 'frontend/element_ready/foundation-portfolio-mega-menu.default', function( $scope ) {
      initPortfolioMegaMenu( $scope[0] );
    } );
  }
}() );
