<?php

/**
 * PHP version 8.1.0
 * 
 * @author Youn Mélois <youn@melois.dev>
 */

// Basice parameters to connect to the database
const DB_SERVER = '127.0.0.1';
const DB_PORT = '5432';
const DB_NAME = 'interpromos';
const DB_USER = 'postgres';
const DB_PASSWORD = '';

/*
	Creating constants for heavily used paths makes things a lot easier.
	ex. require_once(LIBRARY_PATH . "Paginator.php")
*/
defined("LIBRARY_PATH")
    or define("LIBRARY_PATH", realpath(dirname(__FILE__) . '/library'));
