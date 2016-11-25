</div> <br><br>

<footer class="col-md-12 text-center" id="footer">&copy; Copyright 2016-2017 By Chisumo</footer>

<!--Details Modal-->

<script>
    jQuery(window).scroll(function() {
        var vscroll = jQuery(this).scrollTop();
        jQuery('#logotext').css({
            "transform" : "translate(0px, "+vscroll/2+"px)"
        });
        var vscroll = jQuery(this).scrollTop();
        jQuery('#back-flower').css({
            "transform" : "translate("+vscroll/5+"px, -"+vscroll/12+"px)"
        });

        var vscroll = jQuery(this).scrollTop();
        jQuery('#fore-flower').css({
            "transform" : "translate(0px, -"+vscroll/2+"px)"
        });
        //console.log(vscroll);  Testing
    });


    function detailsmodal(id){
        var data = {"id" : id};
        jQuery.ajax({
            url : '/OnlineShop/includes/detailsmodal.php',
            method: "POST",
            data: data,
            success: function(data){
                jQuery('body').append(data);
                jQuery('#details-modal').modal('toggle');
            },
            error :  function() {
                alert("Something Went Wrong");
            }
        });
    }
</script>
</body>
</html>