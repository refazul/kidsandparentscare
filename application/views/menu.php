<div id="menu-container">
	<div class="menu-content-holder">
		<div class="menu-background"></div>
		<div id="template-logo" class="template-logo"></div>
		<div id="template-menu" class="template-menu">
			<?php if(user_can('CREATE_PRODUCT')):?>
			<div class="menu-option-holder">
				<?php menu_render('PRODUCTS',base_url().'products/',true);?>
				<div class="sub-menu-holder">
					<?php submenu_render('CREATE NEW',base_url().'products/create');?>
					<?php //submenu_render('LIST ALL',base_url().'products/all');?>
				</div>
			</div>
         <?php endif;?>
         <?php if(user_can('CREATE_DEPARTMENT')):?>
			<div class="menu-option-holder">
				<?php menu_render('DEPARTMENTS',base_url().'departments/',true);?>
				<div class="sub-menu-holder">
					<?php submenu_render('CREATE NEW',base_url().'departments/create');?>
					<?php //submenu_render('LIST ALL',base_url().'departments/all');?>
				</div>
			</div>
         <?php endif;?>
         <?php if(user_can('CREATE_CATEGORY')):?>
			<div class="menu-option-holder">
				<?php menu_render('CATEGORIES',base_url().'categories/',true);?>
				<div class="sub-menu-holder">
					<?php submenu_render('CREATE NEW',base_url().'categories/create');?>
					<?php //submenu_render('LIST ALL',base_url().'departments/all');?>
				</div>
			</div>
         <?php endif;?>
         <?php if(user_can('CREATE_SUPPLIER')):?>
			<div class="menu-option-holder">
				<?php menu_render('SUPPLIERS',base_url().'suppliers/',true);?>
				<div class="sub-menu-holder">
					<?php submenu_render('CREATE NEW',base_url().'suppliers/create');?>
					<?php //submenu_render('LIST ALL',base_url().'suppliers/all');?>
				</div>
			</div>
         <?php endif;?>
         <?php //if(user_can('CREATE_CUSTOMER')):?>
			<div class="menu-option-holder">
				<?php menu_render('BUYERS',base_url().'buyers/',true);?>
				<div class="sub-menu-holder">
					<?php submenu_render('CREATE NEW',base_url().'buyers/create');?>
					<?php //submenu_render('LIST ALL',base_url().'buyers/all');?>
				</div>
			</div>
         <?php //endif;?>

         <?php if(user_can('CREATE_ROLE') && user_can('EDIT_ROLE') && user_can('REMOVE_ROLE')):?>
			<div class="menu-option-holder">
				<?php menu_render('ROLES',base_url().'roles/',true);?>
                                <div class="sub-menu-holder">
					<?php submenu_render('CREATE NEW',base_url().'roles/create');?>
					<?php //submenu_render('LIST ALL',base_url().'roles/all');?>
				</div>
			</div>
         <?php endif; ?>
         <?php if(user_can('CREATE_USER')):?>
			<div class="menu-option-holder">
				<?php menu_render('USERS',base_url().'users/',true);?>
				<div class="sub-menu-holder">
					<?php submenu_render('CREATE NEW',base_url().'users/create');?>
					<?php //submenu_render('LIST ALL',base_url().'users/all');?>
				</div>
			</div>
         <?php endif; ?>
         <?php if(user_can('CREATE_INVOICE')):?>
			<div class="menu-option-holder">
				<?php menu_render('INVOICES',base_url().'invoices/create',true);?>
				<?php if(user_can('EDIT_INVOICE')):?>
				<div class="sub-menu-holder">
					<?php submenu_render('LIST ALL',base_url().'invoices/all');?>
					<?php //submenu_render('LIST ALL',base_url().'orders/all');?>
				</div>
				<?php endif;?>
			</div>
         <?php endif;?>
         <?php if(user_can('GENERATE_REPORT')):?>
			<div class="menu-option-holder">
				<?php menu_render('REPORTS',base_url().'reports/',true);?>
                                <div class="sub-menu-holder">
					<?php submenu_render('STOCK ENTRY',base_url().'reports/stockentry');?>
					<?php submenu_render('SELL INFO',base_url().'reports/sellinfo');?>
					<?php submenu_render('PROFIT',base_url().'reports/profit');?>
					<?php submenu_render('CURRENT STOCK',base_url().'reports/currentstock');?>
					<?php //submenu_render('LIST ALL',base_url().'orders/all');?>
				</div>
			</div>
         <?php endif;?>
		</div><!-- end #template-menu -->
		<!--
		<div id="template-smpartphone-menu">
			<select>
				<option value="#">Navigate to...</option>
				<option value="#"> HOME +</option>
				  <option value="#home_layout_1.html">  - Home Layout 1</option>
				  <option value="#home_layout_2.html">  - Home Layout 2</option>
				  <option value="#home_layout_3.html">  - Home Layout 3</option>
				<option value="#"> ABOUT US +</option>
				  <option value="#about_us.html">  - About us</option>
				  <option value="#philosophy.html">  - Philosophy</option>
				  <option value="#ethics.html">  - Ethics</option>
				  <option value="#careers.html">  - Careers</option>
				<option value="#news.html"> NEWS</option>
				<option value="#portfolio.html"> PORTFOLIO</option>
				<option value="#"> OUR PROJECTS +</option>
				  <option value="#4_columns_projects.html">  - 4 Columns Projects</option>
				  <option value="#3_columns_projects.html">  - 3 Columns Projects</option>
				  <option value="#2_columns_projects.html">  - 2 Columns Projects</option>
				<option value="#showreel.html"> SHOWREEL</option>
				<option value="#"> GALLERIES +</option>
				  <option value="#image_gallery.html">  - Image Gallery</option>
				  <option value="#mixed_gallery.html">  - Mixed Gallery</option>
				<option value="#"> FEATURES +</option>
				  <option value="#full_width_text_and_image.html">  - Full Width Text + Image</option>
				  <option value="#full_width_text_and_video.html">  - Full Width Text + Video</option>
				  <option value="#fullscreen_video.html">  - Fullscreen Video</option>
				  <option value="#pricing_tables.html">  - Pricing Table</option>
				<option value="#contact.html"> CONTACT</option>
			</select>
		</div>--><!-- end #template-smpartphone-menu -->
	</div><!-- end .menu-content-holder-->

	<div id="menu-hider">
		<div id="menu-hider-background"></div>
		<div id="menu-hider-icon"></div>
	</div>
</div><!-- end #menu-container -->
