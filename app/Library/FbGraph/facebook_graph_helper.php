<?php namespace CsSeoMegaBundlePack\Library\FbGraph;
/**
 * Facebook Graph Helper
 * Graph Actions
 *
 * @package   Facebook Graph Helper
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */


class facebook_graph_helper {
    
    /**
     * Facebook Graph Version
     *
     * @var String
     */
    private $_fb_graph_version = 'v2.9';

    /**
     * Required Data Parameter
     *
     * @var Array 
     */
    private $_fb_data_set = array(
        'app_id' => '*', 
        'app_secret' => '*',
        'access_token' => '*',
        'page_id' => '*',
        'message' => '*',
        'picture' => '',
        'link' => ''
    );
    
    public function publish( $argc ){
        
        /********************check if parameter is not set or not array********************/
        if( empty( $argc ) || !is_array( $argc ) ){
            return array( 'response_code' => 101, 'response_text' => 'Please set a valid array data set.');
        }
        
        /********************check required parameters********************/
        foreach($this->_fb_data_set as $key_name => $required ){
            if(!empty($required) && (!isset($argc[$key_name]) || empty($argc[$key_name]))){
                return array( 'response_code' => 101, 'response_text' => 'Required parameter: "'. $key_name .'" not found in given data set or has\'nt setup.');
            }else{
                ${"$key_name"} = trim($argc[$key_name]);
            }
        }
        
        /********************set data********************/
        $_fb_data = array(
            'picture' => $picture,
            'link' => $link,
            'message' => $message
        );

        $fb_obj = new \Facebook\Facebook([
          'app_id' => $app_id,
          'app_secret' => $app_secret,
          'default_graph_version' => $this->_fb_graph_version,
        //  'default_access_token' => '{access-token}', // optional
        ]);
        
        try {
          // Returns a `Facebook\FacebookResponse` object
          $fb_response = $fb_obj->post("/$page_id/feed", $_fb_data, $access_token); //publish

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            return array( 'response_code' => 102, 'response_text' => 'Facebook Graph returned an response_text: ' . $e->getMessage());

        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return array( 'response_code' => 103, 'response_text'=> 'Facebook SDK returned an response_text: ' . $e->getMessage());
        }

        if(isset($fb_response)){
            $graphNode = $fb_response->getGraphNode();
            return array( 'response_code' => 200, 'response_text'=> isset($graphNode['id']) ? $graphNode['id'] : '');
        }
          
    }
    
    public function delete_post( $argc ){
        
        $fb_obj = new \Facebook\Facebook([
          'app_id' => $argc->app_id,
          'app_secret' => $argc->app_secret,
          'default_graph_version' => $this->_fb_graph_version,
        ]);
        
        try {
          // Returns a `Facebook\FacebookResponse` object
          return $fb_response = $fb_obj->delete("/$argc->post_id", array(), $argc->access_token); //publish

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            return array( 'response_code' => 102, 'response_text' => 'Facebook Graph returned an response_text: ' . $e->getMessage());

        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return array( 'response_code' => 103, 'response_text'=> 'Facebook SDK returned an response_text: ' . $e->getMessage());
        }
        
    }
    
    /**
     * Get Posts Stats
     * 
     * @param type $argc
     * @return type
     */
    public function get_stats( $argc ){
        
        $fb_obj = new \Facebook\Facebook([
          'app_id' => $argc->app_id,
          'app_secret' => $argc->app_secret,
          'default_graph_version' => $this->_fb_graph_version,
        ]);

        try{
            $fb_stats = $fb_obj->get("/$argc->post_id?fields=shares,likes{id},comments{id}", $argc->access_token); //get
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        
        $likes = $fb_stats->getGraphNode()->getField('likes');
        $shares = $fb_stats->getGraphNode()->getField('shares');
        $comments = $fb_stats->getGraphNode()->getField('comments');
        
        return array(
            'fb_likes' => isset( $likes[0]['id'] ) ? count($likes) : 0,
            'fb_comments' => isset( $shares['count'] ) ? $shares['count'] : 0,
            'fb_shares' => isset( $comments[0]['id'] ) ? count($comments) : 0,
        );
       
    }
    
    
    public function publish_by_curl($argc){
        
        /********************check if parameter is not set or not array********************/
        if( empty( $argc ) || !is_array( $argc ) ){
            return array( 'response_code' => 101, 'response_text' => 'Please set a valid array data set.');
        }
        
        /********************check required parameters********************/
        $data = array();
        foreach($this->_fb_data_set as $key_name => $required ){
            if(!empty($required) && (!isset($argc[$key_name]) || empty($argc[$key_name]))){
                return array( 'response_code' => 101, 'response_text' => 'Required parameter: "'. $key_name .'" not found in given data set or has\'nt setup.');
            }else{
                echo $data["$key_name"] = ${"$key_name"} = filter_var( trim( $argc[$key_name] ), FILTER_SANITIZE_STRING );
                echo "<br>";
            }
        }
        
//        post by curl
        $error = '';
        $post_url="https://graph.facebook.com/$page_id/feed";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $post_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret=curl_exec($ch);
        // Check if any error occurred
        if(curl_errno($ch)){
            $error =  curl_error($ch);
        }
        curl_close($ch);
        $s =json_decode($ret);

        echo "<pre>";
        print_r(json_decode($ret));
        print_r(json_decode($error));
        exit;
        
    }
    
}
