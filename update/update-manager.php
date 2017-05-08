<?php
/**
 * Update Manager For Plugin.
 *
 * @class 		FSFlex_Update_Plugin_Client
 * @version		1.0.1
 * @package		Resource_Update_Manager/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

if (! class_exists('FSFlex_Update_Plugin_Client')) :

    class FSFlex_Update_Plugin_Client{

        public $checked = false;

        function __construct()
        {
            add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action( 'wp_ajax_jobboard_migrate_data', array( $this, 'migrate_data' ));
            add_action( 'wp_ajax_resource_update_manager_save_verify_data', array( $this, 'verify_data' ));
            add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_plugins' ) );
            add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );
        }

        function enqueue_scripts(){
            $screen = get_current_screen();

            if($screen->id != 'plugins'){
                return;
            }

            wp_add_inline_script( 'jquery-migrate', $this->inline_script());
        }

        function inline_script(){
            ob_start();
            ?>
            jQuery(document).ready(function($){
                "use strict";
                $('.verify-purchase-key').on('click', function(){
                    var slug   = $(this).parents('.plugin-update-tr').data('slug');
                    var verify = $('.verify-' + slug);

                    if(verify.hasClass('active')){
                        verify.removeClass('active').css('display', 'none');
                    } else {
                        verify.addClass('active').css('display', 'table-row');
                    }
                });

                $('.verify-save').on('click', function(){
                    var current         = $(this).parents('.plugin-verify');
                    var user_name       = current.find('input[name="user_name"]');
                    var api_key         = current.find('input[name="api_key"]');
                    var purchase_key    = current.find('input[name="purchase_key"]');

                    var _user_name      = user_name.val();
                    var _api_key        = api_key.val();
                    var _purchase_key   = purchase_key.val();
                    var _slug           = current.data('slug');
                    var _plugin         = current.data('plugin');

                    if(_user_name == ''){
                        user_name.focus();
                        return false;
                    }

                    if(_api_key == ''){
                        api_key.focus();
                        return false;
                    }

                    if(_purchase_key == ''){
                        purchase_key.focus();
                        return false;
                    }

                    current.find('input').prop('disabled', true);

                    $.post(ajaxurl,
                        {
                            'action'        : 'resource_update_manager_save_verify_data',
                            'slug'          : _slug,
                            'plugin'        : _plugin,
                            'user_name'     : _user_name,
                            'api_key'       : _api_key,
                            'purchase_key'  : _purchase_key,
                        },
                        function(response) {
                            location.reload();
                        }
                    );
                });
            });
            <?php
            return ob_get_clean();
        }

        function verify_data(){
            if(empty($_POST['slug']) || empty($_POST['plugin']) || empty($_POST['user_name']) || empty($_POST['api_key']) || empty($_POST['purchase_key'])){
                exit();
            }

            $verify = array(
                'user_name'     => sanitize_text_field($_POST['user_name']),
                'api_key'       => sanitize_key($_POST['api_key']),
                'purchase_key'  => sanitize_key($_POST['purchase_key']),
            );

            update_option('resource-update-manager-' . $_POST['slug'], $verify);

            $current = get_site_transient( 'update_plugins' );

            if (isset( $current->response[ $_POST['plugin'] ] ) ) {
                $server  = trailingslashit(apply_filters('fsflex_update_plugin_download_server','http://45.63.22.14/repositories/resource/download'));
                $current->response[ $_POST['plugin'] ]->package = add_query_arg($verify, $server . $_POST['slug']);
                set_site_transient('update_plugins',$current, DAY_IN_SECONDS);
            }

            exit();
        }

        function migrate_data(){
            if(get_option('jobboard_migrated')){
                exit();
            };

            $install = new JobBoard_Install();
            $install->install();
            update_option('jobboard_migrated', 1);

            Redux::setOption('jobboard_options', 'employer-custom-fields', JB()->admin->default_profile_employer());
            Redux::setOption('jobboard_options', 'candidate-custom-fields', JB()->admin->default_profile_candidate());

            do_action('jobboard_migrated');

            wp_redirect(admin_url('edit.php?post_type=jobboard-post-jobs'));
        }

        function update_plugins($transient){

            if(!$this->checked){
                $this->checked = true;
                return $transient;
            }

            $plugins = apply_filters('fsflex_update_plugin_check_list', array());

            if(empty($plugins)){
                return $transient;
            }

            $server  = trailingslashit(apply_filters('fsflex_update_plugin_update_server','http://45.63.22.14/repositories/resource/update'));
            $server  = add_query_arg('update', $plugins, $server);

            $return  = wp_remote_get($server, array(
                'timeout' => 5,
                'httpversion' => '1.1')
            );

            if(!is_wp_error($return) && !empty($return['body'])){
                $updates = json_decode($return['body']);

                foreach ($updates as $slug => $update){

                    $data = get_plugin_data(ABSPATH . '/wp-content/plugins/' . $slug);

                    if(isset($update->verification)){
                        $verify = get_option('resource-update-manager-' . $update->slug);
                        if(empty($verify['user_name']) || empty($verify['api_key']) || empty($verify['purchase_key'])) {
                            $update->package = '';
                        } else {
                            $update->package = add_query_arg($verify, $update->package);
                        }
                    }

                    if(version_compare($data['Version'] , $update->new_version, '<')){
                        $transient->response[$slug] = $update;
                    }
                }
            }

            return $transient;
        }

        function plugins_api($false, $action, $arg){
            if ( isset( $arg->slug ) && $slug = apply_filters('fsflex_update_plugin_' . $arg->slug . '_data', '')) {

                $server  = trailingslashit(apply_filters('fsflex_update_plugin_info_server', 'http://45.63.22.14/repositories/resource/info'));
                $return  = wp_remote_get( $server . $slug, array(
                        'timeout' => 30,
                        'httpversion' => '1.1')
                );

                if(!is_wp_error($return) && !empty($return['body'])){
                    $information = json_decode($return['body']);

                    foreach ($information as $key => $info){
                        if(is_object($info)){
                            $information->{$key} = (array)$info;
                        }
                    }

                    return $information;
                }
            }

            return $false;
        }

        function plugin_message($plugin_data){
            if(!isset($plugin_data['verification'])){
                return;
            }

            $data   = get_option('resource-update-manager-' . $plugin_data['slug']);
            echo '<span class="dashicons dashicons-admin-network" style="font-size: 14px;line-height: 26px;"></span>';
            echo '<a href="javascript:void(0)" class="verify-purchase-key">' . (empty($data['purchase_key']) ? esc_html__(' Verify purchase key', 'jobboard') : esc_html__(' Edit purchase key', 'jobboard')) . '</a>';
        }

        function plugin_row($plugin_file, $plugin_data){
            if(!isset($plugin_data['verification'])){
                return;
            }

            $data    = get_option('resource-update-manager-' . $plugin_data['slug'], array());
            $data    = wp_parse_args($data, array(
                'user_name'     => '',
                'api_key'       => '',
                'purchase_key'  => '',
            ));

            ?>
            <tr class="plugin-verify verify-<?php echo esc_attr( $plugin_data['slug'] ); ?>" style="display: none;" data-slug="<?php echo esc_attr( $plugin_data['slug'] ); ?>" data-plugin="<?php echo esc_attr($plugin_file); ?>">
                <td colspan="3" class="plugin-update">
                    <input type="text" name="user_name" value="<?php echo esc_attr($data['user_name']); ?>" placeholder="<?php esc_html_e('Username', 'jobboard'); ?>">
                    <input type="password" name="api_key" value="<?php echo esc_attr($data['api_key']); ?>" placeholder="<?php esc_html_e('Api Key', 'jobboard'); ?>">
                    <input type="password" name="purchase_key" value="<?php echo esc_attr($data['purchase_key']); ?>" placeholder="<?php esc_html_e('Purchase Key', 'jobboard'); ?>">
                    <input type="button" value="<?php esc_attr_e('Save', 'jobboard'); ?>" class="button button-primary verify-save">
                </td>
            </tr>
            <?php
        }
    }

    $GLOBALS['fsflex_update_plugin_client'] = new FSFlex_Update_Plugin_Client();

endif;

add_filter('fsflex_update_plugin_check_list', 'jb_update_plugin_check_list');

function jb_update_plugin_check_list($slugs = array()){
    $slugs[] = 'jobboard';
    return $slugs;
}

add_filter('fsflex_update_plugin_jobboard_data', 'jb_update_plugin_data');

function jb_update_plugin_data(){
    return 'jobboard';
}

add_action( 'in_plugin_update_message-jobboard/jobboard.php', 'jb_update_plugin_message');

function jb_update_plugin_message($plugin_data){
    if(!isset($GLOBALS['fsflex_update_plugin_client'])){
        return;
    }

    $GLOBALS['fsflex_update_plugin_client']->plugin_message($plugin_data);
}

add_action('after_plugin_row_jobboard/jobboard.php', 'jb_update_plugin_row', 100, 2);

function jb_update_plugin_row($plugin_file, $plugin_data){
    if(!isset($GLOBALS['fsflex_update_plugin_client'])){
        return;
    }

    $GLOBALS['fsflex_update_plugin_client']->plugin_row($plugin_file, $plugin_data);
}