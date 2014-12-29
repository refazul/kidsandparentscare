$(document).ready(function()
{
    var animDuration        = 0.3;
    var animEase            = Quad.easeOut;
    var baseColor           = '#5e5e5e';
    var hoverColor          = '#ffffff';
    
    $('.leftover').each(function(){
        background=$('.leftover-background',this);
        text=$('.leftover-text',this);            

        if(!text.length)
        {
            backup=$(this).html();
            $(this).html('');
            $(this).append('<div class="leftover-text">'+backup+'</div>');
            console.log($(this).html());

            width=$(this).width();
            height=$(this).height();                

            if(!background.length)
                $(this).prepend('<div class="leftover-background" style="width:0px;height:'+height+'px;margin-left:'+width+'px;"></div>');
        }

    });

    $('.leftover').bind('mouseenter mouseleave',function(event){

        background=$('.leftover-background',this);
        text=$('.leftover-text a',this).length>0 ? $('.leftover-text a',this) : $('.leftover-text',this);
        width=$(this).width();
        height=$(this).height();

        if(background.length && text.length)
        {                
            if(event.type=='mouseenter')
            {                
                TweenMax.to(background,animDuration,{css:{marginLeft:"0px",width:width},ease:animEase});
                TweenMax.to(text,animDuration,{css:{color:hoverColor},ease:animEase});

            }
            else if(event.type=='mouseleave')
            {
                TweenMax.to(background,animDuration,{css:{marginLeft:width,width:"0px"},ease:animEase});
                TweenMax.to(text,animDuration,{css:{color:baseColor},ease:animEase});
            }
        }
    });
    
    $('.rightover').each(function(){
        background=$('.rightover-background',this);
        text=$('.rightover-text',this);            

        if(!text.length)
        {
            backup=$(this).html();
            $(this).html('');
            $(this).append('<div class="rightover-text">'+backup+'</div>');

            width=$(this).width();
            height=$(this).height();                

            if(!background.length)
                $(this).prepend('<div class="rightover-background" style="width:0px;height:'+height+'px;margin-right:'+width+'px;"></div>');
        }

    });

    $('.rightover').bind('mouseenter mouseleave',function(event){

        background=$('.rightover-background',this);
        text=$('.rightover-text a',this).length>0 ? $('.rightover-text a',this) : $('.rightover-text',this);
        width=$(this).width();
        height=$(this).height();

        if(background.length && text.length)
        {                
            if(event.type=='mouseenter')
            {                
                TweenMax.to(background,animDuration,{css:{marginRight:"0px",width:width},ease:animEase});
                TweenMax.to(text,animDuration,{css:{color:hoverColor},ease:animEase});

            }
            else if(event.type=='mouseleave')
            {
                TweenMax.to(background,animDuration,{css:{marginRight:width,width:"0px"},ease:animEase});
                TweenMax.to(text,animDuration,{css:{color:baseColor},ease:animEase});
            }
        }
    });
});