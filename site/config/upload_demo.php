<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Upload folder
|--------------------------------------------------------------------------
|
| Als attachments meegestuurd moeten kunnen worden stel dan hier de map in waar ze naar worden geupload en welke bestanden zijn toegestaan.
*/

$config['upload_folder'] = 'downloads';


/*
|--------------------------------------------------------------------------
| Upload types
|--------------------------------------------------------------------------
|
| Bepaal hier de toegestane bestanden die geupload mogen worden.
| Als je niets invoert dan wordt gekeken naar de standaard instellingen van de 'upload_folder' zoals ingesteld in Media_Info
*/

$config['upload_types']  = 'gif|jpg|png|doc|docx|xls|xlsx|pdf|zip';


