/**
 * FlexyAdmin (c) Jan den Besten
 * www.flexyadmin.com
 * 
 * @author: Jan den Besten
 * @copyright: Jan den Besten
 * @license: n/a
 * 
 * $Author$
 * $Date$
 * $Revision$
 */


/**
 * flexy-upload
 * 
 * TEST voor _api/media .. UPLOAD
 */


flexyAdmin.controller('flexyMediaCtrl', ['flexySettingsService','flexyApiService','FileUploader','cfpLoadingBar','flexyTableService','$routeParams','$scope','$route', function(settings,api,FileUploader,cfpLoadingBar,tableService,$routeParams,$scope,$route) {

  $scope.path = $routeParams.path;
  $scope.files = [];
  $scope.countFiles = $scope.files.length;
  
  /**
   * Remove file (api)
   */
  $scope.removeFile = function(path,file) {
    api.post('media',{path:path,where:file}).then(function(response){
      if (response.data) {
        loadFiles();
      }
    });
  };
  
  /**
   * Load files (api)
   */
  // function loadFiles() {
  //   api.get('media',{path:$scope.path}).then(function(response){
  //     $scope.files = response.data;
  //     $scope.countFiles = $scope.files.length;
  //   });
  // }
  
  /**
   * Uploader
   */
  var uploaderConfig = {
    url               : settings.item('api_base_url') + 'media',
    formData          : [{path:$scope.path,format:'json'}],
    autoUpload        : true,
    // removeAfterUpload : true,
    headers           : { "X-Requested-With" : 'XMLHttpRequest' },  // Als een AJAX Request
  };
  var uploader = $scope.uploader = new FileUploader(uploaderConfig);

  /**
   * Starts a file upload
   */
  uploader.onBeforeUploadItem = function(fileItem, response, status, headers) {
    cfpLoadingBar.start();
  };
  /**
   * Upload progress
   */
  uploader.onProgressItem = function(fileItem, progress) {
    cfpLoadingBar.set(progress);
  };
  /**
   * Upload file complete
   */
  uploader.onCompleteItem = function(fileItem, response, status, headers) {
    cfpLoadingBar.complete();
    // TRACE / ERROR logging: See http-interceptor-logging.js  -> message()
    if (typeof(response)=='string' || angular.isDefined(response.trace)) {
      console.log(response);
      
      var trace = response;
      if (angular.isDefined(response.trace)) {
        trace = response.trace;
      }
      angular.element(document.querySelector('#debug')).removeClass('hidden');
      trace=trace.replace(/TRACE\s((.|\n)*?)ENDTRACE/gm, "<pre>TRACE $1</pre>");
      angular.element(document.querySelector('#debug .panel-content')).html(trace);
      fileItem.isSuccess = false;
      fileItem.isError   = true;
      fileItem.error     = fileItem.file.name;
    }
    else {
      // Upload Error
      if (!response.success) {
        fileItem.isSuccess = false;
        fileItem.isError = true;
        fileItem.error = response.error;
      }
      else {
        // Ok, remove from que
        fileItem.remove();
      }
    }
  };
  /**
   * Upload complete
   */
  uploader.onCompleteAll  = function() {
    // Reload the files
    tableService.remove('_media_');
    $route.reload();
  };
  
}]);

