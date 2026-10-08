<?php

namespace Hurrytimer\Utils;

class Form
{

    static function toggle($name, $value, $id, $small = false, $attrs = '')
    {
        ?>
         <input type="hidden"
               name="<?php echo esc_attr($name) ?>"
               value="no" 
          >
              
        <input type="checkbox"
               name="<?php echo esc_attr($name) ?>"
               id="<?php echo esc_attr($id) ?>"
               class="hurryt-input-toggle <?php echo $small ? 'is-small' : '' ?>"
               value="yes" 
               <?php echo $attrs ?>
            <?php 
            echo filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'checked' :'' ;
            ?>
        />
        <?php
    }

    /**
     * @param $name
     * @param $value
     *
     * @return string
     */
    static function colorInput($name, $value)
    {
        ?>
        <span class="hurrytimer-color-preview"></span>
        <input
                type="text"
                name="<?php echo esc_attr($name) ?>"
                placeholder="Select color"
                autocomplete="off"
                class="hurrytimer-color-input"
                value="<?php echo esc_attr($value) ?>" />
       <span class="dashicons dashicons-no-alt hurrytimer-color-clear"></span>
        <?php
    }
}