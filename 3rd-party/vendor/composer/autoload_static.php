<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4794e46b2995c15720872b9f9f4d6935
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Carbon_Fields\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Carbon_Fields\\' => 
        array (
            0 => __DIR__ . '/..' . '/htmlburger/carbon-fields/core',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Parsedown' => 
            array (
                0 => __DIR__ . '/..' . '/erusev/parsedown',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4794e46b2995c15720872b9f9f4d6935::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4794e46b2995c15720872b9f9f4d6935::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit4794e46b2995c15720872b9f9f4d6935::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
