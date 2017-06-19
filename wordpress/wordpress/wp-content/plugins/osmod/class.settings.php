<?php
class OsmodSettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'OSMOD SETTINGS', 
            'OSMOD Settings', 
            'manage_options', 
            'osmod-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'osmod_option' );
        ?>
        <style>
            .osmod-settings-notes {
                padding: 20px 0;
                font-size:12px;
            }
            .osmod-settings-options {
                font-size:12px;
                width:100%;
                border-top:1px solid #000;
                padding-top:30px;
            }
            .osmod-settings-options input[type=text] {
                width:80%;
            }
        </style>
        <div class="wrap">
            <h1>OSMOD Settings</h1>
            <div class="osmod-settings-notes">
                <strong>Note: Enabling this plugin forces the following WordPress settings to be enabled.</strong>
                <ul>
                    <li>- Comment author must fill out name and email.</li>
                    <li>- Comment must be manually approved.</li>
                </ul>
            </div>
            <form method="post" action="options.php" class="osmod-settings-options">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'osmod-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'osmod_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            '', // Title
            array( $this, 'print_section_info' ), // Callback
            'osmod-setting-admin' // Page
        );  

        add_settings_field(
            'api_token_setting', // ID
            'API Token', // Title 
            array( $this, 'token_callback' ), // Callback
            'osmod-setting-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'url_setting', 
            'Server API Url', 
            array( $this, 'url_callback' ), 
            'osmod-setting-admin', 
            'setting_section_id'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['token'] ) )
            $new_input['token'] = sanitize_text_field( $input['token'] );

        if( isset( $input['url'] ) )
            $new_input['url'] = sanitize_text_field( $input['url'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter the API Token and the server url endpoint below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function token_callback()
    {
        printf(
            '<input type="text" id="token" name="osmod_option[token]" value="%s" />',
            isset( $this->options['token'] ) ? esc_attr( $this->options['token']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function url_callback()
    {
        printf(
            '<input type="text" id="url" name="osmod_option[url]" value="%s"/>',
            isset( $this->options['url'] ) ? esc_attr( $this->options['url']) : ''
        );
    }
}