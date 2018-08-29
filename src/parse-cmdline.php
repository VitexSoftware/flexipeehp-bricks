<?php
/**
 * FlexiPeeHP Bricks - Commandline parameters parser
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

    /**
     * Parse Commandline
     *
     * @return array
     */
    function parseCmdline()
    {
        $optionsParsed = [];
        $shortopts     = "";
        $shortopts     .= "s:";  // Server
        $shortopts     .= "u:";  // Username
        $shortopts     .= "p:";  // Password
        $shortopts     .= "c:";  // Company
        $shortopts     .= "f:";  // Config
        $shortopts     .= "d";  // Debug

        $longopts = array(
            "server:", // Server
            "username:", // Username
            "password:", // Password
            "company:", // Company
            "file:", // Config file
            "debug", // Debug
        );
        $options  = getopt($shortopts, $longopts);

        if (array_key_exists('s', $options)) {
            $optionsParsed['url'] = $options['s'];
        }
        if (array_key_exists('server', $options)) {
            $optionsParsed['url'] = $options['server'];
        }
        if (array_key_exists('c', $options)) {
            $optionsParsed['company'] = $options['c'];
        }
        if (array_key_exists('company', $options)) {
            $optionsParsed['company'] = $options['company'];
        }
        if (array_key_exists('u', $options)) {
            $optionsParsed['user'] = $options['u'];
        }
        if (array_key_exists('user', $options)) {
            $optionsParsed['user'] = $options['user'];
        }

        if (array_key_exists('p', $options)) {
            $optionsParsed['password'] = $options['p'];
        }
        if (array_key_exists('password', $options)) {
            $optionsParsed['password'] = $options['password'];
        }

        if (array_key_exists('f', $options)) {
            $optionsParsed['config'] = $options['f'] ? $options['f'] : '/etc/flexibee/client.json';
        }

        if (array_key_exists('file', $options)) {
            $optionsParsed['config'] = $options['file'] ? $options['file'] : '/etc/flexibee/client.json';
        }

        if (array_key_exists('config', $optionsParsed)) {
            \Ease\Shared::instanced()->loadConfig($optionsParsed['config'],true);
        }

        if (array_key_exists('d', $options)) {
            $optionsParsed['debug'] = true;
        }
        if (array_key_exists('debug', $options)) {
            $optionsParsed['debug'] = true;
        }

        if ((count($optionsParsed) < 4) && !array_key_exists('config',
                $optionsParsed)) {
            echo("Usage: ".basename($_SERVER['SCRIPT_NAME'])." -s  https://SERVER[:PORT] -u USERNAME -p PASSWORD -c COMPANY [-d debug]\n");
        }

        return $optionsParsed;
    }
    