<?php

/**
 * Server-side Rendering für den Personal Card Block
 */

defined('ABSPATH') || exit;

function ud_render_personal_card_block($attributes) {
    $layout     = esc_attr($attributes['layout'] ?? 'small');
    $name       = trim($attributes['name'] ?? '');
    $role       = trim($attributes['role'] ?? '');
    //$email      = trim($attributes['email'] ?? '');
    //$raw_phone  = trim($attributes['phone'] ?? '');
    //$raw_mobile = trim($attributes['mobile'] ?? '');
    $location   = trim($attributes['location'] ?? '');
    $image_id   = $attributes['imageId'] ?? 0;
    $show_image = $attributes['showImage'] ?? true;

    // Tags vorbereiten
    $tags_json  = $attributes['tags'] ?? '[]';
    $tags_array = json_decode($tags_json, true) ?: [];

    $tags_slug_array = [];
    foreach ($tags_array as $tag) {
        $tag = str_replace('_', ' ', $tag);
        $tags_slug_array[] = sanitize_title($tag);
    }
    $tags_slug_json = wp_json_encode($tags_slug_array);


    //Email vorbereiten (HTML-Bereinigen)
    $email = wp_strip_all_tags(trim($attributes['email'] ?? ''));
    $raw_phone = wp_strip_all_tags(trim($attributes['phone'] ?? ''));
    $raw_mobile = wp_strip_all_tags(trim($attributes['mobile'] ?? ''));

    // Telefonnummern vorbereiten
    $phone_link  = normalize_swiss_phone($raw_phone);
    $mobile_link = normalize_swiss_phone($raw_mobile);

    $phone_label  = $phone_link ? format_ch_phone_display($phone_link) : '';
    $mobile_label = $mobile_link ? format_ch_phone_display($mobile_link) : '';

    // Farbwerte → CSS-Klassen mappen
    $text_color_slug = $attributes['textColor'] ?? '';
    $bg_color_slug   = $attributes['backgroundColor'] ?? '';

    $text_class = $text_color_slug ? 'has-' . sanitize_title($text_color_slug) . '-color' : '';
    $bg_class   = $bg_color_slug ? 'has-' . sanitize_title($bg_color_slug) . '-background-color' : '';

    // Klassen dynamisch zum Block hinzufügen
    $classes = [
        'ud-personal-card',
        'ud-layout-' . $layout,
        $text_class,
        $bg_class,
    ];

    $class_attr = implode(' ', array_filter(array_map('sanitize_html_class', $classes)));

    ob_start();

?>

    <div
        class="<?php echo esc_attr($class_attr); ?>"
        data-tags="<?php echo esc_attr($tags_json); ?>"
        data-tags-slug="<?php echo esc_attr($tags_slug_json); ?>">
        <?php if ($show_image && $image_id): ?>
            <div class="ud-personal-image-left">
                <?php echo wp_get_attachment_image($image_id, 'large', false, ['class' => 'ud-personal-image']); ?>
            </div>
        <?php endif; ?>

        <div class="ud-personal-content">
            <?php if ($name || $role): ?>
                <div class="ud-personal-meta">
                    <?php if ($name): ?>
                        <div class="ud-personal-name"><?php echo esc_html($name); ?></div>
                    <?php endif; ?>
                    <?php if ($role): ?>
                        <div class="ud-personal-role"><?php echo esc_html($role); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($email || $location || $mobile_link || $phone_link): ?>
                <div class="ud-personal-contact">
                    <?php if ($location): ?>
                        <div class="ud-personal-location"><?php echo esc_html($location); ?></div>
                    <?php endif; ?>

                    <?php if ($email): ?>
                        <div class="ud-personal-email">
                            <a href="mailto:<?php echo antispambot($email); ?>">
                                <?php echo antispambot($email); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($mobile_link): ?>
                        <div class="ud-personal-mobile">
                            <a href="tel:<?php echo esc_attr($mobile_link); ?>">
                                <?php echo esc_html($mobile_label); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($phone_link): ?>
                        <div class="ud-personal-phone">
                            <a href="tel:<?php echo esc_attr($phone_link); ?>">
                                <?php echo esc_html($phone_label); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div> <!-- .ud-personal-card -->
<?php
    return ob_get_clean();
}

/**
 * Normalisiert Schweizer Telefonnummern:
 * - akzeptiert lokale Formatierungen wie 0761234567
 * - gibt gültige +41xxxxxxxxx-Nummer zurück oder leeren String
 */
function normalize_swiss_phone($input) {
    $input = preg_replace('/[\s\p{Z}\xA0]+/u', '', $input); // Unicode-Zwischenräume

    // Lokales Format: 0XX xxx xx xx → +41
    if (preg_match('/^0([1-9][0-9])([0-9]{3})([0-9]{2})([0-9]{2})$/', $input, $m)) {
        return '+41' . $m[1] . $m[2] . $m[3] . $m[4];
    }

    // International: +41XXXXXXXXX
    if (preg_match('/^\\+41\\d{9}$/', $input)) {
        return $input;
    }

    return '';
}

/* Gibt Telefonnummer im Format 0XX XXX XX XX aus */
function format_ch_phone_display($phone) {
    if (preg_match('/^\\+41(\\d{2})(\\d{3})(\\d{2})(\\d{2})$/', $phone, $m)) {
        return '0' . $m[1] . ' ' . $m[2] . ' ' . $m[3] . ' ' . $m[4];
    }
    return $phone;
}
