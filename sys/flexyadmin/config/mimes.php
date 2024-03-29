<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| MIME TYPES
| -------------------------------------------------------------------
| This file contains an array of mime types.  It is used by the
| Upload class to help identify allowed file types.
|
*/

return array(
    '3g2'   => 'video/3gpp2',
    '3gp'   => array('video/3gp', 'video/3gpp'),
    '7z'    => array('application/x-7z-compressed', 'application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'),
    '7zip'  => array('application/x-7z-compressed', 'application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'),
    'aac'   => array('audio/x-aac', 'audio/aac'),
    'ac3'   => 'audio/ac3',
    'ai'    => array('application/pdf', 'application/postscript'),
    'aif'   => array('audio/x-aiff', 'audio/aiff'),
    'aifc'  => 'audio/x-aiff',
    'aiff'  => array('audio/x-aiff', 'audio/aiff'),
    'au'    => 'audio/x-au',
    'avi'   => array('video/x-msvideo', 'video/msvideo', 'video/avi', 'application/x-troff-msvideo'),
    'bin'   => array('application/macbinary', 'application/mac-binary', 'application/octet-stream', 'application/x-binary', 'application/x-macbinary'),
    'bmp'   => array('image/bmp', 'image/x-bmp', 'image/x-bitmap', 'image/x-xbitmap', 'image/x-win-bitmap', 'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 'application/bmp', 'application/x-bmp', 'application/x-win-bitmap'),
    'cdr'   => array('application/cdr', 'application/coreldraw', 'application/x-cdr', 'application/x-coreldraw', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr'),
    'cer'   => array('application/pkix-cert', 'application/x-x509-ca-cert'),
    'class' => 'application/octet-stream',
    'cpt'   => 'application/mac-compactpro',
    'crl'   => array('application/pkix-crl', 'application/pkcs-crl'),
    'crt'   => array('application/x-x509-ca-cert', 'application/x-x509-user-cert', 'application/pkix-cert'),
    'csr'   => 'application/octet-stream',
    'css'   => array('text/css', 'text/plain'),
    'csv'   => array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'),
    'dcr'   => 'application/x-director',
    'der'   => 'application/x-x509-ca-cert',
    'dir'   => 'application/x-director',
    'dll'   => 'application/octet-stream',
    'dms'   => 'application/octet-stream',
    'doc'   => array('application/msword', 'application/vnd.ms-office'),
    'docx'  => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword', 'application/x-zip', 'application/octet-stream'),
    'dot'   => array('application/msword', 'application/vnd.ms-office'),
    'dotx'  => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword'),
    'dvi'   => 'application/x-dvi',
    'dxr'   => 'application/x-director',
    'eml'   => 'message/rfc822',
    'eps'   => 'application/postscript',
    'exe'   => array('application/octet-stream', 'application/x-msdownload'),
    'f4v'   => array('video/mp4', 'video/x-f4v'),
    'flac'  => 'audio/x-flac',
    'flv'   => array('application/octet-stream', 'video/x-flv'),
    'gif'   => 'image/gif',
    'gpg'   => 'application/gpg-keys',
    'gpx'   => array('text/xml', 'application/octet-stream', 'application/gpx', 'application/gpx+xml'),
    'gtar'  => 'application/x-gtar',
    'gz'    => 'application/x-gzip',
    'gzip'  => 'application/x-gzip',
    'hqx'   => array('application/mac-binhex40', 'application/mac-binhex', 'application/x-binhex40', 'application/x-mac-binhex40'),
    'htm'   => array('text/html', 'text/plain'),
    'html'  => array('text/html', 'text/plain'),
    'heic'  => 'image/heic',
    'heif'  => 'image/heif',
    'ical'  => 'text/calendar',
    'ico'   => array('image/x-icon', 'image/x-ico', 'image/vnd.microsoft.icon'),
    'ics'   => 'text/calendar',
    'j2k'   => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
    'jar'   => array('application/java-archive', 'application/x-java-application', 'application/x-jar', 'application/x-compressed'),
    'jp2'   => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
    'jpe'   => array('image/jpeg', 'image/pjpeg'),
    'jpeg'  => array('image/jpeg', 'image/pjpeg'),
    'jpf'   => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
    'jpg'   => array('image/jpeg', 'image/pjpeg'),
    'jpg2'  => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
    'jpm'   => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
    'jpx'   => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
    'js'    => array('application/x-javascript', 'text/plain'),
    'json'  => array('application/json', 'text/json'),
    'kdb'   => 'application/octet-stream',
    'kml'   => array('application/vnd.google-earth.kml+xml', 'application/xml', 'text/xml'),
    'kmz'   => array('application/vnd.google-earth.kmz', 'application/zip', 'application/x-zip'),
    'lha'   => 'application/octet-stream',
    'log'   => array('text/plain', 'text/x-log'),
    'lzh'   => 'application/octet-stream',
    'm3u'   => 'text/plain',
    'm4a'   => 'audio/x-m4a',
    'm4u'   => 'application/vnd.mpegurl',
    'mid'   => 'audio/midi',
    'midi'  => 'audio/midi',
    'mif'   => 'application/vnd.mif',
    'mj2'   => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
    'mjp2'  => array('image/jp2', 'video/mj2', 'image/jpx', 'image/jpm'),
    'mov'   => 'video/quicktime',
    'movie' => 'video/x-sgi-movie',
    'mp2'   => 'audio/mpeg',
    'mp3'   => array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
    'mp4'   => 'video/mp4',
    'mpe'   => 'video/mpeg',
    'mpeg'  => 'video/mpeg',
    'mpg'   => 'video/mpeg',
    'mpga'  => 'audio/mpeg',
    'oda'   => 'application/oda',
    'odc'   => 'application/vnd.oasis.opendocument.chart',
    'odf'   => 'application/vnd.oasis.opendocument.formula',
    'odg'   => 'application/vnd.oasis.opendocument.graphics',
    'odi'   => 'application/vnd.oasis.opendocument.image',
    'odm'   => 'application/vnd.oasis.opendocument.text-master',
    'odp'   => 'application/vnd.oasis.opendocument.presentation',
    'ods'   => 'application/vnd.oasis.opendocument.spreadsheet',
    'odt'   => 'application/vnd.oasis.opendocument.text',
    'ogg'   => array('audio/ogg', 'video/ogg', 'application/ogg'),
    'otc'   => 'application/vnd.oasis.opendocument.chart-template',
    'otf'   => 'application/vnd.oasis.opendocument.formula-template',
    'otg'   => 'application/vnd.oasis.opendocument.graphics-template',
    'oth'   => 'application/vnd.oasis.opendocument.text-web',
    'oti'   => 'application/vnd.oasis.opendocument.image-template',
    'otp'   => 'application/vnd.oasis.opendocument.presentation-template',
    'ots'   => 'application/vnd.oasis.opendocument.spreadsheet-template',
    'ott'   => 'application/vnd.oasis.opendocument.text-template',
    'p10'   => array('application/x-pkcs10', 'application/pkcs10'),
    'p12'   => 'application/x-pkcs12',
    'p7a'   => 'application/x-pkcs7-signature',
    'p7c'   => array('application/pkcs7-mime', 'application/x-pkcs7-mime'),
    'p7m'   => array('application/pkcs7-mime', 'application/x-pkcs7-mime'),
    'p7r'   => 'application/x-pkcs7-certreqresp',
    'p7s'   => 'application/pkcs7-signature',
    'pdf'   => array('application/pdf', 'application/force-download', 'application/x-download', 'binary/octet-stream', 'application/binary', 'application/x-pdf', 'application/unknown'),
    'pem'   => array('application/x-x509-user-cert', 'application/x-pem-file', 'application/octet-stream'),
    'pgp'   => 'application/pgp',
    'php'   => array('application/x-httpd-php', 'application/php', 'application/x-php', 'text/php', 'text/x-php', 'application/x-httpd-php-source'),
    'php3'  => 'application/x-httpd-php',
    'php4'  => 'application/x-httpd-php',
    'phps'  => 'application/x-httpd-php-source',
    'phtml' => 'application/x-httpd-php',
    'png'   => array('image/png',  'image/x-png'),
    'ppt'   => array('application/powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.ms-office', 'application/msword'),
    'pptx'  => array('application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/x-zip', 'application/zip', 'application/vnd.ms-powerpoint'),
    'ps'    => 'application/postscript',
    'psd'   => array('application/x-photoshop', 'image/vnd.adobe.photoshop'),
    'qt'    => 'video/quicktime',
    'ra'    => 'audio/x-realaudio',
    'ram'   => 'audio/x-pn-realaudio',
    'rar'   => array('application/x-rar', 'application/rar', 'application/x-rar-compressed'),
    'rm'    => 'audio/x-pn-realaudio',
    'rpm'   => 'audio/x-pn-realaudio-plugin',
    'rsa'   => 'application/x-pkcs7',
    'rtf'   => 'text/rtf',
    'rtx'   => 'text/richtext',
    'rv'    => 'video/vnd.rn-realvideo',
    'sea'   => 'application/octet-stream',
    'shtml' => array('text/html', 'text/plain'),
    'sit'   => 'application/x-stuffit',
    'smi'   => 'application/smil',
    'smil'  => 'application/smil',
    'so'    => 'application/octet-stream',
    'sql'   => array('text/x-sql', 'application/octet-stream', 'text/plain', 'text/x-lisp', 'text/html'),
    'srt'   => array('text/srt', 'text/plain'),
    'sst'   => 'application/octet-stream',
    'svg'	=>	array('image/svg+xml', 'image/svg', 'application/xml', 'text/xml'),
    'swf'   => 'application/x-shockwave-flash',
    'tar'   => 'application/x-tar',
    'text'  => 'text/plain',
    'tgz'   => array('application/x-tar', 'application/x-gzip-compressed'),
    'tif'   => 'image/tiff',
    'tiff'  => 'image/tiff',
    'txt'   => array('text/plain', 'text/html', 'text/x-lisp'),
    'vcf'   => array('text/vcard', 'text/x-vcard'),
    'vlc'   => 'application/videolan',
    'vtt'   => array('text/vtt', 'text/plain'),
    'wav'   => array('audio/x-wav', 'audio/wave', 'audio/wav'),
    'wbxml' => 'application/wbxml',
    'webm'  => 'video/webm',
    'wma'   => array('audio/x-ms-wma', 'video/x-ms-asf'),
    'wmlc'  => 'application/wmlc',
    'wmv'   => array('video/x-ms-wmv', 'video/x-ms-asf'),
    'word'  => array('application/msword', 'application/octet-stream'),
    'xht'   => 'application/xhtml+xml',
    'xhtml' => 'application/xhtml+xml',
    'xl'    => 'application/excel',
    'xls'   => array('application/vnd.ms-excel', 'application/msexcel', 'application/x-msexcel', 'application/x-ms-excel', 'application/x-excel', 'application/x-dos_ms_excel', 'application/xls', 'application/x-xls', 'application/excel', 'application/download', 'application/vnd.ms-office', 'application/msword'),
    'xlsx'  => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/vnd.ms-excel', 'application/msword', 'application/x-zip'),
    'xml'   => array('application/xml', 'text/xml', 'text/plain'),
    'xsl'   => array('application/xml', 'text/xsl', 'text/xml'),
    'xspf'  => 'application/xspf+xml',
    'z'     => 'application/x-compress',
    'zip'   => array('application/x-zip', 'application/zip', 'application/x-zip-compressed', 'application/s-compressed', 'multipart/x-zip'),
    'zsh'   => 'text/x-scriptzsh',
);

/* End of file mimes.php */
/* Location: ./system/application/config/mimes.php */
