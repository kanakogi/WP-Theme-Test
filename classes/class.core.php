<?php
class TTA_Core {
    private $tta_options = array();
    private $tta_default_options = array( 'status' => 0, 'theme' => null, 'level' => 10, 'parameter' => 0 );

    /**
     * 現在の設定されているテーマを取得する
     * @return [str] [description]
     */
    function get_theme() {
        $options = get_option( TTA_PLUGIN_NAME );
        if ( !empty( $options['theme'] ) ) {
            return $options['theme'];
        } else {
            return null;
        }
    }

    /**
     * テストテーマが有効化されているかどうか
     * @return string | bool
     */
    function is_enabled() {
        $options = get_option( TTA_PLUGIN_NAME );
        if( $options['status'] ){
            return true;
        }
        return false;
    }

    /**
     * 設定レベルを取得
     */
    function get_level() {
        $options = get_option( TTA_PLUGIN_NAME );
        $level = $options['level'];

        if ( $level != '' ) {
            return 'level_' . $level;
        } else {
            return 'level_10';
        }
    }    

    /**
     * 現在のパラメーターを取得
     */
    function get_parameter(){
        $options = get_option( TTA_PLUGIN_NAME );
        if( $options['parameter'] ){
            return true;
        }
        return false;
    }

    /**
     * プラグインが有効化されたときに実行
     */
    function activation_hook() {
        if ( !get_option( TTA_PLUGIN_NAME ) ) {
            update_option( TTA_PLUGIN_NAME, $this->tta_default_options );
        }
    }

    /**
     * 無効化ときに実行
     */
    function deactivation_hook() {
        delete_option( TTA_PLUGIN_NAME );
    }

    /**
     * アンインストール時に実行
     */
    function uninstall_hook(){
        delete_option( TTA_PLUGIN_NAME );
    }    
}
