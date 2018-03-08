<?php

Route::group(['prefix' => 'api/pendaftaran', 'middleware' => ['web']], function() {
    $controllers = (object) [
        'index'     => 'Bantenprov\Pendaftaran\Http\Controllers\PendaftaranController@index',
        'create'    => 'Bantenprov\Pendaftaran\Http\Controllers\PendaftaranController@create',
        'show'      => 'Bantenprov\Pendaftaran\Http\Controllers\PendaftaranController@show',
        'store'     => 'Bantenprov\Pendaftaran\Http\Controllers\PendaftaranController@store',
        'edit'      => 'Bantenprov\Pendaftaran\Http\Controllers\PendaftaranController@edit',
        'update'    => 'Bantenprov\Pendaftaran\Http\Controllers\PendaftaranController@update',
        'destroy'   => 'Bantenprov\Pendaftaran\Http\Controllers\PendaftaranController@destroy',
    ];

    Route::get('/',             $controllers->index)->name('pendaftaran.index');
    Route::get('/create',       $controllers->create)->name('pendaftaran.create');
    Route::get('/{id}',         $controllers->show)->name('pendaftaran.show');
    Route::post('/',            $controllers->store)->name('pendaftaran.store');
    Route::get('/{id}/edit',    $controllers->edit)->name('pendaftaran.edit');
    Route::put('/{id}',         $controllers->update)->name('pendaftaran.update');
    Route::delete('/{id}',      $controllers->destroy)->name('pendaftaran.destroy');
});
