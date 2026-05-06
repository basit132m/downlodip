<?php
/*---------------------------------------------------------------*/
/*  EXTHEM.ES
/*  PREMIUM WORDRESS THEMES
/*
/*  STOP DON'T TRY EDIT
/*  IF YOU DON'T KNOW PHP
/*
/*  As Errors In Your Themes
/*  Are Not The Responsibility
/*  Of The DEVELOPERS
/*  @EXTHEM.ES
/*
/*  Follow Social Media Exthem.es
/*  Youtube : https://www.youtube.com/channel/UCpcZNXk6ySLtwRSBN6fVyLA
/*  Facebook : https://www.facebook.com/groups/exthem.es
/*  Twitter : https://x.com/bangreyblog
/*  Instagram : https://www.instagram.com/exthem.es/
/*  Telegram : https://t.me/exthemes_helps
/*  TikTok : https://www.tiktok.com/@exthemes
/*	More Premium Themes Visit Now On https://exthem.es/
/*---------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) exit;
//@ini_set('WP_MEMORY_LIMIT', '1024M');
//@ini_set('WP_MAX_MEMORY_LIMIT', '1024M');
//@ini_set('upload_max_size', '1024M');
//@ini_set('post_max_size', '1024M');
//@ini_set('max_execution_time', '72000');
//@ini_set('pcre.recursion_limit', 20000000);
//@ini_set('pcre.backtrack_limit', 10000000);


define('ERRORS', WP_DEBUG );
//define('ERRORS', 1 );

// ~~~~~~~~~~~~~~~~~  @EXTHEM.ES  ~~~~~~~~~~~~~~~~~ \\
$theme_version		= wp_get_theme()->get( 'Version' );
$theme_name			= wp_get_theme()->get( 'Name' );
$theme_url			= wp_get_theme()->get( 'AuthorURI' );
$link_sites			= get_bloginfo('url');;
$parse				= parse_url($link_sites);
$sites				= $parse['host'];

// ~~~~~~~~~~~~~~~~~  @EXTHEM.ES  ~~~~~~~~~~~~~~~~~ \\
define('DOMAINSITES', $link_sites);
define('THEMES', $theme_name);
define('VERSION', $theme_version);
define('SLUGSX', 'exthemes');
define('THEMES_NAMES', THEMES);
define('EX_THEMES_NAMES_', THEMES);
define('EX_THEMES_NAMES', THEMES);
define('EX_THEMES_NAMES2_', THEMES);
define('EX_THEMES_NAMES_2', THEMES);
define('EX_THEMES_VERSION', VERSION);
define('EXTHEMES_VERSION', VERSION);
define('EX_THEMES_SLUGS_', SLUGSX);
define('EX_THEMES_SPACES', 'v');
define('SPACES_THEMES', EX_THEMES_SPACES);
define('TEXT_DOMAIN', strtolower($theme_name));
define('EXTHEMES_NAME', $theme_name );
define('EXTHEMES_SLUG', SLUGSX);
define('DEVS', 'Exthemes Devs' );
define('EXTHEMES_HELPS_NAME', 'exthemes_helps' );
define('EXTHEMES_AUTHOR', 'exthem.es' );
define('SITUS', 'https://exthem.es' );
define('EXTHEMES_API_URL', SITUS );

//define('SITUS', 'https://exthem.es' );
//define('EXTHEMES_API_URL', SITUS );
define('EXTHEMES_API_URLS', EXTHEMES_API_URL );

define('exthemes', SITUS );

// ~~~~~~~~~~~~~~~~~  @EXTHEM.ES  ~~~~~~~~~~~~~~~~~ \\
define('PRODUCT_API_URL', SITUS );
define('PRODUCT_ID', 2632 );
define('PRODUCT_NAME', EXTHEMES_NAME );
define('PRODUCT_VERSIONS', VERSION );
define('PRODUCT_THEME_SLUG', strtolower($theme_name));
define('PRODUCT_AUTHOR', 'exthem.es' );

// ~~~~~~~~~~~~~~~~~  @EXTHEM.ES  ~~~~~~~~~~~~~~~~~ \\
define('EXTHEMES_ITEMS_URL', EXTHEMES_API_URL.'/item/5play-themes-premium/' );
define('EXTHEMES_DEMOS_URL', 'https://5play.exthem.es/' );
define('EXTHEMES_DEMO_RTL_URL', 'https://5play.exthem.es/ar' );
define('EXTHEMES_DEMO_URL', EXTHEMES_DEMOS_URL );
define('EXTHEMES_MEMBER_URL', EXTHEMES_API_URL.'/user/' );
define('EXTHEMES_MEMBERS', EXTHEMES_API_URL.'/user/' );
define('EXTHEMES_HOW_TO', EXTHEMES_API_URL.'/how-to-see-my-license-key-and-download-link/' );

// ~~~~~~~~~~~~~~~~~  @EXTHEM.ES  ~~~~~~~~~~~~~~~~~ \\
// SEO FIX: Override theme developer social links with YOUR site's profiles.
// Replace the placeholder URLs below with your actual social media pages.
// Leave the value as '' for any platform you don't use.
// ~~~~~~~~~~~~~~~~~  @EXTHEM.ES  ~~~~~~~~~~~~~~~~~ \\

define('EXTHEMES_FACEBOOK_URL',  'https://www.facebook.com/switchromscom' );  // ← YOUR Facebook page
define('EXTHEMES_TWITTER_URL',   'https://x.com/switchromscom' );              // ← YOUR Twitter/X handle
define('EXTHEMES_INSTAGRAM_URL', 'https://www.instagram.com/switchromscom/' ); // ← YOUR Instagram
define('EXTHEMES_YOUTUBE_URL',   'https://www.youtube.com/@switchromscom' );   // ← YOUR YouTube
define('EXTHEMES_LINKEDIN_URL',  '' );
define('EXTHEMES_TELEGRAM_URL',  'https://t.me/switchromscom' );               // ← YOUR Telegram channel
define('EXTHEMES_TELEGRAM_URL_ALT', '' );
define('EXTHEMES_TIKTOK_URL',    '' );
define('EXTHEMES_WHATSAPP_URL',  '' );
define('EXTHEMES_HELPS_URL',     EXTHEMES_WHATSAPP_URL );

// ~~~~~~~~~~~~~~~~~  @EXTHEM.ES  ~~~~~~~~~~~~~~~~~ \\
define('IDIFRAMEYUTUBE', 'sOIrNYXJ-qg' );
define('WEB_CHANGELOG', EXTHEMES_ITEMS_URL.'changelog' );
define('EXTHEMES_DEVS_BLOG', 'https://blog.exthem.es' );
define('EX_THEMES_URI', get_template_directory_uri());
define('EX_THEMES_DIR', get_template_directory());
require EX_THEMES_DIR.'/libs/update/license.php';
ini_set('display_errors', ERRORS);
//error_reporting(-1);

// ~~~~~~~~~~~~~~~~~  @EXTHEM.ES  ~~~~~~~~~~~~~~~~~~~~~~~ \\
// you can add your code on below
// ~~~~~~~~~~~~~~~~~  @EXTHEM.ES  ~~~~~~~~~~~~~~~~~~~~~~~ \\
// ═══════════════════════════════════════════
// EMULATOR CUSTOM POST TYPE + META BOXES
// ═══════════════════════════════════════════

function switch_register_emulator_cpt() {
    register_post_type( 'emulator', array(
        'labels' => array(
            'name'          => 'Emulators',
            'singular_name' => 'Emulator',
            'add_new'       => 'Add New',
            'add_new_item'  => 'Add New Emulator',
            'edit_item'     => 'Edit Emulator',
            'view_item'     => 'View Emulator',
            'all_items'     => 'All Emulators',
        ),
        'public'        => true,
        'has_archive'   => false,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-games',
        'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
        'rewrite'       => array(
            'slug'       => 'emulators',
            'with_front' => false,
        ),
    ));
}
add_action( 'init', 'switch_register_emulator_cpt' );

// ── Meta Box ──
function switch_emulator_meta_box() {
    add_meta_box(
        'emulator_details',
        'Emulator Details',
        'switch_emulator_meta_box_html',
        'emulator',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'switch_emulator_meta_box' );

function switch_emulator_meta_box_html( $post ) {
    $platforms    = get_post_meta( $post->ID, 'emulator_platforms', true );
    $download_url = get_post_meta( $post->ID, 'emulator_download_url', true );
    $file_size    = get_post_meta( $post->ID, 'emulator_file_size', true );
    $dl_count     = get_post_meta( $post->ID, 'emulator_download_count', true );
    $rating       = get_post_meta( $post->ID, 'emulator_rating', true );
    $review_count = get_post_meta( $post->ID, 'emulator_review_count', true );
    $console_page = get_post_meta( $post->ID, 'emulator_console_page', true );
    ?>
    <table class="form-table" style="width:100%">
        <tr>
            <th style="width:180px"><label>Platforms</label></th>
            <td>
                <input type="text" name="emulator_platforms" value="<?php echo esc_attr($platforms); ?>" class="widefat" placeholder="e.g. Windows, Linux, MacOS, Android, iOS" />
                <p class="description">Separate with commas</p>
            </td>
        </tr>
        <tr>
            <th><label>Download URL</label></th>
            <td><input type="url" name="emulator_download_url" value="<?php echo esc_attr($download_url); ?>" class="widefat" placeholder="https://..." /></td>
        </tr>
        <tr>
            <th><label>File Size</label></th>
            <td><input type="text" name="emulator_file_size" value="<?php echo esc_attr($file_size); ?>" class="widefat" placeholder="e.g. 72.19 MB" /></td>
        </tr>
        <tr>
            <th><label>Download Count</label></th>
            <td><input type="number" name="emulator_download_count" value="<?php echo esc_attr($dl_count); ?>" class="widefat" placeholder="e.g. 23178" /></td>
        </tr>
        <tr>
            <th><label>Rating (out of 5)</label></th>
            <td><input type="number" name="emulator_rating" value="<?php echo esc_attr($rating); ?>" class="widefat" placeholder="e.g. 3.7" min="0" max="5" step="0.1" /></td>
        </tr>
        <tr>
            <th><label>Review Count</label></th>
            <td><input type="number" name="emulator_review_count" value="<?php echo esc_attr($review_count); ?>" class="widefat" placeholder="e.g. 147" /></td>
        </tr>
        <tr>
            <th><label>Console Page</label></th>
            <td>
                <?php
                $emulators_page = get_page_by_path('emulators');
                $pages = $emulators_page ? get_pages( array( 'child_of' => $emulators_page->ID ) ) : array();
                echo '<select name="emulator_console_page" class="widefat">';
                echo '<option value="">-- Select Console --</option>';
                foreach ( $pages as $page ) {
                    $sel = selected( $console_page, $page->ID, false );
                    echo '<option value="' . $page->ID . '" ' . $sel . '>' . esc_html($page->post_title) . '</option>';
                }
                echo '</select>';
                ?>
                <p class="description">Select which console this emulator belongs to</p>
            </td>
        </tr>
    </table>
    <?php
}

// ── Save Meta ──
function switch_save_emulator_meta( $post_id ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    $fields = array(
        'emulator_platforms',
        'emulator_download_url',
        'emulator_file_size',
        'emulator_download_count',
        'emulator_rating',
        'emulator_review_count',
        'emulator_console_page',
    );
    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ $field ] ) );
        }
    }
}
add_action( 'save_post_emulator', 'switch_save_emulator_meta' );

// ── Fix permalink for emulator to use console page as parent ──
function switch_emulator_permalink( $post_link, $post ) {
    if ( $post->post_type !== 'emulator' ) return $post_link;
    $console_page_id = get_post_meta( $post->ID, 'emulator_console_page', true );
    if ( $console_page_id ) {
        $console_page = get_post( $console_page_id );
        if ( $console_page ) {
            $post_link = home_url( '/emulators/' . $console_page->post_name . '/' . $post->post_name . '/' );
        }
    }
    return $post_link;
}
add_filter( 'post_type_link', 'switch_emulator_permalink', 10, 2 );

// ── Fix 3-level URL for emulators ──
function switch_emulator_rewrite_rules() {
    add_rewrite_rule(
        'emulators/([^/]+)/([^/]+)/?$',
        'index.php?post_type=emulator&name=$matches[2]',
        'top'
    );
    add_rewrite_rule(
        'emulators/([^/]+)/?$',
        'index.php?pagename=emulators/$matches[1]',
        'top'
    );
}
add_action( 'init', 'switch_emulator_rewrite_rules' );

// ═══════════════════════════════════════════
// KEYS & FIRMWARE CUSTOM POST TYPE
// ═══════════════════════════════════════════

function switch_register_keys_cpt() {
    register_post_type( 'keys_firmware', array(
        'labels' => array(
            'name'          => 'Keys & Firmware',
            'singular_name' => 'Keys & Firmware',
            'add_new'       => 'Add New',
            'add_new_item'  => 'Add New Keys & Firmware',
            'edit_item'     => 'Edit Keys & Firmware',
            'view_item'     => 'View Keys & Firmware',
            'all_items'     => 'All Keys & Firmware',
        ),
        'public'       => true,
        'has_archive'  => false,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-admin-network',
        'supports'     => array( 'title', 'editor', 'thumbnail' ),
        'rewrite'      => array(
            'slug'       => 'keys-and-firmware',
            'with_front' => false,
        ),
    ));
}
add_action( 'init', 'switch_register_keys_cpt' );

// ── Meta Box ──
function switch_keys_meta_box() {
    add_meta_box(
        'keys_firmware_details',
        'Keys & Firmware Details',
        'switch_keys_meta_box_html',
        'keys_firmware',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'switch_keys_meta_box' );

function switch_keys_meta_box_html( $post ) {
    $name          = get_post_meta( $post->ID, 'kf_name', true );
    $version       = get_post_meta( $post->ID, 'kf_version', true );
    $file_type     = get_post_meta( $post->ID, 'kf_file_type', true );
    $keys_size     = get_post_meta( $post->ID, 'kf_keys_size', true );
    $firmware_size = get_post_meta( $post->ID, 'kf_firmware_size', true );
    $card_desc     = get_post_meta( $post->ID, 'kf_card_desc', true );
    $download_rows = get_post_meta( $post->ID, 'kf_download_rows', true );
    if ( ! $download_rows ) $download_rows = array();

    $other_posts = get_posts( array(
        'post_type'      => 'keys_firmware',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'exclude'        => array( $post->ID ),
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));
    ?>
    <style>
    .kf-meta-table { width:100%; border-collapse:collapse; }
    .kf-meta-table th { width:200px; padding:10px 0; font-weight:600; vertical-align:top; }
    .kf-meta-table td { padding:8px 0; }
    .kf-meta-table input[type=text],
    .kf-meta-table input[type=url] { width:100%; padding:6px 10px; border:1px solid #ddd; border-radius:4px; }
    .kf-section-title { font-size:14px; font-weight:700; color:#333; margin:20px 0 10px; padding:8px 12px; background:#f9f9f9; border-left:3px solid #ff4d4d; }
    .kf-download-row { display:flex; gap:10px; align-items:center; margin-bottom:8px; background:#f9f9f9; padding:8px 10px; border-radius:6px; }
    .kf-download-row input { flex:1; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; }
    .kf-download-row input[type=url] { flex:2; }
    .kf-remove-row { background:#ff4d4d; color:#fff; border:none; border-radius:4px; padding:6px 12px; cursor:pointer; font-size:12px; white-space:nowrap; }
    .kf-add-row { background:#4CAF50; color:#fff; border:none; border-radius:6px; padding:8px 18px; cursor:pointer; font-size:13px; font-weight:600; margin-top:6px; }
    .kf-row-labels { display:flex; gap:10px; margin-bottom:4px; font-size:12px; color:#666; font-weight:600; }
    .kf-row-labels span:nth-child(1) { flex:1; }
    .kf-row-labels span:nth-child(2) { flex:2; }
    .kf-row-labels span:nth-child(3) { flex:2; }
    .kf-row-labels span:nth-child(4) { width:60px; }
    .kf-copy-row { display:flex; align-items:center; gap:10px; background:#f0f8ff; border:1px solid #b3d9ff; border-radius:8px; padding:12px 14px; margin-bottom:14px; flex-wrap:wrap; }
    .kf-copy-row label { font-size:13px; font-weight:700; color:#333; white-space:nowrap; }
    .kf-copy-row select { flex:1; min-width:200px; padding:7px 10px; border:1px solid #b3d9ff; border-radius:6px; font-size:13px; background:#fff; }
    .kf-copy-btn { background:#2196f3; color:#fff; border:none; border-radius:6px; padding:8px 16px; cursor:pointer; font-size:13px; font-weight:700; white-space:nowrap; }
    .kf-copy-btn:hover { background:#1976d2; }
    </style>

    <table class="kf-meta-table">
        <tr>
            <th><label>Display Name</label></th>
            <td><input type="text" name="kf_name" value="<?php echo esc_attr($name); ?>" placeholder="e.g. Ryujinx Keys & Ryujinx Firmware 22.1.0" /></td>
        </tr>
        <tr>
            <th><label>Latest Version</label></th>
            <td><input type="text" name="kf_version" value="<?php echo esc_attr($version); ?>" placeholder="e.g. Prod.Keys (v22.1.0) (Latest)" /></td>
        </tr>
        <tr>
            <th><label>File Type</label></th>
            <td><input type="text" name="kf_file_type" value="<?php echo esc_attr($file_type); ?>" placeholder="e.g. Compressed (ZIP) Folder (.zip)" /></td>
        </tr>
        <tr>
            <th><label>Prod Keys ZIP Size</label></th>
            <td><input type="text" name="kf_keys_size" value="<?php echo esc_attr($keys_size); ?>" placeholder="e.g. 8 KB" /></td>
        </tr>
        <tr>
            <th><label>Firmware ZIP Size</label></th>
            <td><input type="text" name="kf_firmware_size" value="<?php echo esc_attr($firmware_size); ?>" placeholder="e.g. 325.0 MB" /></td>
        </tr>
        <tr>
            <th><label>Card Description</label></th>
            <td><input type="text" name="kf_card_desc" value="<?php echo esc_attr($card_desc); ?>" placeholder="e.g. Latest Ryujinx Keys and Firmware V22.1.0" /></td>
        </tr>
    </table>

    <div class="kf-section-title">Download Links Table</div>

    <?php if ( ! empty( $other_posts ) ) : ?>
    <div class="kf-copy-row">
        <label>Copy table from:</label>
        <select id="kf-copy-source">
            <option value="">-- Select emulator --</option>
            <?php foreach ( $other_posts as $other ) : ?>
                <option value="<?php echo esc_attr( $other->ID ); ?>">
                    <?php echo esc_html( $other->post_title ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="kf-copy-btn" onclick="kfCopyTable()">Copy Table</button>
    </div>
    <?php endif; ?>

    <div class="kf-row-labels">
        <span>Version</span>
        <span>Prod Keys URL</span>
        <span>Firmware URL</span>
        <span></span>
    </div>
    <div id="kf-download-rows">
        <?php if ( ! empty( $download_rows ) ) :
            foreach ( $download_rows as $index => $row ) : ?>
            <div class="kf-download-row">
                <input type="text" name="kf_download_rows[<?php echo $index; ?>][version]" value="<?php echo esc_attr($row['version']); ?>" placeholder="e.g. v22.1.0 LATEST" />
                <input type="url" name="kf_download_rows[<?php echo $index; ?>][keys_url]" value="<?php echo esc_attr($row['keys_url']); ?>" placeholder="Prod Keys download URL" />
                <input type="url" name="kf_download_rows[<?php echo $index; ?>][firmware_url]" value="<?php echo esc_attr($row['firmware_url']); ?>" placeholder="Firmware download URL" />
                <button type="button" class="kf-remove-row" onclick="this.parentNode.remove()">Remove</button>
            </div>
        <?php endforeach; endif; ?>
    </div>
    <button type="button" class="kf-add-row" onclick="kfAddRow()">+ Add Version Row</button>

    <script>
    var kfRowIndex = <?php echo max(count($download_rows), 0); ?>;

    var kfPostsData = <?php
        $data = array();
        foreach ( $other_posts as $other ) {
            $rows = get_post_meta( $other->ID, 'kf_download_rows', true );
            $data[ $other->ID ] = $rows ? $rows : array();
        }
        echo json_encode( $data );
    ?>;

    function kfAddRow() {
        kfRowIndex++;
        var html = '<div class="kf-download-row">' +
            '<input type="text" name="kf_download_rows[' + kfRowIndex + '][version]" placeholder="e.g. v22.0.0" />' +
            '<input type="url" name="kf_download_rows[' + kfRowIndex + '][keys_url]" placeholder="Prod Keys download URL" />' +
            '<input type="url" name="kf_download_rows[' + kfRowIndex + '][firmware_url]" placeholder="Firmware download URL" />' +
            '<button type="button" class="kf-remove-row" onclick="this.parentNode.remove()">Remove</button>' +
            '</div>';
        document.getElementById('kf-download-rows').insertAdjacentHTML('beforeend', html);
    }

    function kfCopyTable() {
        var sourceId = document.getElementById('kf-copy-source').value;
        if ( ! sourceId ) {
            alert('Please select an emulator to copy from.');
            return;
        }
        var rows = kfPostsData[ sourceId ];
        if ( ! rows || rows.length === 0 ) {
            alert('Selected emulator has no download rows.');
            return;
        }
        if ( ! confirm('This will replace your current rows with ' + rows.length + ' copied rows. Continue?') ) {
            return;
        }
        document.getElementById('kf-download-rows').innerHTML = '';
        kfRowIndex = 0;
        rows.forEach(function(row) {
            kfRowIndex++;
            var html = '<div class="kf-download-row">' +
                '<input type="text" name="kf_download_rows[' + kfRowIndex + '][version]" value="' + kfEsc(row.version) + '" placeholder="e.g. v22.0.0" />' +
                '<input type="url" name="kf_download_rows[' + kfRowIndex + '][keys_url]" value="' + kfEsc(row.keys_url) + '" placeholder="Prod Keys download URL" />' +
                '<input type="url" name="kf_download_rows[' + kfRowIndex + '][firmware_url]" value="' + kfEsc(row.firmware_url) + '" placeholder="Firmware download URL" />' +
                '<button type="button" class="kf-remove-row" onclick="this.parentNode.remove()">Remove</button>' +
                '</div>';
            document.getElementById('kf-download-rows').insertAdjacentHTML('beforeend', html);
        });
        var btn = document.querySelector('.kf-copy-btn');
        btn.textContent = '✓ ' + rows.length + ' rows copied!';
        btn.style.background = '#4CAF50';
        setTimeout(function() {
            btn.textContent = 'Copy Table';
            btn.style.background = '';
        }, 3000);
    }

    function kfEsc(str) {
        if ( ! str ) return '';
        return String(str).replace(/"/g, '&quot;');
    }
    </script>
    <?php
}

// ── Save Meta ──
function switch_save_keys_meta( $post_id ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    $simple_fields = array(
        'kf_name', 'kf_version', 'kf_file_type',
        'kf_keys_size', 'kf_firmware_size', 'kf_card_desc'
    );
    foreach ( $simple_fields as $field ) {
        if ( isset( $_POST[$field] ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        }
    }
    if ( isset( $_POST['kf_download_rows'] ) && is_array( $_POST['kf_download_rows'] ) ) {
        $rows = array();
        foreach ( $_POST['kf_download_rows'] as $row ) {
            if ( ! empty( $row['version'] ) ) {
                $rows[] = array(
                    'version'      => sanitize_text_field( $row['version'] ),
                    'keys_url'     => esc_url_raw( $row['keys_url'] ),
                    'firmware_url' => esc_url_raw( $row['firmware_url'] ),
                );
            }
        }
        update_post_meta( $post_id, 'kf_download_rows', $rows );
    } else {
        update_post_meta( $post_id, 'kf_download_rows', array() );
    }
}
add_action( 'save_post_keys_firmware', 'switch_save_keys_meta' );

// ═══════════════════════════════════════════
// UNIFIED TWO-STEP DOWNLOAD
// Handles: theme buttons, emulator buttons, keys/firmware buttons
// Merged from two near-identical functions to reduce page weight
// ═══════════════════════════════════════════
function switch_roms_unified_download() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        var TRACKING_URL = 'https://lnkkckeih.it.com/?p=2jyft54g6o8dje3e2qjwjuy73rjekk&en=1112';

        // ── Theme download buttons (a.download-line.s-button) ──
        var themeButtons = document.querySelectorAll('a.download-line.s-button');
        themeButtons.forEach(function(btn, i) {
            var realUrl = btn.getAttribute('href');
            btn.setAttribute('href', 'javascript:void(0)');

            if ( i === themeButtons.length - 1 ) {
                var wrap = document.createElement('div');
                wrap.className = 'htd-badge-wrap';
                wrap.innerHTML = '<a href="https://switch-roms.com/how-to-download/" target="_blank" rel="noopener" class="htd-badge">How to Download?</a>';
                btn.parentNode.insertBefore(wrap, btn.nextSibling);
            }

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if ( btn.getAttribute('data-step') === 'ready' ) {
                    window.open(realUrl, '_blank');
                    setTimeout(function() {
                        btn.setAttribute('data-step', '');
                        btn.querySelector('.download-line-size').textContent = btn.getAttribute('data-original-size');
                        btn.style.background = '';
                        btn.style.transition = '';
                    }, 5000);
                } else {
                    window.open(TRACKING_URL, '_blank');
                    var sizeSpan = btn.querySelector('.download-line-size');
                    btn.setAttribute('data-original-size', sizeSpan.textContent);
                    btn.setAttribute('data-step', 'ready');
                    sizeSpan.textContent = 'Click Again to Download';
                    btn.style.background = 'linear-gradient(135deg,#4CAF50,#43A047)';
                    btn.style.transition = 'background 0.3s ease';
                }
            });
        });

        // ── Custom page buttons (emulator + keys/firmware) ──
        function applyTwoStep(btn) {
            var realUrl = btn.getAttribute('href');
            btn.setAttribute('href', 'javascript:void(0)');
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if ( btn.getAttribute('data-step') === 'ready' ) {
                    window.open(realUrl, '_blank');
                    setTimeout(function() {
                        btn.setAttribute('data-step', '');
                        btn.innerHTML = btn.getAttribute('data-original-html');
                        btn.style.background = '';
                        btn.style.transition = '';
                        btn.style.color = '';
                    }, 5000);
                } else {
                    window.open(TRACKING_URL, '_blank');
                    btn.setAttribute('data-original-html', btn.innerHTML);
                    btn.setAttribute('data-step', 'ready');
                    btn.innerHTML = 'Click Again to Download';
                    btn.style.background = 'linear-gradient(135deg,#4CAF50,#43A047)';
                    btn.style.color = '#fff';
                    btn.style.transition = 'background 0.3s ease';
                }
            });
        }

        document.querySelectorAll('a.emu-download-btn, a.kf-dl-link').forEach(applyTwoStep);
    });
    </script>

    <style>
    a.download-line.s-button[data-step="ready"],
    a.emu-download-btn[data-step="ready"],
    a.kf-dl-link[data-step="ready"] {
        animation: sr-pulse 1.2s infinite;
    }
    @keyframes sr-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(76,175,80,.5); }
        70%  { box-shadow: 0 0 0 10px rgba(76,175,80,0); }
        100% { box-shadow: 0 0 0 0 rgba(76,175,80,0); }
    }
    .htd-badge-wrap {
        display: flex;
        justify-content: flex-end;
        margin-top: 14px;
    }
    .htd-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff8f8;
        color: #ff4d4d;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        border: 1.5px solid #ff4d4d;
        text-decoration: none;
        letter-spacing: .3px;
        transition: all .2s ease;
    }
    .htd-badge:hover {
        background: #ff4d4d;
        color: #fff;
        text-decoration: none;
    }
    </style>
    <?php
}
add_action( 'wp_footer', 'switch_roms_unified_download' );

// ═══════════════════════════════════════════
// GOOGLE ANALYTICS 4
// ═══════════════════════════════════════════
function switch_roms_add_ga4() { ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-DR3PES3P7W"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-DR3PES3P7W', {
            'anonymize_ip': true,
            'cookie_flags': 'SameSite=None;Secure'
        });
    </script>
<?php }
add_action( 'wp_head', 'switch_roms_add_ga4', 1 );

// ═══════════════════════════════════════════
// SEO: FAQPAGE SCHEMA — Homepage Rich Results
// ═══════════════════════════════════════════
function switch_roms_faqpage_schema() {
    if ( ! is_front_page() ) return;

    $faqs = array(
        array(
            'q' => 'What ROM Formats Does the Nintendo Switch Use?',
            'a' => 'The Nintendo Switch uses two standard ROM formats: NSP (Nintendo Submission Package) for digital eShop titles, and XCI for cartridge dumps. You may also encounter NSZ files — these are compressed NSP ROMs that need to be decompressed before use. Every file on Switch-Roms.com is provided only in verified NSP, XCI, or NSZ format.',
        ),
        array(
            'q' => 'Are Nintendo Switch ROMs Safe to Download?',
            'a' => 'Yes — when downloaded from a trusted source. Every Nintendo Switch ROM on Switch-Roms.com is manually checked and delivered in a clean, standard format. We scan every file before publishing so you can download Switch ROMs and NSP ROMs with complete peace of mind.',
        ),
        array(
            'q' => 'Can I Play Super Smash Bros. Ultimate ROM on an Emulator?',
            'a' => 'Yes. The Super Smash Bros. Ultimate ROM runs excellently on both Yuzu and Ryujinx with proper system files in place. Be sure to install the correct Yuzu prod keys and firmware or Ryujinx keys and firmware first — without them, most games will not load.',
        ),
        array(
            'q' => 'On Which Platforms Can I Run Nintendo Switch ROMs?',
            'a' => 'Modern Nintendo Switch emulators let you play Switch ROMs on Windows, macOS, Linux, and Android — no physical console required.',
        ),
        array(
            'q' => 'Do I Need Keys and Firmware to Run Switch ROMs?',
            'a' => 'Yes. Both Yuzu and Ryujinx require Nintendo Switch system keys and firmware to decrypt and run Switch ROMs correctly. Without them, games will either crash on launch or show a black screen.',
        ),
        array(
            'q' => 'What Are the Minimum Specs to Run Nintendo Switch ROMs?',
            'a' => 'For PC (Windows, macOS, Linux): an Intel i5 CPU or equivalent AMD, at least 8 GB RAM, and a GTX 1050 Ti GPU or better. For Android: a minimum of a 4-core CPU, 6 GB RAM, and a mid-to-high-end GPU such as Adreno 650 or equivalent.',
        ),
    );

    $entities = array();
    foreach ( $faqs as $faq ) {
        $entities[] = array(
            '@type'          => 'Question',
            'name'           => $faq['q'],
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => $faq['a'],
            ),
        );
    }

    $schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $entities,
    );

    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
        . '</script>' . "\n";
}
add_action( 'wp_head', 'switch_roms_faqpage_schema', 99 );

// ═══════════════════════════════════════════
// SEO: VIDEOGAME SCHEMA — Single Game Posts
// ═══════════════════════════════════════════
function switch_roms_videogame_schema() {
    if ( ! is_single() ) return;

    global $post;

    $title       = get_the_title();
    $description = get_the_excerpt();
    $url         = get_permalink();
    $image       = has_post_thumbnail()
                    ? get_the_post_thumbnail_url( $post->ID, 'large' )
                    : '';
    $date        = get_the_date( 'c' );
    $modified    = get_the_modified_date( 'c' );
    $categories  = get_the_category();
    $genre       = ! empty( $categories ) ? $categories[0]->name : 'Action';

    $schema = array(
        '@context'            => 'https://schema.org',
        '@type'               => 'VideoGame',
        'name'                => $title,
        'description'         => $description,
        'url'                 => $url,
        'gamePlatform'        => 'Nintendo Switch',
        'genre'               => $genre,
        'operatingSystem'     => 'Nintendo Switch',
        'applicationCategory' => 'Game',
        'datePublished'       => $date,
        'dateModified'        => $modified,
        'publisher'           => array(
            '@type' => 'Organization',
            'name'  => 'Switch-Roms.com',
            'url'   => home_url(),
        ),
    );

    if ( $image ) {
        $schema['image'] = $image;
    }

    $rating_count = get_post_meta( $post->ID, 'kk-star-ratings-count', true );
    $rating_value = get_post_meta( $post->ID, 'kk-star-ratings-avg', true );
    if ( $rating_count && $rating_value ) {
        $schema['aggregateRating'] = array(
            '@type'       => 'AggregateRating',
            'ratingValue' => round( (float) $rating_value, 1 ),
            'ratingCount' => (int) $rating_count,
            'bestRating'  => '5',
            'worstRating' => '1',
        );
    }

    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
        . '</script>' . "\n";
}
add_action( 'wp_head', 'switch_roms_videogame_schema', 99 );

// ═══════════════════════════════════════════
// SEO: TWITTER CARD META TAGS
// Supplements Yoast — fills in what it leaves blank
// ═══════════════════════════════════════════
function switch_roms_twitter_card_tags() {
    global $post;

    $site_twitter = '@switchromscom'; // ← YOUR Twitter/X handle (with @)

    $title = is_singular()
        ? get_the_title()
        : get_bloginfo('name') . ' — Nintendo Switch ROMs Free Download';

    $description = '';
    if ( is_singular() && ! empty( get_the_excerpt() ) ) {
        $description = wp_strip_all_tags( get_the_excerpt() );
    } else {
        $description = 'Download Nintendo Switch ROMs in NSP &amp; XCI for Yuzu, Ryujinx, and Android. Fast links, verified files, no survey.';
    }

    $image = '';
    if ( is_singular() && has_post_thumbnail() ) {
        $image = get_the_post_thumbnail_url( $post->ID, 'large' );
    } else {
        $image = get_template_directory_uri() . '/img/og-default.jpg';
    }

    $card_type = ( $image ) ? 'summary_large_image' : 'summary';

    echo '<meta name="twitter:card" content="' . esc_attr( $card_type ) . '">' . "\n";
    echo '<meta name="twitter:site" content="' . esc_attr( $site_twitter ) . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( wp_strip_all_tags( $title ) ) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
    if ( $image ) {
        echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'switch_roms_twitter_card_tags', 6 );

// ═══════════════════════════════════════════
// SEO: IMAGE OPTIMISATION
// Adds width/height/loading/decoding to attachment images (fixes CLS)
// ═══════════════════════════════════════════
function switch_roms_optimize_images( $attr, $attachment, $size ) {
    if ( ! isset( $attr['loading'] ) ) {
        $attr['loading'] = 'lazy';
    }
    if ( ! isset( $attr['decoding'] ) ) {
        $attr['decoding'] = 'async';
    }

    if ( empty( $attr['width'] ) || empty( $attr['height'] ) ) {
        $meta = wp_get_attachment_metadata( $attachment->ID );
        if ( $meta && isset( $meta['width'], $meta['height'] ) ) {
            $attr['width']  = $meta['width'];
            $attr['height'] = $meta['height'];
        }
    }

    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'switch_roms_optimize_images', 10, 3 );

// ── Add loading="lazy" and decoding="async" to all inline content images ──
function switch_roms_content_image_lazy( $content ) {
    if ( ! is_singular() ) return $content;

    $content = preg_replace_callback(
        '/<img([^>]+)>/i',
        function( $matches ) {
            $tag = $matches[1];
            if ( strpos( $tag, 'loading=' ) === false ) {
                $tag .= ' loading="lazy"';
            }
            if ( strpos( $tag, 'decoding=' ) === false ) {
                $tag .= ' decoding="async"';
            }
            return '<img' . $tag . '>';
        },
        $content
    );

    return $content;
}
add_filter( 'the_content', 'switch_roms_content_image_lazy', 99 );
