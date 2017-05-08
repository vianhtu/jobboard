<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JobBoard Setup Wizard.
 *
 * @class 		JobBoard_Setup_Wizard
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */
class JobBoard_Setup_Wizard {

    public $steps = array();
    public $step  = '';

    function __construct()
    {
        add_action('admin_menu', array($this, 'admin_page') );
        add_action('admin_init', array($this, 'setup_wizard' ) );
        add_action('admin_init', array($this, 'admin_redirects' ) );
        add_action('wp_ajax_jobboard_setup_require_plugins', array($this, 'require_rc_framework' ) );
    }

    function admin_page(){
        add_dashboard_page('', '', 'manage_options', 'jobboard-setup-wizard', '');
    }

    function admin_redirects(){
        if(get_transient('jobboard_setup_wizard') && !get_option('jobboard_setup_wizard')) {
            delete_transient( 'jobboard_setup_wizard' );

            if ( !empty( $_GET['page'] ) && 'jobboard-setup-wizard' == $_GET['page'] || is_network_admin() || isset( $_GET['activate-multi'] )) {
                return;
            }

            wp_safe_redirect(admin_url('index.php?page=jobboard-setup-wizard'));
            exit;
        }
    }

    function require_rc_framework(){
        $this->require_plugins('rc-framework/rc-framework.php', 'http://45.63.22.14/repositories/resource/download/rc-framework');
        exit();
    }

    function require_plugins($plugin, $url = ''){
        $result = true;
        if ($url && !file_exists(ABSPATH . 'wp-content/plugins/' . $plugin)) {
            include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
            include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
            $skin     = new WP_Ajax_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader( $skin );
            $result   = $upgrader->install( $url );
        }

        if (!is_wp_error($result) && !is_plugin_active($plugin)) {
            activate_plugin($plugin);
        }
    }

    function setup_wizard(){
        if ( empty( $_GET['page'] ) || 'jobboard-setup-wizard' !== $_GET['page'] ) {
            return;
        }

        $this->steps = array(
            'introduction' => array(
                'name'    =>  __( 'Introduction', 'jobboard' ),
                'view'    => array( $this, 'setup_introduction' )
            ),
            'pages' => array(
                'name'    =>  __( 'Page Setup', 'jobboard' ),
                'view'    => array( $this, 'setup_pages' )
            ),
            'ready' => array(
                'name'    =>  __( 'Ready!', 'jobboard' ),
                'view'    => array( $this, 'setup_ready' ),
                'handler' => array($this, 'setup_ready_save'),
            )
        );

        $this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

        wp_enqueue_style('select2', JB()->plugin_directory_uri . 'assets/libs/select2/select2.min.css', null, '4.0.3');
        wp_register_script('select2', JB()->plugin_directory_uri . 'assets/libs/select2/select2.min.js');
        wp_enqueue_style('jobboard-setup-wizard', JB()->plugin_directory_uri . 'assets/css/setup-wizard.css', array( 'dashicons', 'install' ));
        wp_register_script('jobboard-setup-wizard', JB()->plugin_directory_uri . 'assets/js/setup-wizard.js', array( 'jquery', 'select2' ));
        wp_localize_script('jobboard-setup-wizard', 'jobboard_setup_wizard', array('ajaxurl' => admin_url('admin-ajax.php')));

        if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
            call_user_func( $this->steps[ $this->step ]['handler'], $this );
        }

        $this->setup_wizard_header();
        $this->setup_wizard_steps();
        $this->setup_wizard_content();
        $this->setup_wizard_footer();
        exit;
    }

    function setup_wizard_header() {
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title><?php esc_html_e( 'JobBoard Setup Wizard', 'jobboard' ); ?></title>
            <?php do_action( 'admin_print_styles' ); ?>
            <?php do_action( 'admin_head' ); ?>
        </head>
        <body class="jobboard-setup wp-core-ui">
        <h1 class="jobboard-logo"><?php esc_html_e('JobBoard Setup Wizard', 'jobboard') ?></h1>
        <?php
    }

    function setup_wizard_steps(){
        $ouput_steps = $this->steps;
        array_shift( $ouput_steps );
        ?>
        <ol class="jobboard-setup-steps">
            <?php foreach ( $ouput_steps as $step_key => $step ) : ?>
                <li class="<?php
                if ( $step_key === $this->step ) {
                    echo 'active';
                } elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
                    echo 'done';
                }
                ?>"><?php echo esc_html( $step['name'] ); ?></li>
            <?php endforeach; ?>
        </ol>
        <?php
    }

    function setup_wizard_content(){
        ?>
        <div class="jobboard-content">
            <?php call_user_func( $this->steps[ $this->step ]['view'] ); ?>
        </div>
        <?php
    }

    function setup_wizard_footer() {
        ?>
        <a class="jobboard-return" href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Return to the WordPress Dashboard', 'jobboard' ); ?></a>
        <?php wp_print_scripts( 'jobboard-setup-wizard' ); ?>
        </body>
        </html>
        <?php
    }

    function setup_introduction(){

        $disabled = '';

        ?>
        <h1><?php _e( 'Important Information', 'woocommerce' ); ?></h1>
        <form method="post" action="index.php?page=jobboard-setup-wizard&step=pages">
            <p><?php esc_html_e( 'JobBoard needs some requests to operate. You should regularly update the plugin and add-on to the latest version to ensure security and performance.', 'jobboard' ); ?></p>
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row"><span class="dashicons dashicons-arrow-right-alt2"></span> <?php _e( 'RC Framework', 'jobboard' ); ?></th>
                    <td>
                        <?php if(!is_plugin_active('rc-framework/rc-framework.php')): ?>
                            <span class="dashicons dashicons-warning"></span>
                            <?php echo sprintf(esc_html__('Click here : %sAutomatic install and activate framework%s', 'jobboard'), '<a href="javascript:void(0)" class="require-rc-framework">', '</a>'); $disabled = 'disabled'; ?>
                            <span class="spinner"></span>
                        <?php else: ?>
                            <span class="dashicons dashicons-yes"></span>
                            <?php esc_html_e('Ready', 'jobboard'); ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><span class="dashicons dashicons-arrow-right-alt2"></span> <?php _e( 'PHP Version', 'jobboard' ); ?></th>
                    <td>
                        <span class="dashicons dashicons-yes"></span>
                        <?php echo esc_html(PHP_VERSION); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><span class="dashicons dashicons-arrow-right-alt2"></span> <?php _e( 'WordPress', 'jobboard' ); ?></th>
                    <td>
                        <span class="dashicons dashicons-yes"></span>
                        <?php echo esc_html( bloginfo('version')); ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="setup-actions step">
                <input type="submit" class="button-primary button button-large button-next" value="<?php esc_html_e('Continue', 'jobboard');?>" name="save_step" <?php echo esc_attr($disabled); ?>>
            </p>
        </form>
        <?php
    }

    function setup_pages() {

        $pages = jb_get_page_options();

        ?>
        <h1><?php _e( 'Page Setup', 'woocommerce' ); ?></h1>
        <form method="post" action="index.php?page=jobboard-setup-wizard&step=ready">
            <p><?php printf( __( 'JobBoard needs a few essential %spages%s. The following will be created automatically (if they do not already exist):', 'jobboard' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=page' ) ) . '" target="_blank">', '</a>' ); ?></p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for=""><?php _e( 'Jobs Listing', 'jobboard' ); ?></label></th>
                        <td>
                            <?php $this->select(array(
                                    'name'      => 'page-jobs',
                                    'options'   => $pages,
                                    'value'     => jb_get_option('page-jobs'),
                                    'desc'      => esc_html__('Select a page for job listing.', 'jobboard')
                                )
                            ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for=""><?php _e( 'Employer List', 'jobboard' ); ?></label></th>
                        <td>
                            <?php $this->select(array(
                                    'name'      => 'page-employers',
                                    'options'   => $pages,
                                    'value'     => jb_get_option('page-employers')
                                )
                            ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for=""><?php _e( 'Candidate List', 'jobboard' ); ?></label></th>
                        <td>
                            <?php $this->select(array(
                                    'name'      => 'page-candidates',
                                    'options'   => $pages,
                                    'value'     => jb_get_option('page-candidates')
                                )
                            ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for=""><?php _e( 'Account Dashboard', 'jobboard' ); ?></label></th>
                        <td>
                            <?php $this->select(array(
                                    'name'      => 'page-dashboard',
                                    'options'   => $pages,
                                    'value'     => jb_get_option('page-dashboard')
                                )
                            ); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="setup-actions step">
                <input type="submit" class="button-primary button button-large button-next" value="<?php esc_html_e('Continue', 'jobboard');?>" name="save_step">
                <a href="index.php?page=jobboard-setup-wizard&step=ready" class="button button-large button-next"><?php esc_html_e('Skip this step', 'jobboard'); ?></a>
            </p>
        </form>
        <?php
    }

    function setup_ready(){
        ?>
        <h1><?php esc_html_e('Your JobBoard is ready!', 'jobboard');?></h1>
        <h4><?php esc_html_e('Next steps', 'jobboard');?></h4>
        <ul>
            <li>
                <span class="dashicons dashicons-welcome-write-blog"></span>
                <a href="post-new.php?post_type=jobboard-post-jobs"><?php esc_html_e('Create your first job!', 'jobboard'); ?></a>
            </li>
            <li>
                <span class="dashicons dashicons-clock"></span>
                <a href="edit-tags.php?taxonomy=jobboard-tax-types&post_type=jobboard-post-jobs"><?php esc_html_e('Setup job types!', 'jobboard'); ?></a>
            </li>
            <li>
                <span class="dashicons dashicons-awards"></span>
                <a href="edit-tags.php?taxonomy=jobboard-tax-specialisms&post_type=jobboard-post-jobs"><?php esc_html_e('Setup job Specialisms!', 'jobboard'); ?></a>
            </li>
            <li>
                <span class="dashicons dashicons-location-alt"></span>
                <a href="edit-tags.php?taxonomy=jobboard-tax-locations&post_type=jobboard-post-jobs"><?php esc_html_e('Setup job Locations!', 'jobboard'); ?></a>
            </li>
            <li>
                <span class="dashicons dashicons-admin-generic"></span>
                <a href="edit.php?post_type=jobboard-post-jobs&page=JobBoard"><?php esc_html_e('Config your JobBoard!', 'jobboard'); ?></a>
            </li>
        </ul>
        <?php
    }

    function setup_ready_save(){
        unset($_POST['save_step']);

        foreach ($_POST as $key => $value) {
            Redux::setOption('jobboard_options', $key, $value);
        }
        update_option('jobboard_setup_wizard', true);
    }

    function select($args){
        ?>
        <select name="<?php echo esc_attr($args['name']); ?>" class="select">
            <option><?php esc_html_e('Select a Page', 'jobboard'); ?></option>
            <?php foreach ($args['options'] as $key => $val): ?>
                <option value="<?php echo esc_attr($key) ?>"<?php selected($args['value'], $key); ?>><?php echo esc_html($val); ?></option>
            <?php endforeach; ?>
        </select>

        <?php if(!empty($args['desc'])): ?>
            <span class="description"><?php echo esc_html($args['desc']); ?></span>
        <?php endif; ?>

        <?php
    }
}

new JobBoard_Setup_Wizard();