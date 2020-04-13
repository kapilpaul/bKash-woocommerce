<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'bKash Payments List', 'bkash_wc' ); ?></h1>

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
