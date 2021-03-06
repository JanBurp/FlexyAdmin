<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



/**
 * Pas hier css classes aan voor de meldingen van de formulieren
 */

$config['_classes'] = array(
  'thanks'  => 'message',
  'error'   => 'error'
);



/*
 *--------------------------------------------------------------------------
 * Alle formulieren worden ingesteld met een eigen config array.
 * De key is de naam van het formulier.
 * 
 * Hieronder de instellingen voor het formulier 'contact'
 *--------------------------------------------------------------------------
 */


$config['contact'] = array(

  // Class dat meegegeven wordt aan de form tag
  'class'                   => 'corners',
  
  // Titel van het formulier
  'title'                   => lang('contact_title'),
  
  // Velden van het formulier. De labels kunnen een verwijzing naar de taalbestanden bevatten.
  'fields'                  => array(
                                'str_name'		  => array( 'label'=>lang('field__str_name'),                         'validation'  => 'required' ),
                                'email_email'	  => array( 'label'=>lang('field__email_email'),                      'validation'	=> 'required|valid_email' ),
                                'str_telefoon'  => array( 'label'=>lang('field__str_telefoon'),                     'validation'  => 'valid_regex[telefoon]' ),
                                'txt_text'	    => array( 'label'=>lang('field__txt_text'),     'type'=>'textarea', 'validation'  => 'required' )
                              ),
                            
  // Voegt placeholders toe aan de velden, deze zijn hetzelfde als de labels
  'placeholders_as_labels'  => true,

  // Geef aan waar de validation errors komen: 'form' of 'field' of een algemene tekst.
  'validation_place'        => 'field',
                  
  // Controleer op spam (Voegt een extra (hidden) veld toe om op spamrobots te testen (veld heeft class='hidden', dit moet je in je stylesheet ook daadwerkelijk onzichtbaar maken))
  'check_for_spam'          => true, // array('str_name','txt_text'),
  
  // Voorkom dat het formulier meerdere keren kan worden verzonden door een pagina refresh.
  // NB deze optie maakt gebruik van sessions (de session library wordt automatisch geladen).
  'prevend_double_submit'   => true,
  // 'always_show_form'        => true,
  
  // Knoppen van het formulier                
  'buttons'                 => array( 'submit'=>array('type'=>'submit','value'=>lang('submit')) ),
  
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
 *--------------------------------------------------------------------------
 * DEMO: upload demo
 *--------------------------------------------------------------------------
 */

$config['upload_demo'] = array(
  'title'             => 'Upload DEMO',
  'fields'            => array( 'file_upload'	=> array( 'type'=>'file') ),
  'buttons'           => array( 'submit'=>array('submit'=>'submit', 'value'=>lang('submit')) ),
  'validation_place'  => 'field',
  'check_for_spam'    => true,
  'formaction'        => array('formaction_upload'),
  'upload_path'       => 'downloads',
  // 'allowed_types'     => 'gif|jpg|png|doc|docx|xls|xlsx|pdf|zip',
  // 'encrypt_name'      => TRUE,
  'thanks'            => 'Uploaden is gelukt!',
  '__return'          => ''
);


/*
 *--------------------------------------------------------------------------
 * DEMO, niet werkend, puur een voorbeeld waarbij velden van een model komen
 *--------------------------------------------------------------------------
 */
$config['shop'] = array(
  // De velden worden nu opgehaald uit een model.method, deze geeft een array van formfields terug
  'model'             => 'shop.fields',
  // Een model.method die de velden een beginwaarde kan meegeven
  'populate_fields'   => 'shop.populate',
  'buttons'           => array( 'submit'=>array('submit'=>'submit', 'value'=>lang('submit')) ),
  'validation_place'  => 'field',
  'check_for_spam'    => true,
  'formaction'        => array('formaction_database','formaction_mail'),
  // In plaats van een 'thank you' tekst na het invullen, kan ook naar een model.method worden verwezen
  'thanks_model'      => 'payment',
  '__return'          => ''
);

/*
 *--------------------------------------------------------------------------
 * Instellingen voor een FlexyForm (flexy_forms.sql moet geinstalleerd zijn)
 *--------------------------------------------------------------------------
 */
$config['flexyform_contact'] = array(
  'class'             => 'corners',
  'validation_place'  => 'field',
  'check_for_spam'    => true,
  'formaction'        => 'formaction_mail',
  '__return'          => ''
);
