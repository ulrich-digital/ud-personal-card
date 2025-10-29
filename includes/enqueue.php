<?php
/**
 * Enqueue Editor & Frontend Assets für UD Personal Card Block
 */

defined('ABSPATH') || exit;

function ud_personal_card_block_enqueue() {
    // Editor Assets
    wp_enqueue_script(
        'ud-personal-card-editor-script',
        plugins_url('../build/editor-script.js', __FILE__),
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'],
        null,
        true
    );

    wp_enqueue_style(
        'ud-personal-card-editor-style',
        plugins_url('../build/editor-style.css', __FILE__),
        [],
        null
    );

// Platzhalter-URL als JS-Variable bereitstellen
    wp_add_inline_script(
        'ud-personal-card-editor-script',
        'window.udPersonalCardPlaceholder = "' . plugins_url('../assets/img/platzhalterbild.svg', __FILE__) . '";',
        'before'
    );
}
add_action('enqueue_block_editor_assets', 'ud_personal_card_block_enqueue');

function ud_personal_card_block_enqueue_frontend() {
    wp_enqueue_script(
        'ud-personal-card-frontend-script',
        plugins_url('../build/frontend-script.js', __FILE__),
        [],
        null,
        true
    );

    wp_enqueue_style(
        'ud-personal-card-frontend-style',
        plugins_url('../build/frontend-style.css', __FILE__),
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'ud_personal_card_block_enqueue_frontend');
