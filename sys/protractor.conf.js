// run webdriver-manager start
// run protractor protractor.conf.js

var baseURL = 'http://localhost/FlexyAdmin/FlexyAdminDEMO/admin/__test/';
var user    = {
  username : 'admin',
  password : 'admin',
}

// Protractor configuration

exports.config = {

  // Address where Selenium is running
  seleniumAddress:'http://localhost:4444/wd/hub',
  
  // Our local server
  baseUrl: baseURL,
  
  capabilities: {
    'browserName': 'chrome'
  },
  
  // Testfiles
  specs: ['__test/tests/e2e_test*.js'],
  
  // Options
  jasmineNodeOpts: {
    showColors: true
  },

  onPrepare: function() {
  }
  
};
