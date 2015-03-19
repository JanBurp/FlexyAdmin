// UNIT TEST FOR jdb.extend.js

describe('jdb.extend', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));
  
  it('JSON.serialize', function() {
    var json = { test:'test' };
    var serialize=jdb.serializeJSON(json);
  });

});