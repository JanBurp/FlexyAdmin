var casper = require('casper').create();

casper.start('http://localhost/FlexyAdmin/FlexyAdminDEMO/', function() {
  this.echo(this.getTitle());
});

casper.thenOpen('http://localhost/FlexyAdmin/FlexyAdminDEMO/contact', function() {
    this.echo(this.getTitle());
});

casper.run();
