// UNIT TEST FOR flexy-grid.js

describe('flexy-grid', function(){
  
  // FlexyAdmin app laden
  beforeEach(module('flexyAdmin'));
  
  var gridCtrl;
  
  // GridController laden
  beforeEach(inject(function($controller){
    gridCtrl = $controller('GridController');
  }));
  
  // construct test
  it('Heeft init vars', function() {
    // expect( gridCtrl ).toBeDefined();
  });
  
});