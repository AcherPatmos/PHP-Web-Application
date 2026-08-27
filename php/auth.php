<?php

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}