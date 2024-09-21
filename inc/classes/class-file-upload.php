<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class File_Upload {

    use Singleton;
    use Program_Logs;

    public function __construct() {
        $this->setup_hooks();
    }

    /**
     * Setup hooks for the file upload.
     */
    public function setup_hooks() {
        add_shortcode( 'file_upload_code', [ $this, 'handle_file_upload_callback' ] );
        add_action( 'rest_api_init', [ $this, 'register_api_endpoints' ] );
    }

    public function register_api_endpoints() {

        // server status
        register_rest_route( 'intakq/v1', '/server-status', [
            'methods'  => 'GET',
            'callback' => [ $this, 'intakq_server_status' ],
        ] );

        // call file upload
        register_rest_route( 'intakq/v1', '/file-upload', [
            'methods'  => 'GET',
            'callback' => [ $this, 'handle_file_upload_callback' ],
        ] );

    }

    public function intakq_server_status() {

        // For now, returning a sample response.
        return new \WP_REST_Response( [
            'success' => true,
            'message' => 'Server is up and running.',
        ], 200 );
    }

    /**
     * Handle file upload shortcode callback.
     */
    public function handle_file_upload_callback() {

        // Define file information
        $file_path    = PLUGIN_ASSETS_DIR_URL . 'images/cat.jpg'; // Use a valid file path
        $file_name    = 'cat.jpg'; // Set the correct file name
        $content_type = 'image/jpeg'; // MIME type for image
        $client_id    = 29; // Your client ID

        // Call the method to upload the file
        $response = $this->upload_file_to_intakeq( $client_id, $file_path, $file_name, $content_type );

        // Log the response
        $this->put_program_logs( 'Response: ' . $response );
    }

    /**
     * Upload file to the IntakeQ API using cURL.
     *
     * @param int    $client_id    The client ID.
     * @param string $file_path    The path to the file.
     * @param string $file_name    The file name.
     * @param string $content_type The MIME type of the file.
     *
     * @return string The API response or error message.
     */
    public function upload_file_to_intakeq( $client_id, $file_path, $file_name, $content_type ) {

        // Get the API key from the stored option
        $api_key = get_option( 'awpiq_api_key' );

        // Define the API endpoint URL
        $url = "https://intakeq.com/api/v1/files/$client_id";

        // Initialize the cURL session
        $ch = curl_init();

        // Prepare the file for upload
        $file = new \CURLFile( $file_path, $content_type, $file_name );

        // Prepare the POST fields
        $post_fields = [
            'file' => $file,
        ];

        // Set cURL options
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_fields );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        // Set headers for the request
        $headers = [
            'X-Auth-Key: ' . $api_key,
            'Content-Type: multipart/form-data',
        ];
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

        // Execute cURL request
        $response = curl_exec( $ch );

        // Check for cURL errors
        if ( curl_errno( $ch ) ) {
            $error_msg = curl_error( $ch );
            curl_close( $ch );
            return 'cURL Error: ' . $error_msg;
        }

        // Close the cURL session
        curl_close( $ch );

        // Return the API response
        return $response;
    }
}
