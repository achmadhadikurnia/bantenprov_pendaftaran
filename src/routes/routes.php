<?php

Route::group(['prefix' => 'pendaftaran'], function() {
    Route::get('demo', 'Bantenprov\Pendaftaran\Http\Controllers\PendaftaranController@demo');
});
