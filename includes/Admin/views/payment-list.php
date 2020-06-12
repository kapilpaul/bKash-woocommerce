<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'bKash Payments List', 'bkash_wc' ); ?></h1>

	<?php if ( isset( $_GET['inserted'] ) ) { ?>
        <div class="notice notice-success">
            <p><?php _e( 'Payment has been added successfully!', 'dc-nagad' ); ?></p>
        </div>
	<?php } ?>

	<?php if ( isset( $_GET['payment-deleted'] ) && $_GET['payment-deleted'] == 'true' ) { ?>
        <div class="notice notice-success">
            <p><?php _e( 'Payment has been deleted successfully!', 'dc-nagad' ); ?></p>
        </div>
	<?php } ?>

    <form action="" method="post">
        <p style="float: left">All list of payments made with bKash</p>
		<?php
		$table = new \Inc\Admin\Payment_List();
		isset( $_POST['s'] ) ? $table->prepare_items( $_POST['s'] ) : $table->prepare_items();
		$table->search_box( 'Search', 'bpay' );
		$table->display();
		?>
    </form>
</div>
