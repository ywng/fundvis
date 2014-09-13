<?php
class SendMessage
{
    /**
     * @author Nishchal Gautam <gautam.nishchal@gmail.com
     * @license http://nishgtm.com DBAD licence
     * 
     */
    private $AccessToken, $Facebook, $appId, $STREAM_XML, $AUTH_XML, $CLOSE_XML, $SESSION_XML, $RESOURCE_XML, $START_TLS, $appSecret;
    private $filehandle, $uid, $userName, $server, $options;
    /**
     * 
     * @param object $Facebook The facebook object after the configuring of normal facebook api
     * @throws Exception if there is some error, loads every required resources otherwise.
     * @access public
     */
    public function __construct( $Facebook )
    {
        //initialize everything we need, from facebook to xml's
        $this->Facebook     = $Facebook;
        $this->appId        = $Facebook->getAppId();
        $this->uid          = $Facebook->getUser();
        $data               = $Facebook->api( '/me' );
       // $first_name= strtolower(preg_replace('/\s+/', '', $data['first_name']));
       // $last_name= strtolower(preg_replace('/\s+/', '', $data['last_name']));
        //$this->userName     = $first_name."\.".$last_name;
        $this->userName     = $data['username'];
        $this->appSecret    = $Facebook->getAppSecret();
        $this->AccessToken  = $Facebook->getAccessToken();
        $this->STREAM_XML   = '<stream:stream ' . 'xmlns:stream="http://etherx.jabber.org/streams" ' . 'version="1.0" xmlns="jabber:client" to="chat.facebook.com" ' . 'xml:lang="en" xmlns:xml="http://www.w3.org/XML/1998/namespace">';
        $this->AUTH_XML     = '<auth xmlns="urn:ietf:params:xml:ns:xmpp-sasl" ' . 'mechanism="X-FACEBOOK-PLATFORM"></auth>';
        $this->CLOSE_XML    = '</stream:stream>';
        $this->RESOURCE_XML = '<iq type="set" id="3">' . '<bind xmlns="urn:ietf:params:xml:ns:xmpp-bind">' . '<resource>fb_xmpp_script</resource></bind></iq>';
        $this->SESSION_XML  = '<iq type="set" id="4" to="chat.facebook.com">' . '<session xmlns="urn:ietf:params:xml:ns:xmpp-session"/></iq>';
        $this->START_TLS    = '<starttls xmlns="urn:ietf:params:xml:ns:xmpp-tls"/>';
        $this->server       = 'chat.facebook.com';
        $this->options      = array(
             'uid' => $this->userName . "@chat.facebook.com",
            'app_id' => $this->appId,
            'server' => $this->server 
        );
        $errno="";
        $errstr=0;
        $fh                 = fsockopen( $this->server, 5222, $errno, $errstr );
        if ( $fh ) {
            $this->filehandle = $fh;
        } //$fh
        else {
            throw new Exception( "fsockopen is not working, may be your server is restricting you from making outbound connections", "bad_request");
        }
    }
    /**
     * Responsible for generating Access Token
     * @return str access token
     * @access private
     */
    private function GetAccessToken()
    {
        //get access token from facebook object passed by user
        $facebook    = $this->Facebook;
        $AccessToken = $facebook->getAccessToken();
        return $AccessToken;
    }
    
    /**
     * 
     * @param filehandle $fh
     * @param xml $xml
     * @access private
     * @author Nishchal Gautam <gautam.nishchal@gmail.com
     */
    private function send_xml( $fh, $xml )
    {
        fwrite( $fh, $xml );
    }
    /**
     * 
     * @param type $fp
     * @param type $size
     * @return null or array
     * @access private
     */
    private function recv_xml( $fp, $size = 4096 )
    {
        $xml = fread( $fp, $size );
        if ( !preg_match( '/^</', $xml ) ) {
            $xml = '<' . $xml;
        } //!preg_match( '/^</', $xml )
        if ( $xml === "" ) {
            return null;
        } //$xml === ""
        $xml_parser = xml_parser_create();
        xml_parse_into_struct( $xml_parser, $xml, $val, $index );
        xml_parser_free( $xml_parser );
        return array(
             $val,
            $index 
        );
    }
    /**
     * 
     * @staticvar null $val
     * @staticvar null $index
     * @param type $fp
     * @param type $tag
     * @param type $value
     * @param type $ret
     * @return boolean
     * @access private
     */
    private function find_xmpp( $fp, $tag, $value = null, &$ret = null )
    {
        //function provided by facebook, DON'T ATTEMPT to change
        static $val = null, $index = null;
        
        do {
            if ( $val === null && $index === null ) {
                list( $val, $index ) = $this->recv_xml( $fp );
                if ( $val === null || $index === null ) {
                    return false;
                } //$val === null || $index === null
            } //$val === null && $index === null
            
            foreach ( $index as $tag_key => $tag_array ) {
                if ( $tag_key === $tag ) {
                    if ( $value === null ) {
                        if ( isset( $val[$tag_array[0]]['value'] ) ) {
                            $ret = $val[$tag_array[0]]['value'];
                        } //isset( $val[$tag_array[0]]['value'] )
                        return true;
                    } //$value === null
                    foreach ( $tag_array as $i => $pos ) {
                        if ( $val[$pos]['tag'] === $tag && isset( $val[$pos]['value'] ) && $val[$pos]['value'] === $value ) {
                            $ret = $val[$pos]['value'];
                            return true;
                        } //$val[$pos]['tag'] === $tag && isset( $val[$pos]['value'] ) && $val[$pos]['value'] === $value
                    } //$tag_array as $i => $pos
                } //$tag_key === $tag
            } //$index as $tag_key => $tag_array
            $val = $index = null;
        } while ( !feof( $fp ) );
        
        return false;
    }
    
    
    /**
     * Opens a connection using fsockopen()
     * @return boolean Returns true if message sending is sucessful and false otherwise
     * @access public
     * @param string $body the body of the message
     * @param string $to receiver's user id, either the username or the ID, both are taken care of.
     */
    public function sendMessage( $body, $to )
    {
        $facebook     = $this->Facebook;
        $receiverData = $facebook->api( $to );
        $receiverId   = $receiverData['id'];
        $fp           = $this->filehandle;
        $this->send_xml( $fp, $this->STREAM_XML );
        if ( !$this->find_xmpp( $fp, 'STREAM:STREAM' ) ) {
            return false;
        } //!$this->find_xmpp( $fp, 'STREAM:STREAM' )
        if ( !$this->find_xmpp( $fp, 'MECHANISM', 'X-FACEBOOK-PLATFORM' ) ) {
            return false;
        } //!$this->find_xmpp( $fp, 'MECHANISM', 'X-FACEBOOK-PLATFORM' )
        // starting tls - MANDATORY TO USE OAUTH TOKEN!!!!
        $this->send_xml( $fp, $this->START_TLS );
        if ( !$this->find_xmpp( $fp, 'PROCEED', null, $proceed ) ) {
            return false;
        } //!$this->find_xmpp( $fp, 'PROCEED', null, $proceed )
        stream_socket_enable_crypto( $fp, TRUE, STREAM_CRYPTO_METHOD_TLS_CLIENT );
        $this->send_xml( $fp, $this->STREAM_XML );
        if ( !$this->find_xmpp( $fp, 'STREAM:STREAM' ) ) {
            return false;
        } //!$this->find_xmpp( $fp, 'STREAM:STREAM' )
        if ( !$this->find_xmpp( $fp, 'MECHANISM', 'X-FACEBOOK-PLATFORM' ) ) {
            return false;
        } //!$this->find_xmpp( $fp, 'MECHANISM', 'X-FACEBOOK-PLATFORM' )
        // gets challenge from server and decode it
        $this->send_xml( $fp, $this->AUTH_XML );
        if ( !$this->find_xmpp( $fp, 'CHALLENGE', null, $challenge ) ) {
            return false;
        } //!$this->find_xmpp( $fp, 'CHALLENGE', null, $challenge )
        $challenge = base64_decode( $challenge );
        $challenge = urldecode( $challenge );
        parse_str( $challenge, $challenge_array );
        // creates the response array
        $resp_array = array(
             'method' => $challenge_array['method'],
            'nonce' => $challenge_array['nonce'],
            'access_token' => $this->AccessToken,
            'api_key' => $this->appId,
            'call_id' => 0,
            'v' => '1.0' 
        );
        // creates signature
        $response   = http_build_query( $resp_array );
        // sends the response and waits for success
        $xml        = '<response xmlns="urn:ietf:params:xml:ns:xmpp-sasl">' . base64_encode( $response ) . '</response>';
        $this->send_xml( $fp, $xml );
        if ( !$this->find_xmpp( $fp, 'SUCCESS' ) ) {
            return false;
        } //!$this->find_xmpp( $fp, 'SUCCESS' )
        // finishes auth process
        $this->send_xml( $fp, $this->STREAM_XML );
        if ( !$this->find_xmpp( $fp, 'STREAM:STREAM' ) ) {
            return false;
        } //!$this->find_xmpp( $fp, 'STREAM:STREAM' )
        if ( !$this->find_xmpp( $fp, 'STREAM:FEATURES' ) ) {
            return false;
        } //!$this->find_xmpp( $fp, 'STREAM:FEATURES' )
        $this->send_xml( $fp, $this->RESOURCE_XML );
        if ( !$this->find_xmpp( $fp, 'JID' ) ) {
            return false;
        } //!$this->find_xmpp( $fp, 'JID' )
        $this->send_xml( $fp, $this->SESSION_XML );
        if ( !$this->find_xmpp( $fp, 'SESSION' ) ) {
            return false;
        } //!$this->find_xmpp( $fp, 'SESSION' )
        $MESSAGE = '<message from="" to="-' . $receiverId . '@chat.facebook.com">
                          <body>' . $body . '</body>
                      </message>';
        $this->send_xml( $fp, $MESSAGE );
        $this->send_xml( $fp, $this->CLOSE_XML );
        fclose( $fp );
        return true;
    }
}
?>
