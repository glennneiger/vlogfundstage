<form id="register_user" class="ajax-auth"  action="register" method="post">
    <p class="status"></p>
    <!--<?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?>-->
    <input id="signonname" type="text" name="signonname" placeholder="Username" class="sf-popup-input required" required>

    <input id="email" type="text" class="sf-popup-input required email" placeholder="Email" name="email" autocomplete='email' required>

    <input id="signonpassword" type="password" class="sf-popup-input required" placeholder="Password" name="signonpassword" new-password required>

    <input type="password" id="password2" class="sf-popup-input required" placeholder="Repeat Password" name="password2" new-password required>

    <input class="frm_final_submit submit_button" type="submit" value="SIGNUP">
</form>
