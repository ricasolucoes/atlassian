<?php

Route::group(
    ['middleware' => ['web']], function () {
        Route::prefix('atlassian')->group(
            function () {
                Route::group(
                    ['as' => 'atlassian.'], function () {
                    }
                );
            }
        );
    }
);
