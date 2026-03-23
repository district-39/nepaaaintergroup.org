<?php

class APVC_Shortcodes {

	/**
	 * Advanced Page Visit Counter Shortcode Library.
	 *
	 * @since    3.0.1
	 */
	public static function apvc_list_shortcodes( $shortcode = '' ) {

        echo '<div class="row"><div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6">';
        echo '<a type="button" class="btn rounded-pill btn-primary" href="'.APVC_DASHBOARD_PAGE_LINK.'&section=apvc-shortcode-generator">'.__('Shortcode Generator','apvc').' ></a>';
        echo '</div></div>';
		$shortcodes = APVC_Shortcode_Templates::apvc_get_shortcode_templates();
		if ( ! empty( $shortcodes ) ) {
			foreach ( $shortcodes as $shortcode ) {
				$addClass = ( isset( $shortcode['class'] ) ) ? $shortcode['class'] : '';
				?>
				<div class="col-lg-6 col-xs-12 col-12 mb-2">
					<div class="card">
						<div class="card-body text-center">
							<?php echo $shortcode['name']; ?> 
							<style>
								<?php echo esc_html( $shortcode['css'] ); ?>
								.<?php echo $shortcode['key']; ?> div{ color: #ffffff !important;;}
							</style>
							<?php echo self::apvc_get_html_without_icon( $shortcode['key'] . ' ' . $addClass, 'yes' ); ?>
						</div>
					</div>
				</div>
				<?php
			}
		}

		$c_shortcodes = APVC_Shortcode_Templates::get_custom_shortcodes();
		if ( ! empty( $c_shortcodes ) ) {
			foreach ( $c_shortcodes as $shortcode ) {
				?>
                <div class="col-lg-3 col-md-6 col-xs-12 col-12 mb-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <?php echo $shortcode->title; ?> <span class="bx-remove-icon delete-sh" data-tooltip="<?php echo esc_html_e("Delete this shortcode.","apvc" );?>" data-tooltip-position="right" data-id="<?php echo $shortcode->id; ?>"><i class="bx bx-x"></i></span> <span class="bx-copy-icon copy-sh" data-tooltip="<?php echo esc_html_e("Copy this shortcode.","apvc" );?>" data-tooltip-position="right" data-id="<?php echo $shortcode->id; ?>"><i class="bx bx-copy"></i></span>
	                        <br /><br />
                            <?php echo do_shortcode( $shortcode->shortcode ); ?>
                            <div style="display: none;" id="cst_<?php echo $shortcode->id; ?>"><?php echo $shortcode->shortcode; ?></div>
                        </div>
                    </div>
                </div>
				<?php
			}
		}
        ?>
        <!-- Modal template -->
        <div class="modal modal-transparent fade" id="modals-show-success-del-sh" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <h3><?php echo esc_html_e("Shortcode deleted successfully.","apvc");?></h3>
                    </div>
                </div>
            </div>
        </div>
        <?php
	}

	public static function apvc_get_html_without_icon( $class, $new = 'no' ) {
        if( 'yes' === $new ){
	        return '<div class="' . $class . '"><div>Visits: <span>999</span></div><div>Today: <span>123</span></div><div>Total: <span>123</span></div></div>';
        }
		return '<div class="' . $class . '"><div>{current_visits_label}{current_visits_counts}</div><div>{today_visits_label}{today_visits_counts}</div><div>{total_visits_label}{total_visits_counts}</div></div>';
	}

	public static function apvc_get_html_with_icon( $class, $new = 'no' ) {
		if( 'yes' === $new ){
			return '<div class="' . $class . '"><div>{current_visits_label}{current_visits_counts}</div><div>{today_visits_label}{today_visits_counts}</div><div>{total_visits_label}{total_visits_counts}</div></div>';
		}
		return '<div class="' . $class . '"><div><i class="icon-graph icons"></i> Visits: <span>999</span></div><div><i class="icon-eyeglass icons"></i> Today: <span>123</span></div><div><i class="icon-chart icons"></i> Total: <span>123</span></div></div>';
	}

}
