/**
 * E2E testing with CasperJS: http://casperjs.org/
 * Run with gulp: gulp test
 */

var url='http://localhost/FlexyAdmin/FlexyAdminDEMO';

/**
 * Start casper
 */
casper.start( url, function() {
  this.echo(this.getTitle());
});


/**
 * De testen
 */
casper.test.begin( 'FlexyAdmin', 1, function(test) {
  test.assert(true);
  
  // Hier komen de testen
  

});


/**
 * Afronding
 */
casper.run(function(){
  phantom.exit();
});