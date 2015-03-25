// run protractor protractor.conf.js

// Protractor configuration

exports.config = {

  // Address where Selenium is running
  seleniumAddress:'http://localhost:4444/wd/hub',
  
  // Our local server
  baseUrl: 'http://localhost/FlexyAdmin/FlexyAdminDEMO/admin/__test',
  
  capabilities: {
    'browserName': 'chrome'
  },
  
  // Testfiles
  specs: ['__test/tests/e2e_test*.js'],
  
  // Options
  jasmineNodeOpts: {
    showColors: true
  }
  
};
