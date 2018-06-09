<?php // Template for my form shortcode ?>
<!--<form id="login" action="login" method="post">
        <p class="status"></p>

        <input id="username" type="text" name="username" class="sf-popup-input" placeholder="Username or email" required>

        <input id="password" type="password" name="password" class="sf-popup-input" placeholder="Enter password" required>

        <input class="frm_final_submit" type="submit" value="Login" name="submit">

    </form>-->

    <form id="login_user" class="ajax-auth" action="login" method="post">
        <p class="status"></p>

        <input id="username" type="text" class="sf-popup-input required" placeholder="Username or Email" name="username" autocomplete='email' required>

        <input id="password" type="password" class="sf-popup-input required" placeholder="Enter Password" name="password" current-password required>

        <input class="frm_final_submit submit_button" type="submit" value="LOGIN" name="submit">

    </form>
