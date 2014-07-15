<?php

/*
 * Controller name: MU
 * Controller description: Blog creation controller for JSON API
 * Controller Author: Quique Torras
 */

class JSON_API_MU_Controller {
	
	/* Creates a new blog calling wpmu_create_blog
	 * the wpmu_create_blog parameters are:
	 * $domain  The domain of the new blog.
	 * $path    The path of the new blog.
	 * $title   The title of the new blog.
	 * $user_id The user id of the user account who will be the blog admin. (you can use an email instead of the user_id. If so, a new user will be created)
	 * $username The Username if we go to create a new user
	 * $password The password if we go to create a new user
	 * $meta    Other meta information.
	 * $site_id The site_id of the blog to be created.
	 */ 
    public function create(){
	    global $json_api;
	  
        $parameters['domain'] = sanitize_text_field($_REQUEST['domain']);
        $parameters['path'] = sanitize_text_field($_REQUEST['path']);
        $parameters['title'] = sanitize_text_field($_REQUEST['title']);
        $parameters['user_id'] = sanitize_text_field($_REQUEST['user_id']);
        $parameters['username'] = sanitize_text_field($_REQUEST['username']);
        $parameters['meta'] = sanitize_text_field($_REQUEST['meta']);
        $parameters['site_id'] = sanitize_text_field($_REQUEST['site_id']);
        $parameters['password'] = sanitize_text_field($_REQUEST['password']);
        
        if ('' == $parameters['site_id']) $parameters['site_id'] = 1;              
        
		if ('' == $parameters['domain']) {
			header("HTTP/1.0 400 Params error");
			return []; 
		}
		if ('' == $parameters['path']) {
			header("HTTP/1.0 400 Params error");
			return [];
		}
		if ('' == $parameters['user_id']) {
			header("HTTP/1.0 400 Params error");
			return [];
		}
		if ('' == $parameters['username']) {
			header("HTTP/1.0 400 Params error");
			return [];
		}
		
        // if the user_id is the user's e-mail
        if (!is_int($parameters['user_id']) ) {
        	if (!($user_id = get_user_id_from_string($parameters['user_id'])) ) {
        		$error = wpmu_validate_user_signup(
        				$parameters['username'],
        				$parameters['user_id']
        		);
        		
        		if ('' != $error['errors']->get_error_code()) {
        			header("HTTP/1.0 400 Bad params");
        			return ['body' => $error['errors']];
        		}
        		if ('' == $parameters['password']) {
        			$parameters['password'] = wp_generate_password();
        		}
        		$user_id = wpmu_create_user(
        				$parameters['username'],
        				$parameters['password'],
        				$parameters['user_id']
        		);
        	}
        	// User found by email, set user_id param with user id       
        	$parameters['user_id'] = $user_id;        	
        }
        else {
        	// Comprobar que existe el id
        }

        if ($this->findBlog($parameters['domain'], $parameters['path']) !== false) {
        	header("HTTP/1.0 409 Site already exists");
			return [];
        }
        
        $id_blog = wpmu_create_blog(
        		$parameters['domain'],
        		$parameters['path'],
        		$parameters['title'],
        		$parameters['user_id'],
        		$parameters['meta'],
        		$parameters['site_id']
        );
        return ['blog_id' => $id_blog];
    }
   
   
    public function getBlogId()
    {
	    global $json_api;

        $domain = sanitize_text_field( $_REQUEST['domain'] );
        $path = sanitize_text_field( $_REQUEST['path'] );

        if ('' == $domain || '' == $path) {
            $json_api->error("You must include 'domain' and 'path' var in your request.", "KO");
        }

        return array($this->findBlog($domain, $path));
    }

    private function findBlog($domain, $path)
    {
        global $wpdb;
        $domain_found = $wpdb->get_results($wpdb->prepare(
            "SELECT blog_id FROM wp_blogs WHERE domain = %s AND path = %s LIMIT 1",
            $domain,
            $path . '/'
        ));

        if ( !count($domain_found) ) {
            return false;
        }
        return array('blog_id' => $domain_found[0]->blog_id);
    }    
}
