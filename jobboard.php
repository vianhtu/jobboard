<?php
/**
 * Plugin Name: JobBoard
 * Plugin URI: http://fsflex.com/
 * Description: JobBoard that allows you to create a useful and easy to use job listings website.
 * Version: 1.0.3
 * Author: FSFlex
 * Author URI: http://fsflex.com/
 * License: GPLv2 or later
 * Text Domain: jobboard
 */
if (! defined('ABSPATH')) {
    exit();
}

if (! class_exists('JobBoard')) :

    final class JobBoard
    {
        static $instance;
        public $file;
        public $basename;
        public $plugin_directory;
        public $plugin_directory_uri;
        public $session;
        public $query;
        public $admin;
        public $post;
        public $job;
        public $form;
        public $dashboard;
        public $account;
        public $candidate;
        public $employer;

        public static function instance(){

            if (is_null(self::$instance)) {
                self::$instance = new JobBoard();
                self::$instance->setup_globals();
                self::$instance->includes();
                self::$instance->setup_actions();
            }

            return self::$instance;
        }

        private function setup_globals()
        {
            $this->file = __FILE__;
            /* base name. */
            $this->basename = plugin_basename($this->file);
            /* base plugin. */
            $this->plugin_directory = plugin_dir_path($this->file);
            $this->plugin_directory_uri = plugin_dir_url($this->file);
        }

        private function setup_actions(){
            add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
            add_action( 'init', array( $this, 'init' ), 0 );
            add_action( 'wp_head', array($this, 'add_head'));
            add_action( 'admin_enqueue_scripts', array($this, 'add_admin_scripts'));
            add_action( 'admin_menu', array ($this, 'menu_notice') , 100);
            add_action( 'admin_notices', array($this, 'admin_notices'));
            add_action( 'wp_enqueue_scripts', array($this, 'add_scripts'));
            //add_action('redux/extensions/before', array($this, 'load_rc_extensions'));
            /* debug. */
            //add_action( 'activated_plugin', array($this, 'activated_error' ));
        }

        private function includes(){

            require_once $this->plugin_directory . 'libs/awesome-font.php';
            require_once $this->plugin_directory . 'update/update-manager.php';
            require_once $this->plugin_directory . 'inc/class.jb-autoloader.php';

            require_once $this->plugin_directory . 'inc/class.jb-admin.php';
            require_once $this->plugin_directory . 'inc/class.jb-setup-wizard.php';
            require_once $this->plugin_directory . 'inc/class.jb-post.php';
            require_once $this->plugin_directory . 'inc/class.jb-account.php';

            require_once $this->plugin_directory . 'inc/functions-core.php';
            require_once $this->plugin_directory . 'inc/functions-widget.php';
            require_once $this->plugin_directory . 'inc/functions-email.php';
            require_once $this->plugin_directory . 'inc/functions-job.php';

            if ( $this->is_request( 'frontend' ) ) {
                $this->frontend_includes();
            }

            $this->admin        = new JobBoard_Admin();
            $this->post         = new JobBoard_Post();
            $this->job          = new JobBoard_Job();
            $this->query        = new JobBoard_Query();
            $this->dashboard    = new JobBoard_Dashboard();
            $this->account      = new JobBoard_Account();
            $this->candidate    = new JobBoard_Candidate();
            $this->employer     = new JobBoard_Employer();

            register_activation_hook($this->file, array(new JobBoard_Install(), 'install'));
        }

        private function frontend_includes(){
            new JobBoard_Template();
            new JobBoard_Fields();
            new JobBoard_Page();
            new JobBoard_Footer();
            $this->form = new JobBoard_FormHandler();

            require_once $this->plugin_directory . 'inc/class.jb-session-handler.php';
            $this->session = new JobBoard_Session_Handler();

            require_once $this->plugin_directory . 'inc/functions-notice.php';
            require_once $this->plugin_directory . 'inc/functions-dashboard.php';
            require_once $this->plugin_directory . 'inc/functions-account.php';
            require_once $this->plugin_directory . 'inc/functions-candidate.php';
            require_once $this->plugin_directory . 'inc/functions-employer.php';
            require_once $this->plugin_directory . 'inc/template-hooks.php';
        }

        /**
         * What type of request is this?
         *
         * @param  string $type admin, ajax, cron or frontend.
         * @return bool
         */
        function is_request( $type ) {
            switch ( $type ) {
                case 'admin' :
                    return is_admin();
                case 'ajax' :
                    return defined( 'DOING_AJAX' );
                case 'cron' :
                    return defined( 'DOING_CRON' );
                case 'frontend' :
                    return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
            }
        }

        function add_head(){
            echo '<script type="text/javascript">';
            echo 'var ajaxurl = "' . admin_url( 'admin-ajax.php', 'relative' ) . '";';
            echo '</script>';
        }


        function init(){
            do_action( 'before/jb/init' );
            $this->load_plugin_textdomain();
            new JobBoard_Shortcodes();
            do_action( 'after/jb/init' );
        }

        function add_scripts(){
            wp_enqueue_style('select2-css', $this->plugin_directory_uri . 'assets/libs/select2/select2.min.css', null, '4.0.3');
            wp_enqueue_script('select2-js', $this->plugin_directory_uri . 'assets/libs/select2/select2.min.js', array('jquery'), '4.0.3', true);

            /* global style.*/
            wp_enqueue_style('jobboard-responsive-css', $this->plugin_directory_uri . 'assets/libs/bootstrap/responsive.min.css');
            wp_enqueue_style('modal-effects-css', $this->plugin_directory_uri . 'assets/libs/modal-effects/modal-effects.css');
            wp_enqueue_style('jobboard-css', $this->plugin_directory_uri . 'assets/css/jobboard.css');

            /* dashicons icons. */
            if(jb_get_option('font-dashicons', true)) {
                wp_enqueue_style('dashicons');
            }

            /* awesome icons. */
            if(jb_get_option('font-awesome', true)){
                wp_enqueue_style('font-awesome', $this->plugin_directory_uri . 'assets/libs/awesome/css/font-awesome.min.css');
            }

            /* global script. */
            wp_enqueue_script('classie', $this->plugin_directory_uri . 'assets/libs/select2/classie.js', array(), time(), true);
            wp_enqueue_script('modal-effects-js', $this->plugin_directory_uri . 'assets/libs/modal-effects/modal-effects.js', array('jquery'), time(), true);
            wp_enqueue_script('jobboard-js', $this->plugin_directory_uri . 'assets/js/jobboard.js', array('jquery'), time(), true);

            /* dashboard. */
            if(is_jb_dashboard()){
                wp_enqueue_style('jobboard-dashboard-css', $this->plugin_directory_uri . 'assets/css/dashboard.css');
                wp_enqueue_script('jobboard-dashboard-js', $this->plugin_directory_uri . 'assets/js/dashboard.js', array('jquery'), time(), true);
                do_action("jobboard_dashboard_scripts");
            }

            /* endpoint. */
            if(is_jb_endpoint_url()){
                $endpoint   = JB()->query->get_current_endpoint();
                $file_css   = $this->plugin_directory . "assets/css/endpoint-{$endpoint}.css";
                $file_js    = $this->plugin_directory . "assets/js/endpoint-{$endpoint}.js";

                if(file_exists($file_css)){
                    wp_enqueue_style("jobboard-endpoint-{$endpoint}-css", $this->plugin_directory_uri . "assets/css/endpoint-{$endpoint}.css");
                }

                if(file_exists($file_js)){
                    wp_enqueue_script("jobboard-endpoint-{$endpoint}-js", $this->plugin_directory_uri . "assets/js/endpoint-{$endpoint}.js", array('jquery'), time(), true);
                }

                do_action("jobboard_endpoint_{$endpoint}_scripts");
            }
        }

        function add_admin_scripts()
        {
            wp_enqueue_style('jobboard-admin', $this->plugin_directory_uri . 'assets/css/admin.css');

            $screen = get_current_screen();

            if (isset($screen->id)) {
                switch($screen->id){
                    case 'edit-jobboard-post-jobs':
                        wp_enqueue_style('jobboard-edit-post', $this->plugin_directory_uri . 'assets/css/edit-post.css');
                        break;
                    case 'jobboard-post-jobs':
                        wp_enqueue_style('jobboard-post', $this->plugin_directory_uri . 'assets/css/post.css');
                        break;
                    case 'edit-jobboard-tax-specialisms':
                        wp_enqueue_style('jobboard-taxonomy-specialism', $this->plugin_directory_uri . 'assets/css/taxonomy-specialism.css');
                        break;
                    case 'edit-jobboard-tax-types':
                        wp_enqueue_style('jobboard-taxonomy-type', $this->plugin_directory_uri . 'assets/css/taxonomy-type.css');
                        break;
                    case 'profile':
                        wp_enqueue_style('jobboard-user-profile', $this->plugin_directory_uri . 'assets/css/user-profile.css');
                        break;
                    case 'user-edit':
                        wp_enqueue_style('jobboard-user-profile', $this->plugin_directory_uri . 'assets/css/user-profile.css');
                        break;
                }
            }
        }

        function template_path() {
            return apply_filters( 'jb/template/path', 'jobboard/' );
        }

        /**
         * Function used to Init WooCommerce Template Functions - This makes them pluggable by plugins and themes.
         */
        public function include_template_functions() {
            require_once $this->plugin_directory . 'inc/template-functions.php';
        }

        /**
         * Load Localisation files.
         *
         * Note: the first-loaded translation file overrides any following ones if the same translation is present.
         */
        public function load_plugin_textdomain() {
            $locale = apply_filters( 'jb/plugin/locale', get_locale(), 'jobboard' );

            load_textdomain( 'jobboard', WP_LANG_DIR . '/jobboard/jobboard-' . $locale . '.mo' );
            load_plugin_textdomain( 'jobboard', false, $this->plugin_directory . '/languages' );
        }

        public function load_rc_extensions($ReduxFramework){

            require_once $this->plugin_directory . 'inc/extensions/rc_salary/extension_rc_salary.php';

            if ( ! isset( $ReduxFramework->extensions['rc_salary'] ) ) {
                $ReduxFramework->extensions['rc_salary'] = new ReduxFramework_Extension_rc_salary( $ReduxFramework );
            }
        }

        function menu_notice(){
            global $submenu;

            $key = "edit.php?post_type=jobboard-post-jobs";

            if(empty($submenu[$key])){
                return;
            }

            global $wpdb;

            $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_status = 'pending' AND {$wpdb->posts}.post_type ='jobboard-post-jobs'");

            if(!$count){
                return;
            }

            foreach ($submenu[$key] as $k => $menu){
                if($menu[2] == $key){
                    $submenu[$key][$k][0] .= ' <span class="update-plugins count-'.$count.'"><span class="job-count">'.$count.'</span></span>';
                    break;
                }
            }

            return $submenu;
        }

        function admin_notices(){
            if(!class_exists('RC_Framework')){
                ?>
                <div class="notice notice-error">
                    <p>
                        <span class="dashicons dashicons-warning"></span>
                        <?php echo sprintf(esc_html__( 'Since version 1.0.2 the JobBoard require the RC Framework, you can click here %sinstall and activate framework.%s', 'sample-text-domain' ), '<a href="index.php?page=jobboard-setup-wizard">', '</a>'); ?>
                    </p>
                </div>
                <?php
            } else {
                if(!get_option('jobboard_migrated')) {
                    ?>
                    <div class="notice notice-error">
                        <p>
                            <span class="dashicons dashicons-warning"></span>
                            <?php esc_html_e('JobBoard version 1.0.3 require migrate your data. (note: before migrate you need update all theme and plugins to latest version.)', 'sample-text-domain'); ?>
                        </p>
                        <p>
                            <a id="jobboard-migrate"
                               href="<?php echo admin_url('admin-ajax.php?action=jobboard_migrate_data'); ?>"
                               class="button button-primary button-large">
                                <?php esc_html_e('Migrate Now', 'jobboard'); ?>
                            </a>
                        </p>
                    </div>
                    <?php
                }
            }
        }

        function activated_error(){
            update_option( 'plugin_error',  ob_get_contents() );
        }

        function plugin_setting( $links ){
            $action_links = array(
                'settings' => '<a href="' . admin_url( 'edit.php?post_type=jobboard-post-jobs&page=JobBoard' ) . '" title="' . esc_attr( esc_html__( 'JobBoard Settings', 'jobboard' ) ) . '">' . esc_html__( 'Settings', 'jobboard' ) . '</a>',
            );
            return array_merge( $action_links, $links );
        }
    }

endif;

function JB(){
    return JobBoard::instance();
}

$GLOBALS['jobboard'] = JB();