<?php

namespace TgUtils;

/**
 * Analyzes URLs and its components.
 * @author ralph
 *        
 */
class URL {

    protected $components;
    
    /**
     * Constructor.
     * @param mixed string $url as string or other URL object.
     */
    public function __construct($url) {
        if (is_string($url)) {
            // Trim to avoid problem with parsing
            $url = trim($url);
            // Ad the scheme if it is missing
            if (strpos($url, '://') === FALSE) {
                $url = 'http://'.$url;
            }
            $this->components = parse_url($url);
            
            // And fixing some potential issues
            $this->fixIssues();            
        } else if (is_object($url) && is_a($url, 'TgUtils\\URL')) {
            $this->components = $url->components;
        }
    }

    protected function fixIssues() {
        $parts = array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment');
        foreach ($parts AS $part) {
            if (!isset($this->components[$part])) {
                $this->components[$part] = NULL;
            }
        }
        
        if (!is_numeric($this->components['port'])) {
            $this->components['port'] = 0;
        }
        if ($this->components['path'] == NULL) {
            $this->components['path'] = '/';
        }
        if (substr($this->components['path'], 0, 1) != '/') {
            $this->components['path'] = '/' . $this->components['path'];
        }
    }
    
    /**
     * Returns the URL scheme.
     * @return string the scheme
     */
    public function getScheme() {
        return $this->components['scheme'];
    }
    
    /**
     * Modify this URL by setting a new scheme.
     * @param string $scheme - the new scheme
     */
    public function setScheme($scheme) {
        $this->components['scheme'] = $scheme;
    }
    
    /**
     * Returns the URL's host.
     * @return string the host
     */
    public function getHost() {
        return $this->components['host'];
    }
    
    /**
     * Modify this URL by setting a new host.
     * @param string $host - the new host
     */
    public function setHost($host) {
        $this->components['host'] = $host;
    }
    
    /**
     * Returns the URL's port.
     * @return string the port
     */
    public function getPort() {
        return $this->components['port'];
    }
    
    /**
     * Modify this URL by setting a new port.
     * @param int $port - the new port
     */
    public function setPort($port) {
        $this->components['port'] = $port;
        $this->fixIssues();
    }
    
    /**
     * Returns the URL's user.
     * @return string the user
     */
    public function getUser() {
        return $this->components['user'];
    }
    
    /**
     * Modify this URL by setting a new user.
     * @param string $user - the new user
     */
    public function setUser($user) {
        $this->components['user'] = $user;
    }
    
    /**
     * Returns the URL's password.
     * @return string the password
     */
    public function getPassword() {
        return $this->components['pass'];
    }
    
    /**
     * Modify this URL by setting a new password.
     * @param string $password - the new password
     */
    public function setPassword($password) {
        $this->components['pass'] = $password;
    }
    
    /**
     * Returns the URL's path.
     * @return string the path
     */
    public function getPath() {
        return $this->components['path'];
    }
    
    /**
     * Modify this URL by setting a new path.
     * @param string $path - the new path
     */
    public function setPath($path) {
        $this->components['path'] = $path;
        $this->fixIssues();
    }
    
    /**
     * Returns the URL's query string.
     * @return string the query string
     */
    public function getQuery() {
        return $this->components['query'];
    }
    
    /**
     * Modify this URL by setting a new query string.
     * @param string $query - the new query string
     */
    public function setQuery($query) {
        $this->components['query'] = $query;
    }
    
    /**
     * Returns the URL's fragment.
     * @return string the fragment
     */
    public function getFragment() {
        return $this->components['fragment'];
    }
    
    /**
     * Modify this URL by setting a new fragment.
     * @param string $fragment - the new fragment
     */
    public function setFragment($fragment) {
        $this->components['fragment'] = $fragment;
    }
    
    public function __toString() {
        // Scheme
        $rc = $this->components['scheme'].'://';
        
        // User
        if ($this->components['user'] != NULL) {
            $rc .= $this->components['user'];
            
            // Password
            if ($this->components['pass'] != NULL) {
                $rc .= ':'.$this->components['pass'];
            }
            
            $rc .= '@';
        }
        
        // Host
        $rc .= $this->components['host'];
        
        // Port
        if ($this->components['port'] != 0) {
            $rc .= ':'.$this->components['port'];
        }
        
        // Path
        $rc .= $this->components['path'];
        
        // Query
        if ($this->components['query'] != NULL) {
            $rc .= '?'.$this->components['query'];
        }
        
        // Fragment
        if ($this->components['fragment'] != NULL) {
            $rc .= '#'.$this->components['fragment'];
        }
        
        return $rc;
    }
    
    /**
     * Returns whether to URLs are equal.
     * <p>Equality is defined as the equality of stringified URLs.</p>
     * @return boolean TRUE or FALSE
     */
    public function equals($another) {
        if (is_string($another)) {
            $other = new URL($another);
            return $this->__toString() == $other->__toString();
        } else if (is_object($another) && is_a($another, 'TgUtils\\URL')) {
            return $this->__toString() == $another->__toString();
        }
        return FALSE;
    }
}

