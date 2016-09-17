// run webdriver-manager start
// run protractor e2e_frontend.conf.js

var baseURL = 'http://localhost/FlexyAdmin/FlexyAdmin/';

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
  specs: ['__test/tests/e2e_frontend_test*.js'],
  
  // Options
  jasmineNodeOpts: {
    showColors: true
  },

  onPrepare: function() { }
  
};
