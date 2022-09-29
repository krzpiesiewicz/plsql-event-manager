<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcd2d024fa8fbcda5372cf15ebdd61312
{
    public static $prefixLengthsPsr4 = array (
        'E' => 
        array (
            'Eloquent\\Enumeration\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Eloquent\\Enumeration\\' => 
        array (
            0 => __DIR__ . '/..' . '/eloquent/enumeration/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcd2d024fa8fbcda5372cf15ebdd61312::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcd2d024fa8fbcda5372cf15ebdd61312::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}