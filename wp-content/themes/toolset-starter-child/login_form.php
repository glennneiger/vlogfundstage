<?php // Template for my form shortcode ?>
    <form id="login_user" class="ajax-auth" action="login" method="post">
        <p class="status"></p>
        <input type="text" style="position: fixed; left: -10000000px;" autofocus="autofocus" disabled/>
        <input id="username" type="text" class="sf-popup-input required" placeholder="Username or Email" name="username" autocomplete='email' required>
        <input id="password" type="password" class="sf-popup-input required" placeholder="Enter Password" name="password" current-password required>
        <input class="frm_final_submit submit_button" type="submit" value="LOGIN" name="submit">
    </form>
