<?php

/* 
 * Plugin Name: ra7form
 * Author: rafaantonio
 * Text Domain: ra7form
 * Domain Path: /languages
 */

class Ra7form_Recipient {
    static $instance;
    public $id;    
    protected $act;
    protected $content;
    protected $forms;
    protected $pages;
    protected $page;
    protected $css;
    protected $jss;
    protected $js;
    protected $ss;

	public function __construct( $id ) {
        $this->id = $id;        
        $this->pages = [			
			'edit'	 => '/ra7form-editor-template.php',
            'report' => '/ra7form-report-template.php'			
        ];
        $this->jss = [			
            'edit'	 => 'js/ra7form-editor.js',
            'ajax'   => 'js/ra7form-editor-ajax.js',
            'report' => ''
        ];
        $this->css = [
            'edit'	 => 'css/ra7form-editor.css',
            'report' => ''            
        ];
        $this->act = filter_input( INPUT_GET, 'action' );
        $this->page = $this->pages[ $this->act ];
        $this->js = $this->jss[ $this->act ];
        $this->ss = $this->css[ $this->act ];
    }

	public function prepare_item() {
        $save_link = add_query_arg(
			array(
				'post' => absint( $this->id ),
				'page' => esc_attr( $_REQUEST['page'] )
			),
			menu_page_url( 'ra7form', false )
        );
        $this->content = [
            'title'    => get_the_title( $this->id ),
            'contains' => self::check_multiple_recipients( true ),
            '_mail'    => get_metadata_raw( 'post', $this->id, '_mail' ),
            '_form'    => get_metadata_raw( 'post', $this->id, '_form' )
        ];
        $this->content['_required'] = preg_match( '/\[select\* your-recipient/', $this->content[ '_form' ][ 0 ] );        
        $this->forms = self::populate_form();
    }

    private function populate_form() {
        if ( $this->js != '' ) {
            wp_register_script( 'ra7form-editor', plugin_dir_url( __DIR__ ) . $this->js, array( 'wp-i18n' ), '1.0', true );
            wp_enqueue_script( 'ra7form-editor' );
            wp_set_script_translations( 'ra7form-editor', 'ra7form', plugin_dir_path( dirname(__FILE__) ) . 'languages/' );
            
            //wp_enqueue_script( 'ra7form-editor', plugin_dir_url( __DIR__ ) . $this->js, array( 'wp-i18n' ) );
        }
        if ( $this->ss != '' ) {
            wp_enqueue_style( 'ra7form_page_css', plugin_dir_url( __DIR__ ) . $this->ss, array(), '0.0.1' );
        }
        ob_start();        	
        include_once( plugin_dir_path( __FILE__ ) . $this->page );
        self::ra7form_ajax_create();        
        return ob_get_clean();
    }

    private function check_multiple_recipients( $verbose = false ) {
        $pass[true] = [
            'This form does not contain multiple recipients with pipes.',
            'This form contains multiple recipients with pipes.'
        ];
        $pass[false] = [ false, true ];
        $str = $this->content[ '_form' ][ 0 ];
        $preg = preg_match( '/\[select(?:\*|) your-recipient/', $str) ;        
        return $pass[ $verbose ][ $preg ];
    }

    private function get_multiple_recipients( $title = false ) {
        $str = $this->content[ '_form' ][ 0 ];
        $p = array();
        if ( preg_match( '/\[select(?:\*|) your-recipient((.|\n|\r)*?)\]/', $str, $match ) == 1 ) {
            $val = $match[1];			
			$val = preg_replace( '/(^(\s|\r|\n|\s+)\")|((\"|\s|\r|\n|\s+)$)/', '', $val );
			$val = preg_replace( '/\"(\s|\r|\n|\s+)\"/', '""', $val );
			$val = explode( '""', $val );            
            foreach( $val as $v ) {
                $v = preg_replace( '/\"/', '', $v );
                $j = explode( '|', $v );
                $j[0] = trim( $j[0] );
                $j[1] = preg_replace( '/,(\s+|\s)/', ',', trim( $j[1] ) );
                $q = explode( ',', $j[1] );
                if ( $title ) {
                    array_push( $p, [ $j[0] => $q ] );
                } else {
                    array_push( $p, $j[0] );
                }
            }
            return $p;
        }
        return false;
    }

    private function check_nonce( $verbose = false ) {
        $nonce = $_REQUEST['_nonce'];
        $return = wp_verify_nonce( $nonce, 'ra7-edit-recipient-' . $this->id ) ? 1 : 0;
        return ( !$verbose ) ? $return : ( ( $return ) ? '' : wp_die( __( 'This link was expired.', 'ra7form' ), __( 'Validate Error', 'ra7form' ), [ 'back_link' => true ] ) );
    }

    private function mount_fields() {
        $content = '';
        $stags = '';
        $pattern = self::get_patterns( $this->act );
        if ( self::check_multiple_recipients() ) {
            $p = self::get_multiple_recipients( true );
            foreach( self::get_multiple_recipients( ) as $k => $v ) {
                $mails = '';
                $mails .= $v . ' | ';
                $content .= sprintf( $pattern['header'], $k, $v );
                foreach( $p[ $k ] as $r ) {
                    foreach( $r as $v ) {
                        if ( $v != '' ) {
                            $content .= sprintf(  $pattern['icons'], $v );
                            $mails .= $v . ', ';
                        }
                    }
                }                
                $mails = substr($mails, 0, -2);
                $stags = sprintf( '%s "%s"', $stags, $mails );                
                $content .= ( $pattern['hidden'] != '' ) ? sprintf(  $pattern['hidden'], $k, $mails ) : '';
            }
            $stags = sprintf( '[select* your-recipient %s]', $stags );
            $content .=  ( $pattern['footer'] != '' ) ? sprintf(  $pattern['footer'], $stags ) : '';
        } else {
            $content = __( 'Nothing to show.', 'ra7form' );
        }
        return $content;
    }

    public function display() {
        self::check_nonce( true );
        echo $this->forms;
    }

    protected function get_patterns( $pattern = 'edit' ) {
        $pat = [
            'edit' => [
                'header' => '                
                <tr id=\'rp_%1$s\' class=\'recipient-prop\' >
                    <td><a class=\'remRec\'><span  rel=\'%1$s\'class=\'dashicons dashicons-trash removeItem\'></span></a>
                    <input type=\'text\' id=\'titleRecipient_%1$s\' value=\'%2$s\' class=\'widefat\'></td>                    
                </tr>
                <tr id=\'cp_%1$s\'><td><span id=\'tag_%1$s\' class=\'tag\' rel=\'%1$s\'>
                    <span class=\'inputSpan\'>
                    <input type=\'text\' id=\'mailRecipient_%1$s\' value=\'\' class=\'thick-box\'/>
                    <a id=\'addMail_%1$s\'><span rel=\'%1$s\' class=\'dashicons dashicons-plus addInItem\'></span></a></span>',
                'icons'  => '<span class=\'thickbox button\'><span class=\'dashicons dashicons-trash removeInItem\'></span>%1$s</span>',
                'hidden' => '</span></td></tr>',
                'footer' => '</span></td></tr>'
            ],
            'report' => [
                'header' => '<tr class=\'recipient-prop\'><td name=\'%1$s\'>%2$s</td><td>',
                'icons'  => '<li>%1$s</li>',
                'hidden' => '',
                'footer' => '</td></tr>'
            ]
        ];
        return $pat[ $pattern ];
    }
    private function ra7form_ajax_create() {        
        wp_register_script( 'ra7form_ajax_js', plugin_dir_url( __DIR__ ) . $this->jss['ajax'] );        
        wp_localize_script(
            'ra7form_ajax_js',
            'ra7form_globals',
            [
                'ajax_url'      => admin_url( 'admin-ajax.php' ),
                '_id'           => $this->id,
                '_ajax_nonce'   => $_REQUEST['_nonce']
            ]
        );
        wp_enqueue_script( 'ra7form_ajax_js' );                   
    }
}

function ra7form_ajax_save() {
    if ( !wp_verify_nonce( filter_input( INPUT_POST, '_ajax_nonce' ), 'ra7-edit-recipient-' . filter_input( INPUT_POST, 'id' ) ) ) {
        echo $response['success'] = false;
        die();
    }

    $response = [
        'id'         => filter_input( INPUT_POST, 'id' ),
        'sender'     => filter_input( INPUT_POST, 'sender' ),
        'recipient'  => filter_input( INPUT_POST, 'recipient' ),
        'content'    => filter_input( INPUT_POST, 'content' )        
    ];

    $getMetaData = [
        '_mail'     => get_metadata( 'post', $response['id'], '_mail' ),
        '_mail_2'   => get_metadata( 'post', $response['id'], '_mail_2' ),
        '_messages' => get_metadata( 'post', $response['id'], '_messages' ),
        '_additional_settings' => get_metadata( 'post', $response['id'], '_additional_settings' )
    ];

    $_getMetaData = $getMetaData['_mail'][0];
    $getMetaData['_mail'][0]['sender'] = $response['sender'];
    $getMetaData['_mail'][0]['recipient'] = $response['recipient'];

    foreach( $getMetaData as $g ) {
        if ( is_array( $g ) ) { foreach( $g[0] as $v ) { $ncontent .= $v . PHP_EOL; }
        } else { $ncontent .= $g . PHP_EOL; }
    }

    $myPost = [
        'ID'           => $response['id'],
        'post_content' => $response['content'] . PHP_EOL . $ncontent
    ];

    $success = [
        '_content'  => wp_update_post( $myPost ) ? true : false,
        '_form'     => update_post_meta( $response['id'], '_form', $response['content'] ),
        '_mail'     => update_post_meta( $response['id'], '_mail', $getMetaData['_mail'][0], $_getMetaData[0] )    
    ];

    $success = json_encode($success);
	echo $success;	
	die();
}

add_action( 'wp_ajax_ra7form_ajax_save' , 'ra7form_ajax_save' );