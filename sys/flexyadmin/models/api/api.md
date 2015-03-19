_api/auth
=========

Authentication API

- _api/auth/check              - gives as a result if a user is logged in, if so, returns userdata
- _api/auth/login              - needs username/password
- _api/auth/logout             - needs username/password
- _api/auth/send_new_password  - needs email
 *
@package default
@author Jan den Besten
 
---------------------------------------

_api/get_admin_nav
==================

Geeft het admin menu terug
 *
@package default
@author Jan den Besten
 
---------------------------------------

_api/get_help
=============

Geeft help pagina
 *
@package default
@author Jan den Besten
 
---------------------------------------

_api/get_plugin
===============

Geeft plugin pagina
 *
@package default
@author Jan den Besten
 
---------------------------------------

_api/media
==========

GET / UPLOAD media files

GET files
- GET => array( 'path'=> ... [, limit=0, offset=0 ]  )

 // * UPLOAD file
 // * - POST => array( 'path'=> ...  .... )

UPDATE file
- POST => array( 'path'=> ... , 'where' => ... , 'data' => array(....)  )

DELETE file
- POST => array( 'path'=> ... , 'where' => ...  )

 *
@package default
@author Jan den Besten
 
---------------------------------------

_api/row
========

GET / INSERT / UPDATE / DELETE row from a table from the database

GET ROW
- GET => array( 'table'=> ... , 'where' => ....)

INSERT ROW
- POST => array( 'table'=> ... , 'data' => array(....)  )

UPDATE ROW
- POST => array( 'table'=> ... , 'where' => ... , 'data' => array(....)  )

DELETE ROW
- POST => array( 'table'=> ... , 'where' => ...  )

 *
@package default
@author Jan den Besten
 
---------------------------------------

_api/table
==========

Returns a table from the database (if user has rights)

Arguments:
- table
- [limit=0]
- [offset=0]
- [txt_as_abstract=false]  - if set to TRUE, txt_ fields will contain an abstract without HTML tags
 *
@package default
@author Jan den Besten
 
---------------------------------------

