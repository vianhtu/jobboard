<?php
/**
 * JobBoard Account.
 *
 * @class 		JobBoard_Account
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

if (! class_exists('JobBoard_Account')) :

    class JobBoard_Account{

        function __construct()
        {
            add_action( 'delete_user',  array($this, 'delete_user') );
            add_action( 'after_setup_theme', array($this, 'show_admin_bar'));
            add_action( 'edit_user_profile', array( $this, 'profile_nav'), 99, 1);
            add_action( 'admin_menu', array( $this, 'user_profile_menu' ), 5);
            add_action( 'show_user_profile', array( $this, 'profile_nav'), 99, 1);
            add_filter( 'get_avatar_url', array($this, 'get_avatar_url'), 10, 2);
            add_filter( 'login_redirect', array($this, 'login_redirect'), 10, 3);
            add_filter( 'jobboard_catalog_input_args', array($this, 'orderby_inputs'));
            add_filter( 'jobboard_catalog_orderby_args', array($this, 'orderby_args'));
            add_filter( 'jobboard_catalog_showing_args', array($this, 'showing_args'));
            add_filter( 'jobboard_search_form_args', array($this, 'search_form_args'));
            add_action( 'jobboard_job_applied', array($this, 'update_applied'), 10, 3);
            add_action( 'jobboard_dashboard_candidate', array($this, 'login_error'), 0);
            add_action( 'jobboard_dashboard_employer', array($this, 'login_error'), 0);
        }

        function query($query = array()){
            $paged      = get_query_var('paged');
            $orderby    = isset($_GET['orderby']) ? sanitize_key($_GET['orderby']) : 'ID';
            $query      = array_merge(array(
                'number'    => jb_get_option('author-per-page', 12),
                'paged'     => $paged ? $paged : 1,
                'order'     => 'ASC',
                'orderby'   => $orderby
            ), $query);

            add_filter('pre_get_users', array($this, 'pre_get_users'));
            add_filter('pre_user_query', array($this, 'pre_user_query'));
            $users = new WP_User_Query(apply_filters('jobboard_user_query_args', $query));
            remove_filter('pre_get_users', array($this, 'pre_get_users'));
            remove_filter('pre_user_query', array($this, 'pre_user_query'));

            return $users;
        }

        function pre_get_users($q){
            $meta_query = array();
            if(!empty($_GET['specialism-filters']) && is_array($_GET['specialism-filters'])) {
                $specialism_query = array('relation' => 'OR');
                foreach ($_GET['specialism-filters'] as $specialism_id) {
                    $specialism_query[] = array(
                        'key'       => 'job_specialisms',
                        'value'     => '"' . $specialism_id . '"',
                        'compare'   => 'LIKE',
                    );
                }

                $meta_query[] = array(
                    $specialism_query
                );
            }

            if(is_jb_candidate_listing() && !empty($_GET['date-filters'])){
                $date = date('Y-m-d H:i:s', strtotime("-{$_GET['date-filters']} hour", current_time( 'timestamp' )));
                $meta_query[] = array(
                    array(
                        'key'       => 'last_login',
                        'value'     => $date,
                        'type'      => 'DATETIME',
                        'compare'   => '>='
                    )
                );
            }

            $pre_meta_query = $q->get('meta_query');
            if(!empty($pre_meta_query) && is_array($pre_meta_query)){
                $meta_query = array_merge($pre_meta_query, $meta_query);
            }

            $q->set('meta_query', $meta_query);
        }

        function pre_user_query($q){
            global $wpdb;
            if(!empty($_GET['search'])) {
                $keywords = esc_attr($_GET['search']);
                $q->query_where .= " AND (display_name LIKE '%{$keywords}%'";
                $q->query_where .= " OR user_login LIKE '%{$keywords}%'";
                $q->query_where .= " OR user_nicename LIKE '%{$keywords}%'";
                $q->query_where .= " OR user_email LIKE '%{$keywords}%'";
                $q->query_where .= " OR ({$wpdb->usermeta}.meta_key = 'first_name' AND {$wpdb->usermeta}.meta_value LIKE '%{$keywords}%')";
                $q->query_where .= " OR ({$wpdb->usermeta}.meta_key = 'last_name' AND {$wpdb->usermeta}.meta_value LIKE '%{$keywords}%')";
                $q->query_where .= " OR ({$wpdb->usermeta}.meta_key = 'description' AND {$wpdb->usermeta}.meta_value LIKE '%{$keywords}%')";

                if (count($keywords = str_word_count($keywords, 1)) >= 3) {
                    foreach ($keywords as $index => $key) {
                        if ($index <= 5 && isset($keywords[$index + 1])) {
                            $key = $key . ' ' . $keywords[$index + 1];
                            $q->query_where .= " OR display_name LIKE '%{$key}%'";
                            $q->query_where .= " OR user_login LIKE '%{$key}%'";
                            $q->query_where .= " OR user_nicename LIKE '%{$key}%'";
                            $q->query_where .= " OR user_email LIKE '%{$key}%'";
                            $q->query_where .= " OR ({$wpdb->usermeta}.meta_key = 'first_name' AND {$wpdb->usermeta}.meta_value LIKE '%{$key}%')";
                            $q->query_where .= " OR ({$wpdb->usermeta}.meta_key = 'last_name' AND {$wpdb->usermeta}.meta_value LIKE '%{$key}%')";
                            $q->query_where .= " OR ({$wpdb->usermeta}.meta_key = 'description' AND {$wpdb->usermeta}.meta_value LIKE '%{$key}%')";
                        }
                    }
                }

                $q->query_where .= ')';
            }

            if(is_jb_employer_listing() && !empty($_GET['date-filters'])){
                $date = date('Y-m-d H:i:s', strtotime("-{$_GET['date-filters']} hour", current_time( 'timestamp' )));
                $q->query_from  .=" INNER JOIN {$wpdb->posts} ON {$wpdb->users}.ID = {$wpdb->posts}.post_author";
                $q->query_where .=" AND {$wpdb->posts}.post_date >= '{$date}'";
                $q->query_where .=" AND {$wpdb->posts}.post_type = 'jobboard-post-jobs'";
                $q->query_where .=" AND {$wpdb->posts}.post_status = 'publish'";
                $q->query_where .=" GROUP BY {$wpdb->users}.ID";
            }
        }

        function setup_users(){
            global $wp_query;
            if(is_jb_employer_listing()){
                $role = 'jobboard_role_employer';
            } elseif (is_jb_candidate_listing()){
                $role = 'jobboard_role_candidate';
            }

            if(isset($role)) {
                $users = $this->query(array('role' => $role));
                if (!empty($users->results)) {
                    $wp_query->max_num_pages = ceil($users->get_total() / $users->get('number'));
                    $GLOBALS['users'] = $users;
                }
            }
        }

        function orderby_inputs($inputs){
            if(is_jb_account_listing()) {
                unset($inputs['post_type']);
                $inputs['page_id'] = '<input type="hidden" name="page_id" value="' . get_the_ID() . '"/>';
            }

            if($search = jb_account_get_search_query()){
                $inputs['search']  = '<input type="hidden" name="search" value="' . esc_attr($search) . '"/>';
            }

            return $inputs;
        }

        function orderby_args($args){
            if(!is_jb_account_listing()){
                return $args;
            }

            return array(
                'ID'            => esc_html__( 'Sort by ID', 'jobboard' ),
                'display_name'  => esc_html__( 'Sort by name', 'jobboard' ),
                'registered'    => esc_html__( 'Sort by registered', 'jobboard' )
            );
        }

        function showing_args($showing){
            global $users, $paged;

            if(!is_jb_account_listing() || empty($users->results)){
                return $showing;
            }

            $posts_per_page     = $users->get('number');
            $post_count         = count($users->results);
            $showing['paged']   = $paged ? $paged : 1;
            $showing['current'] = $post_count;
            $showing['all']     = $users->get_total();
            $posts_per_pages    = $showing['paged'] * $posts_per_page;

            if($posts_per_pages <= $showing['all']){
                $showing['current'] = $posts_per_pages;
            } else {
                $showing['current'] = $showing['all'];
            }

            $showing['paged']   = $showing['current'] - $post_count;
            return $showing;
        }

        function search_form_args($args){
            if(!is_jb_account_listing()){
                return $args;
            }

            return array(
                'name'          => 'search',
                'placeholder'   => esc_attr_x( 'Search Name&hellip;', 'placeholder', 'jobboard' ),
                'button'        => esc_attr_x( 'Search', 'submit button', 'jobboard' ),
                'value'         => isset($_GET['search']) ? $_GET['search'] : '',
                'type'          => 'page_id',
                'type_value'    => get_the_ID()
            );
        }

        function login_redirect($redirect_to, $requested_redirect_to, $user){
            if(!empty($user->errors)){
                wp_redirect(add_query_arg('error', '1', jb_page_permalink('dashboard')));
            }

            if(isset($user->ID)) {
                if (!empty($_POST['dashboard']) || (strpos($requested_redirect_to, 'wp-admin') && (is_jb_employer($user->ID) || is_jb_candidate($user->ID)))) {
                    $redirect_to = jb_page_permalink('dashboard');
                }

                update_user_meta($user->ID, 'last_login', current_time('mysql'));
            }

            return $redirect_to;
        }

        function login_error(){
            if(empty($_GET['error'])){
                return false;
            }

            switch ($_GET['error']){
                case '1':
                    jb_notice_add( esc_html__( 'Error : Email or password incorrect!', 'jobboard' ), 'error');
                    break;
            }
        }

        function delete_user($user_id){
            global $wpdb;
            if(is_jb_candidate($user_id)) {
                $wpdb->delete($wpdb->prefix . 'jobboard_applied',
                    array('user_id' => $user_id),
                    array('%d')
                );
            }
        }

        function update_applied($user_id, $post_id , $status){
            $key      = '_jobboard_' . $status . '_ids';
            $applied  = get_user_meta($user_id, $key, true);

            if(is_array($applied) && !in_array($post_id, $applied)){
                $applied[]  = $post_id;
            } else {
                $applied    = array($post_id);
            }

            update_user_meta($user_id, $key, $applied);
        }

        function get_avatar_url($url, $id_or_email){

            if(is_string($id_or_email) && is_email($id_or_email) && $user = get_user_by('email', $id_or_email)){
                $user_id = $user->ID;
            } elseif (is_int($id_or_email) || is_string($id_or_email)) {
                $user_id = $id_or_email;
            } elseif (is_object($id_or_email) && $user = get_user_by('email', $id_or_email->comment_author_email)){
                $user_id = $user->ID;
            } else {
                return $url;
            }

            $attachment = get_user_meta($user_id, 'user_avatar', true);

            if(!empty($attachment['id']) && $image_url = wp_get_attachment_image_url($attachment['id'], 'thumbnail')){
                $url  = $image_url;
            }

            return apply_filters('jobboard_user_avatar_url', $url, $user_id, $attachment);
        }

        function show_admin_bar(){
            if(is_jb_candidate() || is_jb_employer()){
                show_admin_bar(false);
            }
        }

        function user_args(){
            return apply_filters('jobboard_admin_user_args', array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'             => 'jobboard_user',
                // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'         => '',
                // Name that appears at the top of your panel
                'display_version'      => '',
                // Version that appears at the top of your panel
                'menu_type'            => 'hidden',
                //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'       => false,
                // Show the sections below the admin menu item or not
                'menu_title'           => '',
                'page_title'           => '',
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key'       => '',
                // Set it you want google fonts to update weekly. A google_api_key value is required.
                'google_update_weekly' => true,
                // Must be defined to add google fonts to the typography module
                'async_typography'     => true,
                // Use a asynchronous font on the front end or font string
                //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                'admin_bar'            => false,
                // Show the panel pages on the admin bar
                'admin_bar_icon'       => '',
                // Choose an icon for the admin bar menu
                'admin_bar_priority'   => 50,
                // Choose an priority for the admin bar menu
                'global_variable'      => '',
                // Set a different name for your global variable other than the opt_name
                'dev_mode'             => false,
                'forced_dev_mode_off'  => true,
                // Show the time the page took to load, etc
                'update_notice'        => false,
                // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                'customizer'           => false,
                // Enable basic customizer support
                'open_expanded'        => true,                    // Allow you to start the panel in an expanded way initially.
                'disable_save_warn'    => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority'        => null,
                // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'          => '',
                // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'     => '',
                // Permissions needed to access the options panel.
                'menu_icon'            => '',
                // Specify a custom URL to an icon
                'last_tab'             => '',
                // Force your panel to always open to a specific tab (by id)
                'page_icon'            => '',
                // Icon displayed in the admin panel next to your menu_title
                'page_slug'            => '',
                // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
                'save_defaults'        => false,
                // On load save the defaults to DB before user clicks save or not
                'default_show'         => false,
                // If true, shows the default value next to each field that is not the default value.
                'default_mark'         => '',
                // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export'   => false,
                // Shows the Import/Export panel when not used as a field.
                'show_options_object'  => false,
                // CAREFUL -> These options are for advanced use only
                'transient_time'       => 60 * MINUTE_IN_SECONDS,
                'output'               => false,
                // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'           => false,
                // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                'footer_credit'     => false,
                // Disable the footer credit of Redux. Please leave if you can help it.

                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'             => '',
                // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'use_cdn'              => true,
                // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.
            ));
        }

        function user_sections(){
            global $user;

            $fields = array();
            if(is_jb_employer($user->ID)){
                $fields = array_merge(jb_get_option('employer-custom-fields', array()), jb_get_option('employer-social-fields', array()));
            } elseif (is_jb_candidate($user->ID)){
                $fields = array_merge(jb_get_option('candidate-custom-fields', array()), jb_get_option('candidate-social-fields', array()));
            } else {
                $fields[] = array(
                    'id'        => 'user_avatar',
                    'type'      => 'media',
                    'title'     => esc_html__( 'Avatar Image', 'jobboard' ),
                    'subtitle'  => esc_html__( 'Select or upload a avatar image.', 'jobboard' ),
                );
            }

            $fields    = apply_filters('jobboard_admin_profile_sections', $fields, $user);
            $user_keys = array_merge(jb_user_keys(), array('first_name', 'last_name', 'url'));
            foreach ($fields as $key => $field){
                if($field['type'] == 'heading' || in_array($field['id'], $user_keys)){
                    unset($fields[$key]);
                } elseif ($field['type'] == 'media' && (isset($field['input']) && $field['input'] == 'file')){
                    $fields[$key]['mode'] = false;
                } elseif ($field['type'] == 'select' && $field['id'] == 'job_specialisms'){
                    $fields[$key]['options'] = jb_get_specialism_options();
                }
            }

            return array(
                'basic' => array(
                    'title'     => esc_html__( 'JobBoard Profile', 'jobboard' ),
                    'id'        => 'basic',
                    'icon'      => 'el el-folder-open',
                    'fields'    => $fields
                )
            );
        }

        function user_profile_menu(){
            add_submenu_page(
                'profile.php',
                esc_html__( 'Extended Profile',  'jobboard' ),
                esc_html__( 'Extended Profile',  'jobboard' ),
                'manage_options',
                'jobboard-profile-edit',
                array( $this, 'user_admin' )
            );
            add_submenu_page(
                'user-edit.php',
                esc_html__( 'Extended Profile',  'jobboard' ),
                esc_html__( 'Extended Profile',  'jobboard' ),
                'manage_options',
                'jobboard-profile-edit',
                array( $this, 'user_admin' )
            );
        }

        function profile_nav($user = null, $active = 'WordPress'){
            if ( empty( $user->ID ) ) {
                return;
            }

            $edit_page = 'user-edit.php';
            if(get_current_user_id() == $user->ID){
                $edit_page = 'profile.php';
            }

            $edit_url = '';
            if ( is_user_admin() ) {
                $edit_url = user_admin_url( 'profile.php' );
            } elseif ( is_blog_admin() ) {
                $edit_url = admin_url( $edit_page );
            } elseif ( is_network_admin() ) {
                $edit_url = network_admin_url( $edit_page );
            }

            $jb_active = false;
            $wp_active = ' nav-tab-active';
            if ( 'JobBoard' === $active ) {
                $jb_active = ' nav-tab-active';
                $wp_active = false;
            }

            $query_args = array( 'user_id' => $user->ID );
            if ( ! empty( $_REQUEST['wp_http_referer'] ) ) {
                $query_args['wp_http_referer'] = urlencode( stripslashes_deep( $_REQUEST['wp_http_referer'] ) );
            }
            $community_url = add_query_arg(array_merge($query_args, array('page' => 'jobboard-profile-edit')), $edit_url);
            $wordpress_url = add_query_arg($query_args, $edit_url);
            ?>
            <h2 id="profile-nav" class="nav-tab-wrapper">
                <?php if ( current_user_can( 'edit_user', $user->ID ) ) : ?>
                    <a class="nav-tab<?php echo esc_attr( $wp_active ); ?>" href="<?php echo esc_url($wordpress_url);?>"><?php esc_html_e( 'Profile', 'jobboard' ); ?></a>
                <?php endif; ?>
                <a class="nav-tab<?php echo esc_attr( $jb_active ); ?>" href="<?php echo esc_url( $community_url );?>"><?php esc_html_e( 'Extended Profile', 'jobboard' ); ?></a>
            </h2>
            <?php
        }

        function user_admin(){
            global $redux_meta, $user;

            $user_id = (int) get_current_user_id();
            if ( ! empty( $_GET['user_id'] ) ) {
                $user_id = (int) $_GET['user_id'];
            }
            $request_url     = remove_query_arg( array( 'action', 'error', 'updated', 'spam', 'ham' ), $_SERVER['REQUEST_URI'] );
            $form_action_url = add_query_arg( 'action', 'rc-profile-update', $request_url );
            $user = get_user_to_edit( $user_id );
            ?>
            <div class="wrap" id="community-profile-page">
                <h1>
                    <?php esc_html_e( 'Extended Profile',  'jobboard' ); ?>
                    <a href="user-new.php" class="add-new-h2"><?php echo esc_html_x( 'Add New', 'user', 'jobboard' ); ?></a>
                </h1>
                <?php $this->profile_nav( $user, 'JobBoard' ); ?>
                <?php if ( ! empty( $user ) ) : ?>
                    <form action="<?php echo esc_url( $form_action_url ); ?>" id="your-profile" method="post">
                        <?php $redux_meta->user->add($this->user_args(), $this->user_sections()); ?>
                    </form>
                <?php else: ?>
                    <p><?php
                        printf(
                            '%1$s <a href="%2$s">%3$s</a>',
                            __( 'No user found with this ID.', 'jobboard' ),
                            esc_url( admin_url( 'users.php' ) ),
                            __( 'Go back and try again.', 'jobboard' )
                        );
                    ?></p>
                <?php endif; ?>
            </div>
            <?php
        }
    }

endif;