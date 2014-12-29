<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('menu_render'))
{
	function menu_render()
	{
		$args=func_get_args();		
		//$args[0]=text
		//$args[1]=link
		//$args[2]=true or false
		?>
		<div class="menu-option-background"></div>
		<div class="menu-option-text">
			<a href="<?php echo $args[1];?>"><?php echo $args[0];?></a>
			<?php if($args[2]==true):?>
			<div class="menu-option-sign">+</div>
			<?php endif;?>
		</div>
		<?php
	}
}
if ( ! function_exists('submenu_render'))
{
	function submenu_render()
	{
		$args=func_get_args();		
		//$args[0]=text
		//$args[1]=link		
		?>
		<div class="sub-menu-option-holder">
			<div class="sub-menu-option-background"></div>
			<div class="sub-menu-option-text">
				<a href="<?php echo $args[1];?>"><?php echo $args[0];?></a>			
			</div>
		</div>
		<?php
	}
}