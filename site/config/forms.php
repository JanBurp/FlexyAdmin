<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Alle formulieren worden ingesteld met een eigen config array.
| De key is de naam van het formulier.
| 
| Hieronder de instellingen voor het formulier 'contact'
|--------------------------------------------------------------------------
*/


$config['contact'] = array(
  
  // Titel van het formulier
  'title'                   => lang('contact_title'),
  
  // Velden van het formulier. De labels kunnen een verwijzing naar de taalbestanden bevatten.
  'fields'                  => array(
                                'str_name'		  => array( 'label'=>lang('field__str_name'),     'validation'=>'required' ),
                                'email_email'	  => array( 'label'=>lang('field__email_email'),  'validation'	=>  'required|valid_email' ),
                                'txt_text'	    => array( 'label'=>lang('field__txt_text'),     'type'=>'textarea', 'validation'=>'required' )
                              ),
                            
  // Voegt placeholders toe aan de velden, deze zijn hetzelfde als de labels
  'placeholders_as_labels'  => true,

  // Geef aan waar de validation errors komen: 'form' of 'field' of een algemene tekst.
  'validation_place'        => 'field',
                  
  // Controleer op spam (Voegt een extra (hidden) veld toe om op spamrobots te testen (veld heeft class='hidden', dit moet je in je stylesheet ook daadwerkelijk onzichtbaar maken))
  'check_for_spam'          => true,
  
  // Voorkom dat het formulier meerdere keren kan worden verzonden door een pagina refresh.
  // NB deze optie maakt gebruik van sessions (de session library wordt automatisch geladen).
  'prevend_double_submit'   => true,
  
  // Knoppen van het formulier                
  'buttons'                 => array( 'submit'=>array('submit'=>'submit', 'value'=>lang('submit')) ),
  
  // Welk model doet de afhandeling van dit formulier? Standaard de email
  'formaction'              => 'formaction_mail',
  
  // Specifieke instellingen voor: formaction_mail
  'subject'                 => lang('subject'),                          // Onderwerp van de email. Je kunt er codes inzetten die vervangen worden: %URL% = Url van de site, %MAIL% = 1e email veld, of een willekeurig veld %veldnaam%.
  'send_copy_to_sender'     => FALSE,                                    // Als TRUE dan krijgt de bezoeker die het formulier invult zelf ook een exemplaar toegestuurd.
  'from_address_field'      => 'email_email',                            // Veld in het formulier met het emailadres van de bezoeker
  'attachment_folder'       => 'downloads',                              // Als er files velden in het formulier bestaan is die de map waar de bestanden/attachments naar worden geupload
  'attachment_types'        => 'gif|jpg|png|doc|docx|xls|xlsx|pdf|zip',  // En dit zijn dan de toegestane bestandsoorten

  // Tekst die op de plek van het formulier komt als het formulier is verzonden en behandeld
  'thanks'                  => lang('contact_send_text'),

  // Output routing van dit formulier (zie bij de example.php module voor uitleg)
  '__return'                => ''
  
);


/*
|--------------------------------------------------------------------------
| Instellingen voor het formulier 'reservation'
|--------------------------------------------------------------------------
*/

$config['reservation'] = array(
  
  // De velden worden voor dit formulier uit een tabel gegenereerd en in dezelfde tabel toegevoegd
  'table'             => 'tbl_example',
  
  'title'             => 'Voorbeeld reservering',                
  'buttons'           => array( 'submit'=>array('submit'=>'submit', 'value'=>lang('submit')) ),
  'validation_place'  => 'Niet alle noodzakelijke velden zijn (goed) ingevuld.',
  'check_for_spam'    => true,
  'formaction'        => array('formaction_database','formaction_mail'),
  '__return'          => ''
);


/*
|--------------------------------------------------------------------------
| Voorbeeld waarbij velden van een model komen
|--------------------------------------------------------------------------
*/

$config['shop'] = array(
  
  // De velden worden nu opgehaald uit een model.method, deze geeft een array van formfields terug
  'model'             => 'shop.fields',
  
  'title'             => 'Voorbeeld reservering',                
  'buttons'           => array( 'submit'=>array('submit'=>'submit', 'value'=>lang('submit')) ),
  'validation_place'  => 'field',
  'check_for_spam'    => true,
  'formaction'        => array('formaction_database','formaction_mail'),
  // In plaats van een 'thank you' tekst na het invullen, kan ook naar een model.method worden verwezen
  'thanks_model'      => 'payment',
  '__return'          => ''
);



/*
|--------------------------------------------------------------------------
| Instellingen voor een FlexyForm
|--------------------------------------------------------------------------
*/

$config['flexyform_contact'] = array(
  'validation_place'  => 'field',
  'check_for_spam'    => true,
  'formaction'        => 'formaction_mail',
  '__return'          => ''
);
