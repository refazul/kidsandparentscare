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
        <script type="text/javascript" src="<?php echo asset_url();?>js/cotfield.js"></script>
        <div class='global-overlay'></div>
        <script type='text/template' id='file_uploader_template'>
            <form class='ajax_form' action="{{BASE_DIR}}general/upload" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_scope" value="{{scope}}"/>
                <input type="hidden" name="_name" value="{{name}}"/>
                <div class="image-holder" style="width:300px;float:right;">
                    <div style="width:45%;padding:5px;height:125px;margin:auto;margin-top:10px;">
                        <input type="file" class='file' id="{{name}}" name="{{name}}" style="width: 100%;height: 100%;position: relative;top: 0%;left: 0px;opacity: 0;cursor:pointer">
                        <img class="preview-image" width="100%" height="100%" src="{{BASE_DIR}}assets/images/file-upload-2.png" style="position: relative; z-index: -10;top:-100%;"/>
                    </div>
                    <!--<input type="submit" value="Upload File to Server">-->
                    <div class="progress" style="display: none;width:100%;margin-bottom:0px;border-radius: 0px;">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            <div class="percent">0%</div>
                        </div>
                    </div>
                    <div class="status" style="display:none;font-size: 11px;text-align: center;width: 100%;margin-top:3px;">Ready</div>
                </div>
            </div>
        </script>
        <script type='text/template' id='file_existing_template'>
            <div class="docs-container" id="" data-file='{{file}}'>
                <div class='cross-sign'></div>
                <div class="doc-icons pdf"></div>
                <a class="doc-links" href="{{BASE_DIR}}uploads/{{file}}" target="_blank"><{{file}}></a>
            </div>
        </script>
    </body>
</html>
