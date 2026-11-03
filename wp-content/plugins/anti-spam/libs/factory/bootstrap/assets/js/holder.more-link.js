/**
* Factory More Link
*/

;(function ( $, window, document, undefined ) {
    "use strict"; // jshint ;_;
  
    var pluginName = 'factoryBootstrap455_moreLink';

    $.fn[pluginName] = function ( param1, param2 ) {
        
        return this.each(function () {
            var $this = $(this);
            
            $this.find(".factory-more-link-show").click(function(){
                $( $(this).attr('href') ).fadeIn();
                $(this).hide();
                return false;
            });
            
            $this.find(".factory-more-link-hide").click(function(){
                var content = $( $(this).attr('href') );
                content.fadeOut(300, function(){
                    content.parents(".factory-more-link").find(".factory-more-link-show").show();
                });
                return false;
            });
        });
    };

    // auto init
 
    $(function(){
        $('.factory-bootstrap-455 .factory-more-link').factoryBootstrap455_moreLink();  
    });
    
})( jQuery, window, document );