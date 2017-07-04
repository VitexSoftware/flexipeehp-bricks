<?php
/**
 * FlexiPeeHP - Example how to configure Connection
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017 Vitex Software
 */

/**
 * Write logs as:
 */
define('EASE_APPNAME', 'FlexiPeeHP-Example');

/**
 * Write logs TO: one of memory,console,file,syslog or combinations like "console|syslog"
 */
define('EASE_LOGGER', 'console');

/*
 * URL Flexibee API
 */
define('FLEXIBEE_URL', 'https://demo.flexibee.eu');
//define('FLEXIBEE_URL', 'https://localhost:5434/');

/*
 * FlexiBee API User Login
 */
define('FLEXIBEE_LOGIN', 'winstrom');
//define('FLEXIBEE_LOGIN', 'admin');
/*
 * FlexiBee API User Password
 */
define('FLEXIBEE_PASSWORD', 'winstrom');
//define('FLEXIBEE_PASSWORD', 'admin123');
/*
 * FlexiBee API Company
 */
define('FLEXIBEE_COMPANY', 'demo');
//define('FLEXIBEE_COMPANY', 'spoje_net_s_r_o_');
