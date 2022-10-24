;(function($){
    $(document).ready(function() {
        $('.toggle').minitoggle();
        var current_val = $('#qrcg-toggle').val();
        if(1 == current_val){
           $('.minitoggle').addClass('active');
           $('.toggle-handle').css({'transform':'translate3d(36px, 0px, 0px)','cursor': 'pointer'});
        }

        $('.toggle').on("toggle", function(e){
            if (e.isActive){
                $('#qrcg-toggle').val(1);
            }else{
                $('#qrcg-toggle').val(0);
            }
            
        });
        
	});
})(jQuery)