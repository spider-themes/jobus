<?php
// Get employer user ID (author of current job post)
$employer_id   = get_post_field( 'post_author', get_the_ID() );
$company_args  = array(
        'post_type'      => 'jobus_company',
        'author'         => $employer_id,
        'posts_per_page' => 1,
);
$company_query = new WP_Query( $company_args );
$company_name  = '';
$company_url   = '';
if ( $company_query->have_posts() ) {
    $company_name = $company_query->posts[0]->post_title;
    $company_url  = get_permalink( $company_query->posts[0]->ID );
}

// Get display options
$show_job_title = jobus_opt( 'is_job_title', true );
$show_job_meta  = jobus_opt( 'is_job_meta', true );
$show_job_share = jobus_opt( 'is_job_share_media', true );
$show_job_edit  = jobus_opt( 'is_job_edit_button', true );
$show_job_edit  = $show_job_edit && is_user_logged_in(); // Only show edit button if user is logged in

if ( $show_job_title || $show_job_meta || $show_job_share || $show_job_edit ) {
    ?>
    <div class="job-head">
        <div class="jbs-d-flex jbs-justify-content-between jbs-align-items-center jbs-flex-wrap jbs-gap-2">
            <div>
                <?php if ( $show_job_meta ) : ?>
                    <div class="post-date">
                        <?php echo get_the_date( 'd M, Y' ) . ', '; ?>
                        <?php esc_html_e( 'by', 'jobus' ) ?>
                        <a href="<?php echo esc_url( $company_url ) ?>" class="jbs-fw-500 jbs-text-dark">
                            <?php echo esc_html( $company_name ); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if ( $show_job_title ) : ?>
                    <?php the_title( '<h1 class="post-title">', '</h1>' ) ?>
                <?php endif; ?>
            </div>
            <?php
            if ($show_job_edit) {
                if (is_user_logged_in()) {
                    $current_user_id = get_current_user_id();
                    $is_admin        = current_user_can('administrator');
                    $is_author       = $current_user_id === (int) $employer_id;

                    if ($is_admin || $is_author) {
                        if ($is_admin) {
                            $edit_job_url = get_edit_post_link(get_the_ID());
                        } else {
                            $dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url('jobus_employer');
                            $edit_job_url  = $dashboard_url ? trailingslashit($dashboard_url) . 'submit-job?job_id=' . get_the_ID() : '#';
                        }
                        ?>
                        <a href="<?php echo esc_url($edit_job_url); ?>" class="jbs-btn-ten jbs-fw-500 jbs-text-white tran3s">
                            <i class="bi bi-pencil-square"></i>
                            <?php esc_html_e('Edit Job', 'jobus'); ?>
                        </a>
                        <?php
                    }
                }
            }
            ?>
        </div>
        <?php if ( $show_job_share ) : ?>
            <ul class="share-buttons jbs-d-flex jbs-flex-wrap jbs-style-none">
                <li>
                    <a class="share-item" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>" target="_blank">
                        <i class="bi bi-facebook"></i>
                        <span><?php esc_html_e( 'Facebook', 'jobus' ); ?></span>
                    </a>
                </li>
                <li>
                    <a class="share-item"
                       href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>"
                       target="_blank">
                        <i class="bi bi-x"></i>
                        <span><?php esc_html_e( 'X', 'jobus' ); ?></span>
                    </a>
                </li>
                <li>
                    <button type="button" class="share-item share-copy-btn" data-copy-url="<?php echo esc_url( get_permalink() ); ?>">
                        <i class="bi bi-link-45deg"></i>
                        <span class="copy-text"> <?php esc_html_e( 'Copy', 'jobus' ) ?> </span>
                    </button>
                </li>
            </ul>
        <?php endif; ?>
    </div>
    <?php
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const copyButtons = document.querySelectorAll('.share-copy-btn');

        copyButtons.forEach(button => {
            button.addEventListener('click', async function (e) {
                e.preventDefault();
                const url = this.getAttribute('data-copy-url');
                const textSpan = this.querySelector('.copy-text');
                const originalText = textSpan.textContent;

                try {
                    // Try using the modern Clipboard API
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(url);
                    } else {
                        // Fallback for older browsers or non-secure contexts
                        const textArea = document.createElement('textarea');
                        textArea.value = url;
                        textArea.style.position = 'fixed';
                        textArea.style.left = '-999999px';
                        document.body.appendChild(textArea);
                        textArea.focus();
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                    }

                    // Show success feedback
                    textSpan.textContent = 'Copied!';
                    this.classList.add('copied');

                    // Reset after 2 seconds
                    setTimeout(() => {
                        textSpan.textContent = originalText;
                        this.classList.remove('copied');
                    }, 2000);
                } catch (err) {
                    console.error('Failed to copy:', err);
                    textSpan.textContent = 'Failed';
                    setTimeout(() => {
                        textSpan.textContent = originalText;
                    }, 2000);
                }
            });
        });
    });
</script>