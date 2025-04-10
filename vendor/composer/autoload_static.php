<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit000672d23e2b305d22d7ef78307652cf
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'FuLang\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'FuLang\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'FuLang\\Core\\Interpreter' => __DIR__ . '/../..' . '/src/Core/Interpreter.php',
        'FuLang\\Core\\Parser' => __DIR__ . '/../..' . '/src/Core/Parser.php',
        'FuLang\\Exception\\EvalException' => __DIR__ . '/../..' . '/src/Exception/EvalException.php',
        'FuLang\\Exception\\ParseException' => __DIR__ . '/../..' . '/src/Exception/ParseException.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit000672d23e2b305d22d7ef78307652cf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit000672d23e2b305d22d7ef78307652cf::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit000672d23e2b305d22d7ef78307652cf::$classMap;

        }, null, ClassLoader::class);
    }
}
