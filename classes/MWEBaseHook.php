<?php
/*
 * Copyright (C) 2016 Mark W. Eastwood 
 */
namespace mwesim;

// is call from WP
defined('ABSPATH') or die("Invalid ...\n"); 

/**
 * Hook - a base class that acts as a wrapper for WP hooks
 *
 * Author:      Mark Eastwood <markweastwood.co.uk>
 */
class MWEBaseHook
{
    // When a callback is a standard function it should include it's namespace
    
    /**
     * Actions - each action is an array of add_action() args
     * [string $tag, callable $function_to_add, int $priority, int $accepted_args]
     * 
     * @var array
     */
    public $actions;
    
    
    /**
     * Filters - each action is an array of add_filter() args
     * [string $tag, callable $function_to_add, int $priority, int $accepted_args]
     * 
     * @var array
     */
    public $filters;
    
    
    /*------------------------------------------------------------------------*/        
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addActions();
        $this->addFilters();
    }
    
    
    /**
     * Add action hooks
     */
    public function addActions()
    {
        if ( ! is_array( $this->actions ) 
                || empty( $this->actions ) ) { return; }
                
        $obj = new \ReflectionFunction('\add_action');
        foreach ( $this->actions as $args ) {
            if ( count( $args ) > 1 ) {
                $method = $args[1];
                $args[1] = $this->addCallback( $method );
                $obj->invokeArgs( $args );
            }
         }
     }
    
    
    /**
     * Add filter hooks
     */
    public function addFilters()
    {
        if ( ! is_array( $this->filters ) 
                || empty( $this->filters ) ) { return; }
                
        $obj = new \ReflectionFunction('\add_filter');
        foreach ( $this->filters as $args ) {
            if ( ! is_array( $args ) ) { continue; }
            if ( count( $args ) > 1 ) {
                $args[1] = $this->addCallback( $args[1] );
                $obj->invokeArgs( $args );
            }
         }        
    }
    
    
    /**
     * Add callback
     * 
     * If the named callback does not exist as a method in the child class 
     * it is set 'as is' i.e. assumed to be either a static method or standard function. 
     * 
     * @param string $callback      - name of callback to add to hook
     * @return mixed                - array if callable is an instance method, string if not
     */
    public function addCallback( $callback )
    { 
        if ( method_exists( $this, $callback ) ) {
            return array( &$this, $callback );
        }
        
        return $callback;
    }
     
}
