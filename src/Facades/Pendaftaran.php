<?php namespace Bantenprov\Pendaftaran\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * The Pendaftaran facade.
 *
 * @package Bantenprov\Pendaftaran
 * @author  bantenprov <developer.bantenprov@gmail.com>
 */
class Pendaftaran extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pendaftaran';
    }
}
