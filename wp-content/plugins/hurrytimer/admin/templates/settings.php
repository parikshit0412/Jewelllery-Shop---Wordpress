<?php
namespace Hurrytimer;

use Hurrytimer\Utils\Helpers;

?>

<div class="wrap hurryt-plugin-settings">
    <h2>Settings</h2>
    <form method="post" action="options.php">
        <?php
        settings_fields("hurrytimer_settings");
        do_settings_sections("hurrytimer_settings");
        submit_button();
        ?>
    </form>
    <button type="button" class="button button-default hurrytResetAllEvergreenCampaigns  hurryt-block"
            data-cookie-prefix="<?php echo esc_attr(Cookie_Detection::COOKIE_PREFIX) ?>"
            data-url="<?php echo esc_url(Helpers::createResetAllEvergreenCampaignsUrl('admin')) ?>"><?php esc_html_e('Reset all evergreen campaigns for me...', 'hurrytimer') ?>
    </button>
    <br>
    <button type="button" class="button button-default hurrytResetAllEvergreenCampaigns hurryt-block"
            data-cookie-prefix="<?php echo esc_attr(Cookie_Detection::COOKIE_PREFIX) ?>"
            data-url="<?php echo esc_url(Helpers::createResetAllEvergreenCampaignsUrl('all')) ?>"><?php esc_html_e('Reset all evergreen campaigns for all visitors...', 'hurrytimer') ?>
    </button>
</div>