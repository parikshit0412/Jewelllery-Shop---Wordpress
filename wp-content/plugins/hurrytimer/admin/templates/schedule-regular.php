<?php namespace Hurrytimer;

//removeIf(pro)
$hasMultipleRanges = false;
//endRemoveIf(pro)
?>

 <table class="hurrytimer-standard form-table hidden mode-settings" data-for="hurrytModeRegular">
        <?php ?>

        <!-- Single end date -->
        <tr class="form-field hurrytimer-enddate-field" id="hurryt-single-date-row" <?php echo $hasMultipleRanges ? 'style="display:none"' : ''; ?>>
            <td>
                <label><?php _e("End date/time", "hurrytimer") ?></label>
                <div style="margin-top:6px;">
                    <?php ?>
                    <?php //removeIf(pro) ?>
                    <a href="#" id="hurryt-toggle-date-ranges-teaser" class="hurryt-toggle-deadlines-link">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <span><?php _e('Use multiple deadlines', 'hurrytimer'); ?></span>
                        <span class="hurryt-badge hurryt-badge-pro"><?php _e('Pro', 'hurrytimer'); ?></span>
                    </a>
                    <?php //endRemoveIf(pro) ?>
                </div>
            </td>
            <td>
                <label for="hurrytimer-end-datetime" class="date">
                    <input type="text" name="end_datetime" autocomplete="off"
                           id="hurrytimer-end-datetime"
                           class="hurrytimer-datepicker hurryt-w-full"
                           value="<?php echo esc_attr($campaign->endDatetime) ?>"
                    >
                </label>
            </td>
        </tr>

        <?php //removeIf(pro) ?>
        <!-- Pro teaser dialog for multiple deadlines (free version only) -->
        <div id="hurryt-deadlines-pro-dialog" class="hurryt-pro-dialog-overlay" style="display:none;">
            <div class="hurryt-pro-dialog">
                <button type="button" class="hurryt-pro-dialog-close" id="hurryt-deadlines-dialog-close">&times;</button>
                <div class="hurryt-pro-dialog-icon">
                    <span class="dashicons dashicons-calendar-alt"></span>
                </div>
                <h3 class="hurryt-pro-dialog-title"><?php _e('Multiple Deadlines', 'hurrytimer'); ?> <span class="hurryt-badge hurryt-badge-pro"><?php _e('Pro', 'hurrytimer'); ?></span></h3>
                <p class="hurryt-pro-dialog-desc"><?php _e('Set a list of specific dates and let HurryTimer automatically cycle through them — no manual updates needed.', 'hurrytimer'); ?></p>
                <div class="hurryt-pro-dialog-usecases">
                    <div class="hurryt-pro-dialog-usecase">
                        <span class="dashicons dashicons-tag"></span>
                        <span><?php _e('Monthly flash sales with irregular deadlines', 'hurrytimer'); ?></span>
                    </div>
                    <div class="hurryt-pro-dialog-usecase">
                        <span class="dashicons dashicons-welcome-learn-more"></span>
                        <span><?php _e('Course or webinar enrollment closing dates', 'hurrytimer'); ?></span>
                    </div>
                    <div class="hurryt-pro-dialog-usecase">
                        <span class="dashicons dashicons-tickets-alt"></span>
                        <span><?php _e('Event registration cutoffs throughout the year', 'hurrytimer'); ?></span>
                    </div>
                    <div class="hurryt-pro-dialog-usecase">
                        <span class="dashicons dashicons-update"></span>
                        <span><?php _e('10 dates, set once, runs all year automatically', 'hurrytimer'); ?></span>
                    </div>
                </div>
                <div class="hurryt-pro-dialog-actions">
                    <a class="button button-primary button-hero" href="https://hurrytimer.com/pricing?utm_source=plugin&utm_medium=multiple_deadlines&utm_campaign=upgrade"><?php _e('Upgrade to Pro', 'hurrytimer'); ?></a>
                    <br>
                    <a class="hurryt-pro-dialog-learn-more" href="https://docs.hurrytimer.com/getting-started/multiple-deadlines" target="_blank"><?php _e('Learn more', 'hurrytimer'); ?> →</a>
                </div>
            </div>
        </div>
        <?php //endRemoveIf(pro) ?>

        <?php ?>

        <tr class="form-field hurrytimer-timezone-field">
            <td><label for="hurrytimer-timezone"><?php _e('Timezone', 'hurrytimer') ?> <span title="<?php esc_attr_e('By default, the site\'s timezone is used.', 'hurrytimer') ?>" class="hurryt-icon" data-icon="help"></span></label></td>
            <td>
                <div class="hurryt-flex hurryt-items-center">
                    <label class="hurryt-mr-4">
                        <input type="radio" name="timezone_type" value="site" <?php echo $campaign->timezoneType === 'site' ? 'checked' : ''; ?> class="hurryt-timezone-choice">
                        <?php esc_html_e('Use site\'s timezone', 'hurrytimer'); ?>
                    </label>
                    <label class="hurryt-mr-4">
                        <input type="radio" name="timezone_type"
                        <?php ?>
                        <?php //removeIf(pro) ?>
                        disabled
                        <?php //endRemoveIf(pro) ?>
                        value="custom" <?php echo $campaign->timezoneType === 'custom' ? 'checked' : ''; ?> class="hurryt-timezone-choice">
                        <?php esc_html_e('Select custom timezone ', 'hurrytimer'); ?>
                        <?php //removeIf(pro) ?>
                        <span
                       title="Available in Pro version. Upgrade to unlock."
                        class="hurryt-badge hurryt-badge-pro"><?php esc_html_e('Pro', 'hurrytimer'); ?></span>
                        <?php //endRemoveIf(pro) ?>
                    </label>
                    <div class="hurryt-custom-timezone hurryt-flex-grow" style="display: <?php echo $campaign->timezoneType === 'custom' ? 'block' : 'none'; ?>">
                        <select name="timezone" class="hurryt-w-full">
                            <?php echo wp_timezone_choice($saved_timezone, get_user_locale()); ?>
                        </select>
                    </div>
                </div>
                <p class="description">
                    <?php esc_html_e('Choose whether to use the site\'s timezone or select a custom one.', 'hurrytimer') ?>
                </p>
            </td>
        </tr>

 </table>
