<?php
class WPTT_Admin extends WPTT_Core {

    /**
     * __construct
     */
    function __construct() {
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'add_pages' ) );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * 管理画面に設定ページを追加
     */
    function add_pages() {
        add_theme_page( 'WP Theme Test', 'WP Theme Test', 'edit_theme_options', WPTT_PLUGIN_NAME, array( $this, 'options_page' ) );
    }


    /**
     * インストールテーマを表示し、テストが有効化されているテーマを表示する
     */
    function the_list_themes() {
        $themes = wp_get_themes();

        if ( count( $themes ) > 1 ) {
            $html = '<select name="theme" size="'.count( $themes ).'" style="height:auto;">';

            foreach ( $themes as $theme ) {
                if ( $this->get_theme() == $theme->get_template() ) {
                    $html .= '<option value="' . $theme->get_template() . '" selected="selected">' . $theme->Name . '</option>' . PHP_EOL;
                } else {
                    $html .= '<option value="' . $theme->get_template() . '">' . $theme->Name . '</option>' . PHP_EOL;
                }
            }
            $html .= '</select>';

            echo $html;
        }
    }


    function admin_init() {
        /**
         * テストテーマのOn/Offを設定
         */
        if ( isset( $_POST['_wpnonce'] ) && $_POST['_wpnonce'] ) {
            $errors = new WP_Error();
            $updates = new WP_Error();
            if ( check_admin_referer( 'wp-theme-test', '_wpnonce' ) ) {

                //オプションを設定
                $theme = esc_html( $_POST['theme'] );
                $level = esc_html( $_POST['level'] );
                $parameter = esc_html( $_POST['parameter'] );

                $options = get_option( WPTT_PLUGIN_NAME );
                $options['theme'] = $theme;
                $options['level'] = $level;
                $options['parameter'] = $parameter;

                //On/Off設定
                if ( esc_html( $_POST['status'] ) == 1 ) {
                    $options['status'] = 1;
                }else {
                    $options['status'] = 0;
                }

                update_option( WPTT_PLUGIN_NAME, $options );

                $updates->add( 'update', '保存しました' );
                set_transient( 'wptt-updates', $updates->get_error_messages(), 1 );

                // wp_safe_redirect( menu_page_url( WPTT_PLUGIN_NAME, false ) );
            }else {
                $errors->add( 'error', '不正な値が送信されました' );
                set_transient( 'wptt-errors', $errors->get_error_messages(), 1 );
            }
        }
    }


    /**
     * アップデート表示
     */
    function admin_notices() {
?>
    <?php if ( $messages = get_transient( 'wptt-updates' ) ): ?>
    <div class="updated">
        <ul>
            <?php foreach ( $messages as $key => $message ) : ?>
            <li><?php echo esc_html( $message ); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

 <?php if ( $messages = get_transient( 'wptt-errors' ) ): ?>
    <div class="error">
        <ul>
            <?php foreach ( $messages as $key => $message ) : ?>
            <li><?php echo esc_html( $message ); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
<?php
    }


    /**
     * options_page
     */
    function options_page() {
        // 保存されている情報を取得
        $options = get_option( WPTT_PLUGIN_NAME );
        // print_r($options);
?>
<div class="wrap" >
<h1>WP Theme Test</h1>
<div class="dbx-content">
<form name="form_apu" method="post" action="">
<?php wp_nonce_field( 'wp-theme-test', '_wpnonce' ); ?>

<h3>Current Status</h3>
<p>
<label><input type='radio' name='status' value='1' <?php if ( $this->is_enabled() ): ?>checked='checked'<?php endif; ?> /> On</label>
<label><input type='radio' name='status' value='0' <?php if ( !$this->is_enabled() ): ?>checked='checked'<?php endif; ?> /> Off</label>
</p>

<hr>

<h3>Usage</h3>
<?php $this->the_list_themes(); ?>

<hr>

<h3>Access Level</h3>
Access level<input name="level" value="<?php echo esc_attr( $options['level'] ); ?>" />

<hr>

<h3>パラメーターを有効にする</h3>
<p>Additionally you may add "?theme=xxx" to your blog url, where xxx is the slug of the theme you want to test.</p>
<select name="parameter">
<option value="1" <?php if ( $this->get_parameter() ): ?>selected='selected'<?php endif; ?>>有効</option>
<option value="0" <?php if ( !$this->get_parameter() ): ?>selected='selected'<?php endif; ?>>無効</option>
</select>

<hr>

<p>
<input type="submit" name="button" value="Save" class="button-primary" />
</p>

</form>
</div>
</div>
<?php
    }
}
new WPTT_Admin();
