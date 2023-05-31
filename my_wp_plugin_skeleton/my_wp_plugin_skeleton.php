<?php
/*
Plugin Name: My wp plugin skeleton
Plugin URI: https://rosander.no
Description: A plugin skeleton for WordPress
Version: 1.0.0
Author: Tore André Rosander
Author URI: https://rosander.no
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: my-wp-plugin-skeleton


*/

// Create the database table on plugin activation
function mwps_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mwps_entries';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(4) NOT NULL AUTO_INCREMENT,
        mwps_entry VARCHAR(500) NOT NULL,
        mwps_created DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

register_activation_hook( __FILE__, 'mwps_create_table' );

// Add the admin page
function mwps_admin_menu() {
    add_menu_page('My wp plugin skeleton', 'My wp plugin skeleton', 'manage_options', 'mwps_admin_page', 'mwps_admin_page_callback', 'dashicons-admin-generic');
}

add_action( 'admin_menu', 'mwps_admin_menu' );

// Render the admin page
function mwps_admin_page_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mwps_entries';

    // Check if form has been submitted before loading the rest of the page
    if(isset($_POST['mwps_entry'] ) && ! empty( $_POST['mwps_entry'])) {
        if(!isset($_POST['mwps_entry_nonce'] ) || ! wp_verify_nonce( $_POST['mwps_entry_nonce'], 'mwps_entry_action' ) ) {
            return; // Nonce did not verify
        }
        
        $mwps_entry = sanitize_text_field( $_POST['mwps_entry'] );

        // Write entry into the database
        $result = $wpdb->insert(
            $table_name,
            array(
                'mwps_entry' => $mwps_entry,
            ),
            array(
                '%s',
            )
        );

        if(false === $result) {
            // Error handling
        }
    }

    // Delete entry
    if(isset($_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['entry_id'])) {
        $entry_id = sanitize_key( $_GET['entry_id'] );

        if(!is_numeric($entry_id)) {
            return; // invalid entry_id
        }
        
        $entry_id = absint($entry_id);

        // Delete entry from the database
        $wpdb->delete(
            $table_name,
            array(
                'id' => $entry_id,
            ),
            array(
                '%d',
            )
        );
    }

    // Retrieve all entries from the database
    $entries = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    // Display the admin page content
    ?>
    <div class="wrap">
        <h1>My wp plugin skeleton</h1>

        <form method="POST" action="">
            <label for="mwps_entry">Entry:</label>
            <input type="text" name="mwps_entry" id="mwps_entry" required>
            <?php wp_nonce_field( 'mwps_entry_action', 'mwps_entry_nonce' ); ?>
            <input type="submit" value="Add Entry" class="button button-primary">
        </form>

        <hr>

        <h2>Current Entries</h2>
        <?php if(!empty($entries)){ ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Entry</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($entries as $entry) { ?>
                        <tr>
                            <td><?php echo esc_html( $entry['id'] ); ?></td>
                            <td><?php echo esc_html( $entry['mwps_entry'] ); ?></td>
                            <td><?php echo esc_html( $entry['mwps_created'] ); ?></td>
                            <td>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=mwps_admin_page&action=delete&entry_id=' . $entry['id'] ) ); ?>" onclick="return confirm('Are you sure you want to delete this entry?')">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>No entries found.</p>
        <?php } ?>
    </div>
    <?php
}

// Create the REST API endpoint
function mwps_rest_api_init() {
    register_rest_route( 'mwps/v1', '/entries', array(
        'methods'  => 'GET',
        'callback' => 'mwps_get_entries',
    ) );
}
add_action( 'rest_api_init', 'mwps_rest_api_init' );

// Retrieve entries for the REST API
function mwps_get_entries() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mwps_entries';

    $entries = $wpdb->get_results( "SELECT mwps_entry, mwps_created FROM $table_name", ARRAY_A );

    return rest_ensure_response( $entries );
}

// Create the shortcode to display entries on the frontend
function mwps_entries_shortcode( $atts ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mwps_entries';

    $entries = $wpdb->get_results( "SELECT mwps_entry, mwps_created FROM $table_name", ARRAY_A );

    if(!empty($entries)) {
        $output = '<ul>';
        foreach ( $entries as $entry ) {
            $output .= '<li>' . esc_html( $entry['mwps_entry'] ) . ' - ' . esc_html( $entry['mwps_created'] ) . '</li>';
        }
        $output .= '</ul>';

        return $output;
    }

    return 'No entries found.';
}
add_shortcode( 'mwps_entries', 'mwps_entries_shortcode' );
