<?php // LOAD FACEBOOK JS API   ?>
<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
    FB.init({
        appId   : '<?php echo $this->auth_connect->connection->getAppID(); ?>',
        status  : true, <?php // check login status   ?>
        cookie  : true, <?php // enable cookies to allow the server to access the session   ?>
        xfbml   : true <?php // parse XFBML   ?>
    });

    <?php // whenever the user logs in, we refresh the page   ?>
    FB.Event.subscribe('auth.login', function() {
        window.location.reload();
    });

    FB.Event.subscribe('auth.logout', function(response) {
        window.location.reload();
    });
    };
    (function() {
        var e = document.createElement('script');
            e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
            e.async = true;
            document.getElementById('fb-root').appendChild(e);
    }());
</script>
