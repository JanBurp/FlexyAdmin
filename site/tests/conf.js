/**
 * Config for FrontEnd E2E testing, see test.js
 */
exports.config = {
  seleniumAddress: 'http://localhost:4444/wd/hub',


  // specs: ['test.js'],
  
  // Capabilities to be passed to the webdriver instance.
  capabilities: {
    'browserName': 'chrome'
  },

  // Spec patterns are relative to the location of the spec file. They may
  // include glob patterns.
  // suites: {
  //   homepage: 'tests/e2e/homepage/**/*Spec.js',
  //   search: ['tests/e2e/contact_search/**/*Spec.js',
  //     'tests/e2e/venue_search/**/*Spec.js']
  // },
  
  
  // Options to be passed to Jasmine-node.
  jasmineNodeOpts: {
    showColors: true,
  }
}