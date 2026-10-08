<?php namespace Hurrytimer;
global $post_id;
?>
<table class="form-table hidden mode-settings" data-for="hurrytModeEvergreen">

    <tr class="form-field">
        <td style="padding-bottom: 0;">
            <label>
                <select name="evergreen_end_type" id="hurrytimer-evergreen-end-type">
                    <option value="duration" <?php selected($campaign->getEvergreenEndType(), 'duration') ?>>
                        <?php _e( "Ends after", "hurrytimer" ) ?>
                    </option>
                    <option value="time" <?php selected($campaign->getEvergreenEndType(), 'time') ?> >
                        <?php _e( "Ends at", "hurrytimer" ) ?>
                    </option>
                </select>
                <span class="hurryt-icon hurryt-ml-1 <?php echo $campaign->getEvergreenEndType() === 'time' ? '' : 'hidden' ?>" id="hurryt-ends-at-tooltip" data-icon="help" title="<?php esc_attr_e('Set a specific time and day when the countdown should end. If the time has already passed on the same day, it will automatically move to the next occurrence.', 'hurrytimer') ?>" style="vertical-align: middle;"></span>
            </label>
        </td>
        <td>
            <div class="hurrytimer-field-duration" <?php echo $campaign->getEvergreenEndType() === 'duration' ? '' : 'style="display:none;"' ?>>
                <label>
                    <?php _e( "Days", "hurrytimer" ) ?>
                    <input type="number"
                           class="hurrytimer-duration"
                           name="duration[]"
                           min="0"
                           data-index="0"
                           value="<?php echo esc_attr($campaign->duration[ 0 ]) ?>">
                </label>
                <label>
                    <?php _e( "Hours", "hurrytimer" ) ?>
                    <input type="number"
                           class="hurrytimer-duration"
                           name="duration[]"
                           min="0"
                           data-index="1"
                           value="<?php echo esc_attr($campaign->duration[ 1 ]) ?>"
                    >
                </label>
                <label>
                    <?php _e( "Minutes", "hurrytimer" ) ?>
                    <input type="number"
                           class="hurrytimer-duration"
                           name="duration[]"
                           min="0"
                           data-index="2"
                           value="<?php echo esc_attr($campaign->duration[ 2 ]) ?>"
                    >
                </label>
                <label>
                    <?php _e( "Seconds", "hurrytimer" ) ?>
                    <input type="number"
                           class="hurrytimer-duration"
                           name="duration[]"
                           data-index="3"
                           value="<?php echo esc_attr($campaign->duration[ 3 ]) ?>"
                    >
                </label>
            </div>

            <div class="hurrytimer-field-time hurryt-w-full" <?php echo $campaign->getEvergreenEndType() === 'time' ? '' : 'style="display:none"' ?>>
                <?php if (!defined('HURRYT_IS_PRO') || !HURRYT_IS_PRO): ?>
                <p class="description" style="margin-bottom: 10px;">
                    <span class="dashicons dashicons-lock" style="color:#828282"></span>
                    <b>Ends At</b> is a pro feature. <a href="http://hurrytimer.com/#pricing?utm_source=plugin&utm_medium=ends_at&utm_campaign=upgrade">Upgrade now</a>.
                </p>
                <?php endif; ?>
                <div class="hurryt-flex hurryt-items-center hurryt-gap-2">
                    <input type="time" name="evergreen_end_time" id="evergreen_end_time" value="<?php echo esc_attr($campaign->getEvergreenEndTime()) ?>" class="hurryt-w-32 <?php echo !defined('HURRYT_IS_PRO') || !HURRYT_IS_PRO ? 'disabled' : '' ?>" <?php echo !defined('HURRYT_IS_PRO') || !HURRYT_IS_PRO ? 'disabled' : '' ?>>
                    &nbsp;<span><?php _e('on', 'hurrytimer') ?></span>&nbsp;
                    <select name="evergreen_end_day" id="evergreen_end_day" class="hurryt-w-40 <?php echo !defined('HURRYT_IS_PRO') || !HURRYT_IS_PRO ? 'disabled' : '' ?>" <?php echo !defined('HURRYT_IS_PRO') || !HURRYT_IS_PRO ? 'disabled' : '' ?>>
                        <option value="0" <?php selected($campaign->getEvergreenEndDay(), 0) ?>><?php _e('Same day', 'hurrytimer') ?></option>
                        <option value="1" <?php selected($campaign->getEvergreenEndDay(), 1) ?>><?php _e('Next day', 'hurrytimer') ?></option>
                        <option value="2" <?php selected($campaign->getEvergreenEndDay(), 2) ?>><?php _e('2 days later', 'hurrytimer') ?></option>
                        <option value="3" <?php selected($campaign->getEvergreenEndDay(), 3) ?>><?php _e('3 days later', 'hurrytimer') ?></option>
                        <option value="4" <?php selected($campaign->getEvergreenEndDay(), 4) ?>><?php _e('4 days later', 'hurrytimer') ?></option>
                        <option value="5" <?php selected($campaign->getEvergreenEndDay(), 5) ?>><?php _e('5 days later', 'hurrytimer') ?></option>
                        <option value="6" <?php selected($campaign->getEvergreenEndDay(), 6) ?>><?php _e('6 days later', 'hurrytimer') ?></option>
                        <option value="7" <?php selected($campaign->getEvergreenEndDay(), 7) ?>><?php _e('7 days later', 'hurrytimer') ?></option>
                    </select>
                </div>
            </div>
        </td>
    </tr>
    <tr class="form-field">
        <td>
            <label><?php _e( 'Detection methods', "hurrytimer" ) ?></label>
        </td>
        <td>
            <div>
                <label class="hurryt-mr-2">
                    <input class="hurryt-m-0" type="checkbox" name="detection_methods[]"
                           value="<?php echo C::DETECTION_METHOD_COOKIE ?>"
                        <?php echo in_array( C::DETECTION_METHOD_COOKIE,
                            $campaign->detectionMethods ) ? 'checked' : '' ?>
                    >
                    <?php _e( "Cookie", "hurrytimer" ) ?>
                </label>
                <label class="hurryt-mr-2">
                    <input class="hurryt-m-0" type="checkbox" name="detection_methods[]"
                           value="<?php echo C::DETECTION_METHOD_IP ?>"
                        <?php echo in_array( C::DETECTION_METHOD_IP,
                            $campaign->detectionMethods ) ? 'checked' : '' ?>
                    >
                    <?php _e( "IP address", "hurrytimer" ) ?>

                </label>
                <label id="hurrytUserSessionWrap" for="hurrytUserSessionMethod">
                    <input 
                   
                        id="hurrytUserSessionMethod"
                     class="hurryt-mx-0
                     <?php 
        // removeIf(pro)
                     echo 'disabled style="pointer-events:none"';
        // endRemoveIf(pro)

                     ?>
                     
                     " type="checkbox" name="detection_methods[]"
                           value="<?php echo C::DETECTION_METHOD_USER_SESSION ?>"
                        <?php echo in_array( C::DETECTION_METHOD_USER_SESSION,
                            $campaign->detectionMethods ) ? 'checked' : '' ?>
                    >
                    <?php _e( "User Session", "hurrytimer" ) ?> <span
                            title="This is recommended for campaigns restricted to logged-in users."
                            class="hurryt-icon" data-icon="help" style="vertical-align: middle;"></span>

                </label>
            </div>
            <?php // removeIf(pro) ?>
            <p class="description hidden" style="margin-top: 10px;" id="hurrytUserSessionUpgradeNotice">
            <span class="dashicons dashicons-lock" style="color:#828282"></span>
                <b>User Session</b> method is a pro feature. <a
                    href="http://hurrytimer.com/#pricing?utm_source=plugin&utm_medium=user_sess&utm_campaign=upgrade">Upgrade
                now</a>.
            </p>
            <?php // endRemoveIf(pro) ?>
        </td>
    </tr>
    <tr class="form-field">
        <td><label for="active"><?php _e( "Restart when expired", "hurrytimer" ) ?></label></td>
        <td>
            <select name="restart" id="hurrytimer-evergreen-restart"
                    class="hurryt-w-full">
                <option value="<?php echo C::RESTART_NONE ?>" <?php echo selected( $campaign->restart,
                    C::RESTART_NONE ) ?>>
                    <?php _e( "None", "hurrytimer" ) ?>
                </option>
                <option value="<?php echo C::RESTART_IMMEDIATELY ?>" <?php echo selected( $campaign->restart,
                    C::RESTART_IMMEDIATELY ) ?>>
                    <?php _e( "Restart immediately", "hurrytimer" ) ?>
                </option>
                <option value="<?php echo C::RESTART_AFTER_RELOAD ?>" <?php echo selected( $campaign->restart,
                    C::RESTART_AFTER_RELOAD ) ?>>
                    <?php _e( "Restart at the next visit", "hurrytimer" ) ?>
                </option>
                <option value="<?php echo C::RESTART_AFTER_DURATION ?>" <?php echo selected( $campaign->restart,
                    C::RESTART_AFTER_DURATION ) ?>>
                    <?php _e( "Restart after a specific time...", "hurrytimer" ) ?>
                </option>
            </select>
            <?php // removeIf(pro) ?>
            <p class="description hidden" style="margin-top: 10px;" id="hurrytimer-restart-after-feature-unlock">
                <b><span class="dashicons dashicons-lock" style="color:#828282"></span> Restart After a Specific Time</b> is a pro feature. <a
                    href="http://hurrytimer.com/#pricing?utm_source=plugin&utm_medium=restart_after&utm_campaign=upgrade">Upgrade
                now</a>.
            </p>
            <?php // endRemoveIf(pro) ?>

        </td>
    </tr>
    <tr class="form-field" id="hurrytimer-evergreen-restart-duration"
    
    <?php // removeIf(pro) ?>
     style="opacity: .5; pointer-events:none;"
     <?php //endRemoveIf(pro) ?>
     >
        <td>
            <label><?php _e( 'Restart after', "hurrytimer" ) ?></label>
        </td>
        <td>
            <div class="hurrytimer-field-duration">
                <label>
                    <?php _e( "Days", "hurrytimer" ) ?>

                    <input type="number"
                           class="hurrytimer-duration"
                           name="restart_duration[days]"
                           min="0"
                           value="<?php echo esc_attr($campaign->restartDuration[ 'days' ]) ?>">
                </label>
                <label>
                    <?php _e( "Hours", "hurrytimer" ) ?>
                    <input type="number"
                           class="hurrytimer-duration"
                           name="restart_duration[hours]"
                           min="0"
                           value="<?php echo esc_attr($campaign->restartDuration[ 'hours' ]) ?>"
                    >

                </label>
                <label>
                    <?php _e( "Minutes", "hurrytimer" ) ?>
                    <input type="number"
                           class="hurrytimer-duration"
                           name="restart_duration[minutes]"
                           min="0"
                           value="<?php echo esc_attr($campaign->restartDuration[ 'minutes' ]) ?>"
                    >

                </label>
                <label>
                    <?php _e( "Seconds", "hurrytimer" ) ?>
                    <input type="number"
                           class="hurrytimer-duration"
                           name="restart_duration[seconds]"
                           value="<?php echo esc_attr($campaign->restartDuration[ 'seconds' ]) ?>"
                    >

                </label>
            </div>
        </td>
    </tr>

    <tr class="form-field">
        <td><label for="active"><?php _e( "Reset on page reload", "hurrytimer" ) ?></label></td>
        <td>
            <?php Utils\Form::toggle( 'reload_reset',
                $campaign->reloadReset,
                'hurrytimer-reload-reset' ); ?>

            <p class="description">This will force visitor timer to reset even if it is not yet expired.</p>
        </td>
    </tr>
    <?php if ( $post_id !== null ): ?>
        <tr class="form-field">
            <td><label for="active"><?php _e( "Reset countdown now", "hurrytimer" ) ?>
                </label></td>
            <td>
                <div>
                    <button type="button" data-id="<?php echo esc_attr($post_id) ?>"
                            data-cookie="<?php echo esc_attr(Cookie_Detection::cookieName( $post_id )) ?>"
                            data-url="<?php echo esc_url($resetCampaignCurrentAdminUrl) ?>" class="button button-default"
                            id="hurrytResetCurrent">Only for me
                    </button>
                    &nbsp;
                    <button type="button" data-url="<?php echo esc_url($resetCampaignAllVisitorsUrl) ?>"
                            class="button button-default" id="hurrytResetAll">For all visitors...
                    </button>
                </div>
                <p class="description">Reset the countdown timer to apply changes for existing users who have an active timer.</p>

            </td>
        </tr>
    <?php endif; ?>
</table>

<script>
jQuery(document).ready(function($) {
    $('#hurrytimer-evergreen-end-type').on('change', function() {
        $('#hurryt-ends-at-tooltip').toggleClass('hidden', $(this).val() !== 'time');
    });
});
</script>

<style>
.hurrytimer-field-time input.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background-color: #f0f0f0;
}
</style>