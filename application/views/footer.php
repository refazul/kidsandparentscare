        </div><!-- end #template-wrapper"-->
        
        <div class="popup">            
            <iframe id='holder' frameborder="0" scrolling="no" width="100%" height="100%"></iframe>
            <a class="popup-close"><div class="cross-sign"></div></a>    
        </div>
        <script type="text/javascript">
            $(document).ready(function(){                
                $('.popup').resizable().draggable();
            });
        </script>
        
        <div class="popup-back"></div>
        <div class="modal"><!-- Place at bottom of page --></div>
        <script type="text/javascript">
            popup_loaded=false;

            $(document).ready(function(){        

                $('.popup-close').click(function(){
                    unloadPopupBox();
                });
                $('.popup-back').click(function(){
                    if(popup_loaded==true)
                    {
                        unloadPopupBox();
                    }
                });
            });
            function unloadPopupBox(){
                $('.popup-back').fadeOut();
                $('.popup').fadeOut();
                popup_loaded=false;
                $('.major-form').submit();
                if($('.major-form').length==0)
                    window.location = window.location;
            }

            function loadPopupBox(){
                $('.popup-back').fadeIn();
                $('.popup').fadeIn();
                popup_loaded=true;
            }

            $body = $("body");
            $(document).on({
                ajaxStart: function(){
                    $body.addClass("loading");                    
                },
                ajaxStop: function(){
                    $body.removeClass("loading");
                    $('.middle').each(function(){
                        var sum=0;
                        $(this).children().each( function(){ sum += $(this).width()+parseInt($(this).css('margin-left').replace('px',''),10)+parseInt($(this).css('padding-left').replace('px',''),10)+parseInt($(this).css('padding-right').replace('px',''),10); });
                        
                        console.log(sum);
                        
                        if(sum<700)
                            $(this).css('left','50%').css('margin-left',-sum/2).css('overflow-x','hidden');
                        else
                            $(this).css('left','50%').css('width','470px').css('margin-left','-235px').css('overflow-x','scroll');
                    })
                }
            });
            $(function() {
                $("table")
                    .tablesorter(
                                    {widgets: ['zebra']}
                                )
                    //.tablesorterPager({container: $("#pager")});
            });
        </script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/jquery.tablesorter.min.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/greensock/minified/TweenMax.min.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/menu.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/style.js"></script>
    </body>
</html>