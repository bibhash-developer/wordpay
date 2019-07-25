<?php
    //pr($user);
?>
Please <a href="{{ url(config('constants.front_url').'/reset-password') }}/{{ $user['token'] }}">Click Here</a> to reset your password