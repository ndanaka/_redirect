<?php

namespace _redirect;

class Admin
{
	private $title = 'URL Redirect';

	function activate()
	{
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	function admin_menu()
	{
		add_options_page(
			$this->title,
			$this->title,
			'manage_options',
			"_redirect",
			array( $this, "display" )
		);

		if ( ! empty( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], '_redirect' ) ) {
			if ( empty( $_POST['items'] ) ) {
				update_option( "_redirect", array() );
			} else {
				update_option( "_redirect", $_POST['items'] );
			}
		}
	}

	function display()
	{
		$redirects = get_option( '_redirect', array() );
		if ( empty( $redirects ) ) {
			$redirects = array();
		}

		?>
<div id="url-redirect">
	<h1>URL Redirect</h1>
	<style>
		table.items
		{
			width: 95%;
		}
		table.items input
		{
			width: 100%;
		}
		table.items th:nth-child(3)
		{
			width: 100px;
		}
		table.items th:nth-child(4)
		{
			width: 4em;
		}
		table.items td
		{
			text-align: center;
		}
	</style>
	<form method="post" id="redirect-form">
		<?php wp_nonce_field( '_redirect' ); ?>
	<table class="items">
		<tr><th>From URL</th><th>To</th><th>Status Code</th><th>&nbsp;</th></tr>
		<?php foreach( $redirects as $redirect ): ?>
		<tr class="item">
			<td><input type="text" class="from" value="<?php echo esc_attr( $redirect['from'] ) ?>" /></td>
			<td><input type="text" class="to" value="<?php echo esc_url( $redirect['to'] ) ?>" /></td>
			<td><select class="code">
				<?php if ( "302" === $redirect['code'] ): ?>
					<option value="301">301</option>
					<option value="302" selected>302</option></select></td>
				<?php else: ?>
					<option value="301" selected>301</option>
					<option value="302">302</option></select></td>
				<?php endif; ?>
			<td><a class="btn-remove" href="#"><span class="dashicons dashicons-trash"></span></span></a></td>
		</tr>
		<?php endforeach; ?>
		<tr class="item">
			<td><input type="text" class="from" placeholder="/path/to/example.html" /></td>
			<td><input type="text" class="to" placeholder="https://example.com/path/to/example.html" /></td>
			<td><select class="code">
					<option value="301" selected>301</option>
					<option value="302">302</option></select></td>
			<td><a class="btn-remove" href="#"><span class="dashicons dashicons-trash"></span></span></a></td>
		</tr>
		<tr class="add">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><a class="btn-plus" href="#"><span class="dashicons dashicons-plus-alt"></span></a></td>
		</tr>
	</table>

		<p class="submit"><button id="submit" class="button button-primary">Save Changes</button></p>
	</form>
</div>

<script>
  (function($) {
    $( '.btn-plus' ).on( 'click', function(e) {
      var line = $( ".item:first" ).clone( true );
      $( 'input', line ).val( '' );
      $( '.code', line ).val( '301' );
      $( 'table.items .add' ).before( line );
      e.preventDefault()
    })


    $( '.btn-remove' ).on( 'click', function(e) {
      $( this ).parents( 'tr.item' ).remove();
      e.preventDefault()
    })

    $( '#submit' ).on( 'click', function( e ) {
      var lines = $( '.item' );
      for ( var i = 0; i < lines.length; i++ ) {
        var line = $( lines[i] )
        if ( $( '.from', line ).val() && $( '.to', line ).val() ) {
          $('.from', line).attr("name", "items[" + i + "][from]");
          $('.to', line).attr("name", "items[" + i + "][to]");
          $('.code', line).attr("name", "items[" + i + "][code]");
        }
      }

      $( '#redirect-form' ).submit();
    } )
  })(jQuery)
</script>
		<?php
	}
}
