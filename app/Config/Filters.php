<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use App\Filters\AuthGuard;
use App\Filters\AuthGuardAdmin;
use App\Filters\AuthGuardNotif;
use App\Filters\AuthGuardPembimbing;
use App\Filters\AuthGuardPeserta;
use App\Filters\authGuardTU;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array
     */
    public $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'authGuard'     => AuthGuard::class,
        'authGuardAdmin' => AuthGuardAdmin::class,
        'authGuardNotif' => AuthGuardNotif::class,
        'authGuardPembimbing' => AuthGuardPembimbing::class,
        'authGuardPeserta'   => AuthGuardPeserta::class,
        'authGuardTU'   => AuthGuardTU::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array
     */
    public $globals = [
        'before' => [
            //     'honeypot',
            //     'csrf',
            //     'invalidchars',
        ],
        'after' => [
            //    'toolbar',
            //    'honeypot',
            //    'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['csrf', 'throttle']
     *
     * @var array
     */
    public $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array
     */
    public $filters = [
        'authGuard' => ['before' => ['dashboard/*']],
        'authGuardAdmin' => ['before' => ['Admin/*']],
        'authGuardNotif' => ['before' => ['Notif/*']],
        'authGuardPembimbing' => ['before' => ['Pembimbing/*']],
        'authGuardPeserta' => ['before' => ['Peserta/*']],
        'authGuardTU' => ['before' => ['TU/*']],
    ];
}
