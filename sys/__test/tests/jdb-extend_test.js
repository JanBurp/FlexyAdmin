// UNIT TEST FOR jdb.extend.js

describe('jdb.extend', function(){
  'use strict';
  
  beforeEach(module('flexyAdmin'));

  it('JSON.serialize', function() {
    var json = { test:'test' };
    var serialize=jdb.serializeJSON(json);
  });
  

  it('indexOfProperty', function() {
    var test = [ {'id':2,'uri':'een'}, {'id':1,'uri':'twee'}, {'id':3,'uri':'drie'} ];
    expect(  jdb.indexOfProperty(test,'id',2)  ).toEqual(0);
    expect(  jdb.indexOfProperty(test,'id',1)  ).toEqual(1);
    expect(  jdb.indexOfProperty(test,'uri','twee')  ).toEqual(1);
    expect(  jdb.indexOfProperty(test,'uri','een')  ).toEqual(0);
  });
  

  it('moveMultipleArrayItems', function() {
    var source = [0,1,2,3,4,5];
    var result = [];

    // 1 item van 2 naar 3
    result = jdb.moveMultipleArrayItems(source, 2, 1, 3);
    expect( result ).toEqual( [0,1,3,2,4,5] );
    expect( source ).toEqual( [0,1,2,3,4,5] );

    // 1 item van 3 naar 1
    result = jdb.moveMultipleArrayItems(source, 3, 1, 1);
    expect( result ).toEqual( [0,3,1,2,4,5] );
    expect( source ).toEqual( [0,1,2,3,4,5] );

    // 2 items van 2 naar 3
    result = jdb.moveMultipleArrayItems(source, 2, 2, 3);
    expect( result ).toEqual( [0,1,4,2,3,5] );
    expect( source ).toEqual( [0,1,2,3,4,5] );

    // 2 items van 2 naar 4
    result = jdb.moveMultipleArrayItems(source, 2, 2, 4);
    expect( result ).toEqual( [0,1,4,5,2,3] );
    expect( source ).toEqual( [0,1,2,3,4,5] );

    // 3 items van 0 naar 3
    result = jdb.moveMultipleArrayItems(source, 0, 3, 3);
    expect( result ).toEqual( [3,4,5,0,1,2] );
    expect( source ).toEqual( [0,1,2,3,4,5] );

    // 2 items van 3 naar 1
    result = jdb.moveMultipleArrayItems(source, 3, 2, 1);
    expect( result ).toEqual( [0,3,4,1,2,5] );
    expect( source ).toEqual( [0,1,2,3,4,5] );
    
    // 3 items van 3 naar 0
    result = jdb.moveMultipleArrayItems(source, 3, 3, 0);
    expect( result ).toEqual( [3,4,5,0,1,2] );
    expect( source ).toEqual( [0,1,2,3,4,5] );
    
    // Foute invoer...


  });



  
  

});