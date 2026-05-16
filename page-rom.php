<?php
/**
 * Template Name: ROM Archive
 * Description: All ROMs — list view default, card view toggle
 */

get_header();

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$rom_q = new WP_Query( array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 24,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
?>

<main id="main" role="main" class="ra-wrap">
<div class="ra-inner">

    <!-- ── Page header ── -->
    <div class="ra-header">
        <div class="ra-header-text">
            <h1 class="ra-title">Nintendo Switch ROMs</h1>
            <p class="ra-subtitle"><?php echo number_format( $rom_q->found_posts ); ?> Games Available</p>
        </div>
        <div class="ra-toggle-wrap" role="group" aria-label="Switch layout">
            <button class="ra-btn active" id="ra-btn-list" title="List view" aria-pressed="true">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="8" y1="6"  x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <circle cx="3" cy="6"  r="1.5" fill="currentColor" stroke="none"/>
                    <circle cx="3" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                    <circle cx="3" cy="18" r="1.5" fill="currentColor" stroke="none"/>
                </svg>
                List
            </button>
            <button class="ra-btn" id="ra-btn-card" title="Card view" aria-pressed="false">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="2"  y="2"  width="9" height="9" rx="2"/>
                    <rect x="13" y="2"  width="9" height="9" rx="2"/>
                    <rect x="2"  y="13" width="9" height="9" rx="2"/>
                    <rect x="13" y="13" width="9" height="9" rx="2"/>
                </svg>
                Cards
            </button>
        </div>
    </div>

    <?php if ( $rom_q->have_posts() ) : ?>

    <!-- ── ROM container ── -->
    <div class="ra-container" id="ra-container">

        <?php while ( $rom_q->have_posts() ) : $rom_q->the_post();
            $post_id    = get_the_ID();
            $version    = get_post_meta( $post_id, 'version',   true ); // ← confirm key
            $file_size  = get_post_meta( $post_id, 'file_size', true ); // ← confirm key
            $thumb_sm   = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
            $thumb_md   = get_the_post_thumbnail_url( $post_id, 'medium' );
            $fallback   = get_template_directory_uri() . '/img/og-default.jpg';
            $cats       = get_the_category();
            $genre      = $cats ? esc_html( $cats[0]->name ) : '';
            $mod_time   = get_post_modified_time('U');
            $is_updated = ( $mod_time > strtotime('-60 days') );
        ?>

        <article class="ra-item" id="post-<?php the_ID(); ?>">
            <a href="<?php the_permalink(); ?>" class="ra-item-link">

                <!-- UPDATE badge -->
                <?php if ( $is_updated ) : ?>
                <span class="ra-badge-update">
                    <span class="ra-badge-dot"></span>UPDATE
                </span>
                <?php endif; ?>

                <!-- ── Thumbnail ── -->
                <div class="ra-thumb-wrap">
                    <img
                        class="ra-thumb-card"
                        src="<?php echo esc_url( $thumb_md ?: $fallback ); ?>"
                        alt="<?php echo esc_attr( get_the_title() ); ?>"
                        width="200" height="200"
                        loading="lazy"
                        decoding="async"
                    >
                    <img
                        class="ra-thumb-list"
                        src="<?php echo esc_url( $thumb_sm ?: $fallback ); ?>"
                        alt=""
                        width="56" height="56"
                        loading="lazy"
                        decoding="async"
                        aria-hidden="true"
                    >
                    <span class="ra-rom-pill">ROM</span>
                </div>

                <!-- ── Info ── -->
                <div class="ra-info">

                    <h2 class="ra-item-title"><?php the_title(); ?></h2>

                    <?php if ( $genre ) : ?>
                        <span class="ra-genre"><?php echo $genre; ?></span>
                    <?php endif; ?>

                    <?php if ( $file_size ) : ?>
                        <span class="ra-size-badge">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm0 1.5L18.5 9H14V3.5zM6 20V4h6v7h6v9H6z"/></svg>
                            <?php echo esc_html( $file_size ); ?>
                        </span>
                    <?php endif; ?>

                    <!-- Divider -->
                    <div class="ra-divider"></div>

                    <!-- Platform + Version row -->
                    <div class="ra-meta-row">

                        <div class="ra-platform">
                            <!-- Nintendo Switch logo SVG -->
                            <svg class="ra-switch-icon" width="28" height="20" viewBox="0 0 48 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="11" y="0" width="26" height="34" rx="4" fill="#e4000f"/>
                                <rect x="13" y="2" width="22" height="30" rx="3" fill="#1a1a1a"/>
                                <rect x="14" y="3" width="20" height="28" rx="2" fill="#222"/>
                                <!-- Left Joy-Con -->
                                <rect x="0" y="6" width="12" height="22" rx="6" fill="#e4000f"/>
                                <circle cx="6" cy="12" r="3" fill="#fff" opacity=".9"/>
                                <circle cx="6" cy="24" r="2" fill="#fff" opacity=".5"/>
                                <!-- Right Joy-Con -->
                                <rect x="36" y="6" width="12" height="22" rx="6" fill="#e4000f"/>
                                <circle cx="42" cy="14" r="2" fill="#fff" opacity=".5"/>
                                <circle cx="42" cy="22" r="3" fill="#fff" opacity=".9"/>
                            </svg>
                            <span class="ra-platform-label">Nintendo Switch</span>
                        </div>

                        <?php if ( $version ) : ?>
                        <span class="ra-meta-sep">|</span>
                        <div class="ra-version">
                            <svg class="ra-ver-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e4000f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 4 23 10 17 10"/>
                                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                            </svg>
                            <span class="ra-version-num"><?php echo esc_html( $version ); ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- List-view arrow -->
                        <svg class="ra-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>

                    </div>
                </div>

            </a>
        </article>

        <?php endwhile; wp_reset_postdata(); ?>

    </div><!-- /ra-container -->

    <!-- ── Pagination ── -->
    <div class="ra-pagination">
        <?php echo paginate_links( array(
            'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, $paged ),
            'total'     => $rom_q->max_num_pages,
            'prev_text' => '&#8249; Prev',
            'next_text' => 'Next &#8250;',
        ) ); ?>
    </div>

    <?php else : ?>
    <p class="ra-empty">No ROMs found.</p>
    <?php endif; ?>

</div><!-- /ra-inner -->
</main>

<!-- ══════════════════════════════════════════
     CSS
══════════════════════════════════════════ -->
<style>
/* ── Page shell ── */
.ra-wrap { padding-bottom: 3rem; }
.ra-inner {
    max-width: 1160px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* ── Page header ── */
.ra-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1.8rem 0 1.2rem;
    border-bottom: 1px solid rgba(0,0,0,.08);
    margin-bottom: 1.4rem;
}
.ra-title {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0 0 .2rem;
}
.ra-subtitle { font-size: .8rem; opacity: .5; margin: 0; }

/* ── Toggle buttons ── */
.ra-toggle-wrap { display: flex; gap: .4rem; }
.ra-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .45rem 1rem;
    border: 2px solid #ddd;
    border-radius: 8px;
    background: #fff;
    color: #888;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}
.ra-btn:hover { border-color: #e4000f; color: #e4000f; }
.ra-btn.active { background: #e4000f; border-color: #e4000f; color: #fff; }

/* ══════════════════════════
   LIST VIEW  (default)
══════════════════════════ */
#ra-container {
    display: flex;
    flex-direction: column;
    gap: .45rem;
}

/* List item card */
#ra-container .ra-item .ra-item-link {
    display: flex;
    align-items: center;
    gap: .9rem;
    padding: .55rem .85rem .55rem .7rem;
    background: #fff;
    border: 1px solid #ebebeb;
    border-radius: 10px;
    text-decoration: none;
    color: inherit;
    position: relative;
    transition: box-shadow .18s, border-color .18s;
}
#ra-container .ra-item .ra-item-link:hover {
    box-shadow: 0 4px 18px rgba(0,0,0,.1);
    border-color: #e4000f33;
}

/* UPDATE badge — list view */
#ra-container .ra-item .ra-badge-update {
    position: absolute;
    top: .55rem;
    left: .7rem;
    display: none; /* hidden in list, shown in card */
}

/* Thumb — small icon in list */
#ra-container .ra-item .ra-thumb-wrap {
    flex-shrink: 0;
    width: 56px;
    height: 56px;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    background: #f3f3f3;
}
#ra-container .ra-item .ra-thumb-card { display: none; }
#ra-container .ra-item .ra-thumb-list {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
#ra-container .ra-item .ra-rom-pill {
    display: none; /* hidden in list view */
}

/* Info */
#ra-container .ra-item .ra-info {
    flex: 1;
    min-width: 0;
    display: flex;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
}
#ra-container .ra-item .ra-item-title {
    font-size: .9rem;
    font-weight: 700;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 340px;
}
#ra-container .ra-item .ra-genre {
    font-size: .75rem;
    color: #e4000f;
    font-weight: 500;
}
#ra-container .ra-item .ra-size-badge {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .72rem;
    padding: .18rem .5rem;
    background: #f0f4ff;
    color: #3a5bd9;
    border-radius: 5px;
    font-weight: 600;
}
#ra-container .ra-item .ra-divider { display: none; }
#ra-container .ra-item .ra-meta-row {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-left: auto;
    flex-shrink: 0;
}
#ra-container .ra-item .ra-platform {
    display: flex;
    align-items: center;
    gap: .35rem;
}
.ra-switch-icon { width: 22px; height: auto; }
.ra-platform-label {
    font-size: .72rem;
    color: #e4000f;
    font-weight: 700;
}
.ra-meta-sep { color: #ccc; font-size: .9rem; }
.ra-version {
    display: flex;
    align-items: center;
    gap: .25rem;
}
.ra-version-num {
    font-size: .72rem;
    color: #e4000f;
    font-weight: 700;
}
.ra-arrow {
    opacity: .3;
    transition: opacity .15s, transform .15s;
}
#ra-container .ra-item .ra-item-link:hover .ra-arrow {
    opacity: .7;
    transform: translateX(3px);
}

/* ══════════════════════════
   CARD VIEW
══════════════════════════ */
#ra-container.ra-card-view {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
    gap: 1.2rem;
    flex-direction: unset;
}

/* Card item */
#ra-container.ra-card-view .ra-item .ra-item-link {
    flex-direction: column;
    align-items: stretch;
    padding: .9rem .85rem .8rem;
    border-radius: 14px;
    gap: 0;
    background: #fff;
    border: 1px solid #ebebeb;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    position: relative;
    min-height: 320px;
}
#ra-container.ra-card-view .ra-item .ra-item-link:hover {
    box-shadow: 0 8px 28px rgba(0,0,0,.13);
    transform: translateY(-3px);
    transition: all .2s ease;
}

/* UPDATE badge — card view */
#ra-container.ra-card-view .ra-item .ra-badge-update {
    display: flex;
    align-items: center;
    gap: .3rem;
    font-size: .68rem;
    font-weight: 700;
    color: #1a9bdc;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: .6rem;
}
.ra-badge-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #1a9bdc;
    flex-shrink: 0;
    box-shadow: 0 0 0 3px rgba(26,155,220,.2);
}

/* Thumb — card view: large centered image */
#ra-container.ra-card-view .ra-item .ra-thumb-wrap {
    width: 100%;
    height: 160px;
    border-radius: 10px;
    margin: 0 auto .85rem;
    position: relative;
    background: #f5f5f5;
    overflow: hidden;
}
#ra-container.ra-card-view .ra-item .ra-thumb-list { display: none; }
#ra-container.ra-card-view .ra-item .ra-thumb-card {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* ROM pill badge on image */
#ra-container.ra-card-view .ra-item .ra-rom-pill {
    display: block;
    position: absolute;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(20,20,20,.82);
    color: #fff;
    font-size: .62rem;
    font-weight: 700;
    letter-spacing: .08em;
    padding: .18rem .55rem;
    border-radius: 5px;
    backdrop-filter: blur(4px);
}

/* Card info layout */
#ra-container.ra-card-view .ra-item .ra-info {
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: .3rem;
}
#ra-container.ra-card-view .ra-item .ra-item-title {
    font-size: .85rem;
    font-weight: 700;
    white-space: normal;
    max-width: 100%;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: .1rem;
}
#ra-container.ra-card-view .ra-item .ra-genre {
    font-size: .73rem;
    font-style: italic;
}
#ra-container.ra-card-view .ra-item .ra-size-badge { display: none; }

/* Divider */
#ra-container.ra-card-view .ra-item .ra-divider {
    display: block;
    width: 100%;
    height: 1px;
    background: #f0f0f0;
    margin: .6rem 0;
}

/* Card bottom row */
#ra-container.ra-card-view .ra-item .ra-meta-row {
    justify-content: center;
    margin-left: 0;
    gap: .5rem;
}
#ra-container.ra-card-view .ra-item .ra-arrow { display: none; }

/* ── Pagination ── */
.ra-pagination { text-align: center; margin-top: 2.5rem; }
.ra-pagination .page-numbers {
    display: inline-block;
    padding: .5rem 1rem;
    margin: .15rem;
    border-radius: 7px;
    background: #fff;
    border: 1px solid #ddd;
    color: #333;
    text-decoration: none;
    font-size: .85rem;
    font-weight: 600;
    transition: all .15s;
}
.ra-pagination .page-numbers.current,
.ra-pagination .page-numbers:hover {
    background: #e4000f;
    border-color: #e4000f;
    color: #fff;
}
.ra-empty { text-align: center; padding: 4rem 0; opacity: .5; }

/* ── Responsive ── */
@media (max-width: 700px) {
    #ra-container.ra-card-view {
        grid-template-columns: repeat(2, 1fr);
        gap: .75rem;
    }
    #ra-container.ra-card-view .ra-item .ra-thumb-wrap { height: 120px; }
    .ra-header { flex-direction: column; align-items: flex-start; }
    #ra-container .ra-item .ra-item-title { max-width: 180px; }
}
@media (max-width: 420px) {
    #ra-container.ra-card-view { grid-template-columns: repeat(2, 1fr); }
}
</style>

<!-- ══════════════════════════════════════════
     JS — layout toggle + localStorage
══════════════════════════════════════════ -->
<script>
(function(){
    var container = document.getElementById('ra-container');
    var btnList   = document.getElementById('ra-btn-list');
    var btnCard   = document.getElementById('ra-btn-card');

    // Restore saved preference
    if ( localStorage.getItem('ra_layout') === 'card' ) {
        activate('card');
    }

    btnList.addEventListener('click', function(){ activate('list'); localStorage.setItem('ra_layout','list'); });
    btnCard.addEventListener('click', function(){ activate('card'); localStorage.setItem('ra_layout','card'); });

    function activate(mode){
        if (mode === 'card'){
            container.classList.add('ra-card-view');
            btnCard.classList.add('active');
            btnList.classList.remove('active');
            btnCard.setAttribute('aria-pressed','true');
            btnList.setAttribute('aria-pressed','false');
        } else {
            container.classList.remove('ra-card-view');
            btnList.classList.add('active');
            btnCard.classList.remove('active');
            btnList.setAttribute('aria-pressed','true');
            btnCard.setAttribute('aria-pressed','false');
        }
    }
})();
</script>

<?php get_footer(); ?>
