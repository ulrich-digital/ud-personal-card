<?php
/**
 * Plugin Name:     UD Block: Mitarbeiter Card
 * Description:     Block zur Darstellung von Personenkarten mit Bild und Kontaktdaten.
 * Version:         1.0.0
 * Author:          ulrich.digital gmbh
 * Author URI:      https://ulrich.digital/
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     personal-card-block-ud
 */

// Sicherheitscheck
defined('ABSPATH') || exit;

// Includes laden
require_once plugin_dir_path(__FILE__) . 'includes/enqueue.php';
require_once plugin_dir_path(__FILE__) . 'includes/render.php';
require_once plugin_dir_path(__FILE__) . 'includes/block-register.php';
