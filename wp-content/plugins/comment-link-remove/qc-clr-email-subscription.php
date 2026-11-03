<?php

	defined('ABSPATH') or die("No direct script access!");

		
		wp_register_style('qc-clr-admin-style-email', QCCLR_ASSETS_URL.'/css/qc-clr-email_subscription.css');
		wp_enqueue_style('qc-clr-admin-style-email');
		wp_register_style('qc-clr-admin-font-awesome', QCCLR_ASSETS_URL.'/css/font-awesome.min.css');
		wp_enqueue_style('qc-clr-admin-font-awesome');

		wp_register_script('qc-clr-email-subscription-js', QCCLR_ASSETS_URL.'/js/qc-clr-email_subscription.js', array('jquery'), true);
        wp_enqueue_script('qc-clr-email-subscription-js');

		global $wpdb;
		if(!function_exists('wp_get_current_user')) {
			include(ABSPATH . "wp-includes/pluggable.php"); 
		}
		
		$table             = $wpdb->prefix.'qc_clr_subscription';
		$current_user = wp_get_current_user();
		$url = admin_url('admin.php?page=email-subscriptions');
		$customPagHTML = '';
		// Main Report Area
		
		$sql = "SELECT * FROM $table where 1 order by id desc";
		$sql1 = "SELECT count(*) FROM $table where 1";

		$total             = $wpdb->get_var( $sql1 );
		$items_per_page = 50;
		
		$page             = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
		$offset         = ( $page * $items_per_page ) - $items_per_page;
		
		$sql .=" LIMIT ${offset}, ${items_per_page}";
		
		$rows = $wpdb->get_results( $sql );
		$totalPage         = ceil($total / $items_per_page);
		
		if($totalPage > 1){
			$customPagHTML     =  '<div><span class="wpbot_pagination">Page '.esc_html($page).' of '.esc_html($totalPage).'</span>'.paginate_links( array(
			'base' => add_query_arg( 'cpage', '%#%' ),
			'format' => '',
			'prev_text' => esc_html__('&laquo;'),
			'next_text' => esc_html__('&raquo;'),
			'total' => esc_html($totalPage),
			'current' => esc_html($page)
			)).'</div>';
		}
		$mainurl = admin_url( 'admin.php?page=email-subscriptions');
		
	?>	
		<div class="qc-clr_email_list_wrapper">
			<div class="qc_clr_menu_title">
				<h2><?php echo esc_html__('Email Subscription List', 'qc-clr') ?></h2>

                <p class="qc_clr_upgrade_pro"><?php  esc_html_e( 'Email Subscription List is a Pro Version Feature. Please ', 'qc-clr' ); ?> <a href="<?php  echo esc_url( 'https://www.quantumcloud.com/products/comment-tools/', 'qc-clr' ); ?>" target="_blank"><span class="qc_clr_pro_feature" > <?php  esc_html_e( 'Upgrade to the Pro Version.', 'qc-clr' ); ?></span> </a></p>
			</div>
			
			<div class="qc_clr_menu_title qc_clr_submenu_title_wrap qc_clr_menu_title_align"><?php echo ($customPagHTML); ?><span><a class="button-primary" href="#">Export All Contacts</a> Total <?php echo esc_html($total); ?></span> </div>
			
			<?php 
			if(isset($_GET['msg']) && $_GET['msg']=='success'){
				echo '<div class="notice notice-success"><p>Record has beed Deleted Successfully!</p></div>';
			}
			?>
			
			<form id="qc_clr_form_sessions" action="<?php echo $mainurl; ?>" method="POST" style="width:100%">
			<input type="hidden" name="qc_clr_email_subscription_remove" />
			

			<div class="qchero_slider_table_area">
				<div class="qc_clr_payment_table">
					<div class="qc_clr_payment_row header">
						
						<div class="qc_clr_payment_cell">
							<input type="checkbox" id="qc_clr_checked_all" />
						</div>

						<div class="qc_clr_payment_cell">
							<?php echo esc_html__( 'Date', 'qc-clr' ) ?>
						</div>
						<div class="qc_clr_payment_cell">
							<?php echo esc_html__( 'Name', 'qc-clr' ) ?>
						</div>
						<div class="qc_clr_payment_cell">
							<?php echo esc_html__( 'Email', 'qc-clr' ); ?>
						</div>
						<div class="qc_clr_payment_cell">
							<?php echo esc_html__( 'Action', 'qc-clr' ); ?>
						</div>
						
					</div>

			<?php
			foreach($rows as $row){
			?>
				<div class="qc_clr_payment_row">

					<div class="qc_clr_payment_cell">
						
						<input type="checkbox" name="emails[]" class="qc_clr_email_checkbox" value="<?php echo $row->id ?>" />
					</div>
					
					<div class="qc_clr_payment_cell">
						<div class="qc_clr_responsive_head"><?php echo esc_html__('Date', 'qc-clr') ?></div>
						<?php echo esc_html(date('m/d/Y', strtotime($row->date))); ?>
					</div>
					<div class="qc_clr_payment_cell">
						<div class="qc_clr_responsive_head"><?php echo esc_html__('Name', 'qc-clr') ?></div>
						<?php echo esc_html($row->name); ?>
					</div>
					<div class="qc_clr_payment_cell">
						<div class="qc_clr_responsive_head"><?php echo esc_html__('Email', 'qc-clr') ?></div>
						<?php echo esc_html($row->email); ?>
					</div>
					<div class="qc_clr_payment_cell">
						<div class="qc_clr_responsive_head"><?php echo esc_html__('Action', 'qc-clr') ?></div>
						<a href="#" class="qc-clr-email-del" data-id="<?php echo esc_html($row->id); ?>"> <i class="fa fa-trash"></i> </a>
					</div>
					
				</div>
			<?php
			}
			?>

			</div>
			<button class="button-primary" id="qc_clr_submit_email_form">Delete </button>
			</form>
		</div>
		</div>

<?php 
