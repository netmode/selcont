<?php
/*
 *
 * Template Name: Single Selcont Lecture Type
 *
 *
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header();

$selcont_post = get_post();
$instructor_name = esc_attr( get_post_meta( get_the_ID(), 'instructor_name_meta_box', true ) );
$video_url = esc_url( get_post_meta( get_the_ID(), 'video_url_meta_box', true ) );
$presentation_file = esc_attr( get_post_meta( get_the_ID(), 'umb_file', true ) );
$institution_name = esc_attr( get_post_meta( get_the_ID(), 'school_name_meta_box', true ) );
$school_name = esc_attr( get_post_meta( get_the_ID(), 'school_name_meta_box', true ) );
$slides = get_post_meta( get_the_ID(), 'repeatable_fields', true );
$attachmentsArray = get_post_meta( get_the_ID(), 'attachmentsSlides', true );

?>


<?php if ( count($slides) <= 1  ) { ?>

    <div id="selcont-content post-<?php the_ID(); ?>" class="container post-<?php the_ID(); ?>">

            <div class="row">
                <div class="col-sm-12">
                    <h1 class="selcont-entry-title"><?php the_title(); ?></h1>

                    <div class="selcont-lecture-description">
                        <?php echo apply_filters('the_content', $selcont_post->post_content); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">

                    <?php if ($video_url) { ?>
                        <div class="selcont-video">
                            <div id="jwp">Loading the player...</div>
                            <script type="text/javascript">
                                jwplayer("jwp")
                                    .setup({
                                        file: "<?php echo $video_url; ?>",
                                        width: "100%",
                                        aspectratio: "16:9",
                                        controls: true,
                                        players: [
                                            {type: "html5"},
                                            {type: "flash", src: "../js/jwplayer.flash.swf"}
                                        ]
                                    });
                            </script>

                        </div>
                    <?php } ?>

                </div>

                <div class="col-sm-6">

                    <?php if ($attachmentsArray) { ?>

                        <div class="selcont-slides">
                            <?php
                            foreach ($attachmentsArray as $u => $uval) {
                                ?>
                                <img id="selcont-slide-<?php echo $u; ?>"
                                     src="<?php echo $attachmentsArray[$u]['slide-url']; ?>" class="img-responsive selcont-img" />
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="row selcont-info">
                <div class="col-sm-6">
                    <?php if ($attachmentsArray) { ?>
                    <div class="selcont-titles">
                        <?php foreach ($attachmentsArray as $t => $tval) { ?>
                            <div class="col-xs-3 selcont-titles-thumb">
                                <a id="selcont-tl" href="#plcr" onclick="jwplayer().seek(<?php echo $attachmentsArray[$t]['slide-time']; ?>);">
                                    <img src="<?php echo $attachmentsArray[$t]['slide-url']; ?>" id="selcont-thumb-<?php echo $t ?>" class="img-responsive titles-thumb" />
                                </a>
                            </div>
                        <?php } ?>

                        <script type="text/javascript">
                            <?php foreach ($attachmentsArray as $kk => $vix) { ?>
                            jwplayer().onTime(function () {
                                if (jwplayer().getPosition() >= <?php echo $attachmentsArray[$kk]['slide-time']; ?>) {
                                    <?php
                                        foreach ($attachmentsArray as $item => $val) {
                                            if ($item != $kk) {
                                    ?>
                                    document.getElementById("selcont-slide-<?php echo $item; ?>").style.display = "none";
                                    <?php
                                            }
                                        }
                                    ?>
                                    document.getElementById("selcont-slide-<?php echo $kk; ?>").style.display = "block";
                                }
                            });
                            <?php } ?>
                        </script>
                    </div>
                    <?php } ?>
                </div>
                <div class="col-sm-6">
                    <div class="col-xs-12 selcont-meta">
                        <?php if ($instructor_name) { ?>
                            <p><?php echo $instructor_name; ?></p>
                        <?php } ?>

                        <?php if ($school_name) { ?>
                            <p><?php echo $school_name; ?></p>
                        <?php } ?>

                        <?php if ($presentation_file) { ?>
                            <p><a target="_blank" href="<?php echo $presentation_file; ?>">Διαφάνειες</a></p>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php
} else {
?>

    <div class="content-area" id="primary">
        <div role="main" class="content" id="selcont-content">

            <h1 class="entry-title"><?php the_title(); ?></h1>

            <article class="post-<?php the_ID(); ?> post type-post status-publish category-uncategorized" id="post-<?php the_ID(); ?>">

                <div class="selcont-lecture-description">
                    <?php echo apply_filters( 'the_content', $selcont_post->post_content ); ?>
                </div>
                <br/>

                <?php if( $video_url ) { ?>
                    <div id="video-player" class="selcont-video">
                        <div id="jwp">Loading the player...</div>
                        <script type="text/javascript">
                            jwplayer("jwp")
                                .setup({
                                    file: "<?php echo $video_url; ?>",
                                    width: "100%",
                                    aspectratio: "16:9",
                                    controls: true,
                                    players: [
                                        { type: "html5" },
                                        { type: "flash", src: "../js/jwplayer.flash.swf" }
                                    ]
                                });
                        </script>

                        <br/><br/><br/><br/>

                        <?php if( $instructor_name ) { ?>
                            <p><strong>Instructor:</strong> <?php echo $instructor_name; ?></p>
                        <?php } ?>

                        <?php if( $school_name ) { ?>
                            <p><strong>School:</strong> <?php echo $school_name; ?></p>
                        <?php } ?>

                        <p><strong>Presentation file:</strong> <a target="_blank" href="<?php echo $presentation_file; ?>">Download</a></p>
                    </div>
                <?php } ?>

                <?php if( $slides ) { ?>

                    <div class="selcont-slides">
                        <?php   foreach ($slides as $v => $im) { ?>
                            <img id="selcont-slide-<?php echo $v; ?>" src="<?php echo $im['stt_meta_box_image']; ?>" class="selcont-img" />
                        <?php   } ?>
                    </div>

                    <div class="selcont-titles">
                            <?php foreach ($slides as $s) { ?>
                                <div class="selcont-titles-thumb">
                                    <a id="selcont-tl" href="#plcr" onclick="jwplayer().seek(<?php echo $s['stt_meta_box_time']; ?>);"><?php echo $s['stt_meta_box_title']; ?></a>
                                </div>
                            <?php } ?>
                    </div>
                <?php } ?>


                <script type="text/javascript">

                    <?php foreach ($slides as $kk => $vix) { ?>

                    jwplayer().onTime(function() {
                        if (jwplayer().getPosition() >= <?php echo $slides[$kk]['stt_meta_box_time']; ?>) {
                            <?php
                                foreach ($slides as $item => $val) {
                                    if ($item != $kk) {
                            ?>
                                    document.getElementById("selcont-slide-<?php echo $item; ?>").style.display="none";
                            <?php
                                    }
                                }
                            ?>
                            document.getElementById("selcont-slide-<?php echo $kk; ?>").style.display="block";
                        }
                    });

                    <?php } ?>

                </script>

            </article>
        </div>
    </div>

<?php
}


get_footer();
