<?php
// routes/api.php

Route::group(['prefix' => 'v1'], function () {
    // Other routes...
    require __DIR__ . '/../app/Modules/SiteVisits/Routes/api.php';
    require __DIR__ . '/../app/Modules/FollowUp/Routes/api.php';
});
