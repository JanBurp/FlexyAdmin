<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit11f29e82defaaa1fb3c645f5fd377325
{
    public static $files = array (
        '75d4b1647cdbc77a59f72bcb74df0995' => __DIR__ . '/..' . '/spipu/html2pdf/html2pdf.class.php',
        '766ddebdb359eb94f1ba3ece4f768b10' => __DIR__ . '/..' . '/spipu/html2pdf/_class/exception.class.php',
        '585b118af784f8bbcc53fec65bb600cd' => __DIR__ . '/..' . '/spipu/html2pdf/_class/locale.class.php',
        '4148c0c72e9cb9146c3692e138ddcedc' => __DIR__ . '/..' . '/spipu/html2pdf/_class/myPdf.class.php',
        '24a5693ab78636f7a23448ee74523987' => __DIR__ . '/..' . '/spipu/html2pdf/_class/parsingHtml.class.php',
        '30eee86291d721c2174ad40239331e78' => __DIR__ . '/..' . '/spipu/html2pdf/_class/parsingCss.class.php',
    );

    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'o' => 
        array (
            'org\\bovigo\\vfs' => 
            array (
                0 => __DIR__ . '/..' . '/mikey179/vfsStream/src/main/php',
            ),
        ),
    );

    public static $classMap = array (
        'Datamatrix' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/barcodes/datamatrix.php',
        'PDF417' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/barcodes/pdf417.php',
        'QRcode' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/barcodes/qrcode.php',
        'TCPDF' => __DIR__ . '/..' . '/tecnickcom/tcpdf/tcpdf.php',
        'TCPDF2DBarcode' => __DIR__ . '/..' . '/tecnickcom/tcpdf/tcpdf_barcodes_2d.php',
        'TCPDFBarcode' => __DIR__ . '/..' . '/tecnickcom/tcpdf/tcpdf_barcodes_1d.php',
        'TCPDF_COLORS' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_colors.php',
        'TCPDF_FILTERS' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_filters.php',
        'TCPDF_FONTS' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_fonts.php',
        'TCPDF_FONT_DATA' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_font_data.php',
        'TCPDF_IMAGES' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_images.php',
        'TCPDF_IMPORT' => __DIR__ . '/..' . '/tecnickcom/tcpdf/tcpdf_import.php',
        'TCPDF_PARSER' => __DIR__ . '/..' . '/tecnickcom/tcpdf/tcpdf_parser.php',
        'TCPDF_STATIC' => __DIR__ . '/..' . '/tecnickcom/tcpdf/include/tcpdf_static.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit11f29e82defaaa1fb3c645f5fd377325::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit11f29e82defaaa1fb3c645f5fd377325::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit11f29e82defaaa1fb3c645f5fd377325::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit11f29e82defaaa1fb3c645f5fd377325::$classMap;

        }, null, ClassLoader::class);
    }
}
