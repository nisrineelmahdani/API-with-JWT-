<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite82149b647a3445ce8d0afeec8d41e3f
{
    public static $prefixLengthsPsr4 = array (
        'E' => 
        array (
            'Elmah\\Api\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Elmah\\Api\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite82149b647a3445ce8d0afeec8d41e3f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite82149b647a3445ce8d0afeec8d41e3f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite82149b647a3445ce8d0afeec8d41e3f::$classMap;

        }, null, ClassLoader::class);
    }
}
