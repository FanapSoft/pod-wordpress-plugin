<div class="wrap">
    <h3><?php echo __('تنظیمات سامانه احراز هویت پاد'); ?></h3>
    <hr />

    <div class="notice notice-success">
        <p>
            برای دریافت اطلاعات سامانه احراز هویت با اکانت کسب و کاری خود به <a target="_blank" href="https://accounts.pod.land">سامانه پاد</a> لاگین نمایید.
        </p>
    </div>
    <div class="content">
        <form method="post" action="options.php">
            <?php settings_fields('pod_options');

            if (!isset($options["client_id"])) {
                $options["client_id"] = '';
            }
            if (!isset($options["client_secret"])) {
                $options["client_secret"] = '';
            }
            if (!isset($options["api_token"])) {
                $options["api_token"] = '';
            }
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">شناسه مشتری (Client_Id):</th>
                    <td>
                        <input type="text" name="<?php echo $this->option_name ?>[client_id]" min="10" value="<?php echo $options["client_id"]; ?>" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">رمز مشتری (Client_Secret):</th>
                    <td>
                        <input type="text" name="<?php echo $this->option_name ?>[client_secret]" min="10" value="<?php echo $options["client_secret"]; ?>" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">توکن کسب و کار شما (API Token):</th>
                    <td>
                        <input type="text" name="<?php echo $this->option_name ?>[api_token]" min="10" value="<?php echo $options["api_token"]; ?>" />
                        <p class="description"></p>
                    </td>
                </tr>

            </table>

            <div class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </div>
        </form>
    </div>
</div>