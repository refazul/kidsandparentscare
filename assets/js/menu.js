var menuActive				= true;

$(document).ready(function()
{
	var menuTextOutColor        = "#777777";	/* Menu Text Color */
	var menuTextOverColor       = "#FFFFFF";	/* Hover Menu Text Color */
	var menuAnimEase            = Quad.easeOut; 	/* Circ.easeOut  Quad.easeOut*/
	var menuTranEase            = Sine.easeInOut;	/* Circ.easeOut  Quad.easeOut*/
        var menuAnimDuration        = 0.3; /* 0.6  or  0.3*/        
        var menuTranDuration        = 0.3; /* 0.6  or  0.3*/        
	var menuWidth               = $(".template-menu").width() + "px";	/* We add 2 px in order to fix the 2px margin on the right. Since sub menu holder has overflow hidden the 2px will fill the gap in IE 8 and in the other browser it won't be shown. */
	var submenuWidth            = $(".template-menu").width() + 2 + "px";
	var submOptBackSel          = "sub-menu-option-background-selected";	
	
	function hideSubmenu( obj ){ obj.css( 'opacity', '0' ).css( 'display', 'none' ); }
        
        
		
	$("#template-menu").children().each(function(index, element){
		$(element).bind('mouseenter mouseleave',{idx:index},function(event){
		
			subMenuHol = $('.sub-menu-holder',this);
			if(event.type=='mouseenter')
			{
				TweenMax.to( $(".menu-option-background", this), menuAnimDuration, { css:{marginLeft: "0px", width: menuWidth}, ease:menuAnimEase });
				TweenMax.to( $(".menu-option-text a", this), menuAnimDuration, { css:{color: menuTextOverColor}, ease:menuAnimEase });				
				
				if( subMenuHol.length )
				{
					TweenMax.to( $(".menu-option-sign", this), menuAnimDuration, { css:{color: menuTextOverColor}, ease:menuAnimEase });
					
					if(!subMenuHol.attr('data-height'))subMenuHol.attr('data-height',subMenuHol.css('height'));
					if(!subMenuHol.attr('data-width'))subMenuHol.attr('data-width',subMenuHol.css('width'));					
					var initialHeight = subMenuHol.attr('data-height'),initialWidth  = subMenuHol.attr('data-width');
					
					subMenuHol.css( 'width', '0px' ).css( 'height', '0px' );
					TweenMax.to( subMenuHol, menuAnimDuration, { css:{height:initialHeight, width:initialWidth}, delay:0.2, ease:menuAnimEase, onStart:
						function(){
							subMenuHol.css( 'opacity', '1' ).css( 'display', 'block' );
						}
					});
				}
			}
			else if(event.type='mouseleave')
			{
				//if( menuOptionsArr[ idx ][ 2 ].hasClass('menu-option-background-selected') == false ){
					TweenMax.to( $(".menu-option-background", this), menuAnimDuration, { css:{marginLeft: menuWidth, width: "0px"}, ease:menuAnimEase });
					TweenMax.to( $(".menu-option-text a", this), menuAnimDuration, { css:{color: menuTextOutColor}, ease:menuAnimEase });					
				//}
				if( subMenuHol.length )
				{
					TweenMax.to( $(".menu-option-sign", this), menuAnimDuration, { css:{color: menuTextOutColor}, ease:menuAnimEase });
					TweenMax.killTweensOf( subMenuHol );
					TweenMax.to( subMenuHol, menuAnimDuration, { css:{height:"0px", width:"0px" }, ease:menuAnimEase, onComplete:hideSubmenu, onCompleteParams:[subMenuHol]});
				}
			}
			
		});
        });
		
	$(".sub-menu-option-holder").hover(
		function(){
			var submOptBack = $(".sub-menu-option-background", this);
			var elem = submOptBack.length == 1  ? submOptBack : $("." + submOptBackSel, this);
			TweenMax.to( elem, menuAnimDuration, { css:{marginLeft:"0px", width: submenuWidth}, ease:menuAnimEase });
			TweenMax.to( $(".sub-menu-option-text a", this), menuAnimDuration, { css:{color: menuTextOverColor}, ease:menuAnimEase });
		},
		function(){
			if( $('div:first', this ).hasClass(submOptBackSel) == false ){
				var submOptBack = $(".sub-menu-option-background", this);
				var elem = submOptBack.length == 1  ? submOptBack : $("." + submOptBackSel, this);
				TweenMax.to( elem, menuAnimDuration, { css:{marginLeft: submenuWidth, width:"0px"}, ease:menuAnimEase });
				TweenMax.to( $(".sub-menu-option-text a", this), menuAnimDuration, { css:{color: menuTextOutColor}, ease:menuAnimEase });
			}
	});
	
	$('#menu-hider-icon').click(function(){
		var winW = $(window).width(),winH = $(window).height();
		if( menuActive == true )
		{
			menuActive = false;			
			if( winW >= 768 )
			{
				var menuHiderW = ($("#menu-hider").length > 0 ) ? parseInt($("#menu-hider").width(), 10) : 0;
				var menuWidth = parseInt( $("#menu-container").css("width"), 10 ) - menuHiderW;
				
				TweenMax.to( $("#menu-container"), menuTranDuration, { css:{marginLeft: -(menuWidth) + "px"}, ease:menuTranEase, onComplete:function(){}});
				TweenMax.to( $("#module-container"), menuTranDuration, { css:{marginLeft: "0px"}, ease:menuTranEase, onComplete:function(){}});
			}
			else
			{
				var menuHiderH = ($("#menu-hider").length > 0 ) ? parseInt( $("#menu-hider").height(), 10) : 0;
				var menuHeight = parseInt( $("#menu-container").css("height"), 10 ) - menuHiderH;
				
				TweenMax.to( $("#menu-container"), menuTranDuration, { css:{marginTop: -(menuHeight) + "px"}, ease:menuTranEase, onComplete:function(){}});
                                TweenMax.to( $("#module-container"), menuTranDuration, { css:{marginTop: menuHiderH+"px"}, ease:menuTranEase, onComplete:function(){}});
			}
		}
		else{
			menuActive = true;
			if( winW >= 768 )
			{                                
				var menuWidth = parseInt( $("#menu-container").css("width"), 10 );
                                
				TweenMax.to( $("#menu-container"), menuTranDuration, { css:{marginLeft: "0px"}, top: "0px", ease:menuTranEase });
                                TweenMax.to( $("#module-container"), menuTranDuration, { css:{marginLeft: "0px"}, top: "0px", ease:menuTranEase });
			}
			else
			{                                
				var menuHeight = parseInt( $("#menu-container").css("height"), 10 );
                                
				TweenMax.to( $("#menu-container"), menuTranDuration, { css:{marginTop: "0px"}, top: "0px", ease:menuTranEase });;
                                TweenMax.to( $("#module-container"), menuTranDuration, { css:{marginTop: menuHeight+"px"}, top: "0px", ease:menuTranEase });
			}
		}
	});
});

$(window).resize(function(){
	var winW = $(window).width(),winH = $(window).height();
	if( menuActive == false )
	{
		if( winW >= 768 )
		{
			var menuHiderW = ($("#menu-hider").length > 0 ) ? parseInt($("#menu-hider").width(), 10) : 0;
			var menuWidth = parseInt( $("#menu-container").css("width"), 10 ) - menuHiderW;
			
			$('#menu-container').css('margin-left', -(menuWidth)+'px');
			$('#menu-container').css('margin-top', '0px');
                        
                        $('#module-container').css('margin-left', '0px');
			$('#module-container').css('margin-top', '0px');
		}
		else
		{
			var menuHiderH = ($("#menu-hider").length > 0 ) ? parseInt( $("#menu-hider").height(), 10) : 0;
			var menuHeight = parseInt( $("#menu-container").css("height"), 10 ) - menuHiderH;
				
			$('#menu-container').css('margin-left','0px');
			$('#menu-container').css('margin-top', -(menuHeight)+'px');
                        
                        $('#module-container').css('margin-left', '0px');
			$('#module-container').css('margin-top', '0px');
		}
	}
        else if(menuActive==true)
        {
                if( winW >= 768 )
		{			
			var menuWidth = parseInt( $("#menu-container").css("width"), 10 );
			
			$('#menu-container').css('margin-left', '0px');
			$('#menu-container').css('margin-top', '0px');
                        
                        $('#module-container').css('margin-left', '0px');
			$('#module-container').css('margin-top', '0px');
		}
		else
		{			
			var menuHeight = parseInt( $("#menu-container").css("height"), 10 );
				
			$('#menu-container').css('margin-left','0px');
			$('#menu-container').css('margin-top', '0px');
                        
                        $('#module-container').css('margin-left', '0px');
			$('#module-container').css('margin-top', menuHeight+'px');
		}
        }
});