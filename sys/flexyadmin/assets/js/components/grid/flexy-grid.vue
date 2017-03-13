<script>
import draggable        from 'vuedraggable'

import jdb              from '../../jdb-tools.js'
import flexyState       from '../../flexy-state.js'
import flexyButton      from '../flexy-button.vue'

import FlexyPagination  from '../flexy-pagination.vue'
import FlexyGridCell    from './flexy-grid-cell.vue'
// import FlexyForm        from './../form/flexy-form.vue' // https://vuejs.org/v2/guide/components.html#Circular-References-Between-Components

export default {
  name: 'FlexyGrid',
  components: {draggable,flexyButton,FlexyGridCell,FlexyPagination },
  props:{
    'title':String,
    'name':String,
    'api':{
      type: [String,Boolean],
      default:false,
    },
    'order': {
      type   :String,
      default:''
    },
    'offset': {
      type   :String,
      default:'0'
    },
    'limit': {
      type   :String,
      default:''
    },
    'filter':{
      type   :[String,Object],
      default:'',
    },
    'type':{
      type:String,
      default:'table',
    },
    'selection':{
      type:[Array,Boolean],
      default:false,
    }
  },
  
  // https://vuejs.org/v2/guide/components.html#Circular-References-Between-Components
  beforeCreate: function () {
    this.$options.components.FlexyForm = require('./../form/flexy-form.vue');
  },
  
  
  mounted : function() {

    var self = this;
    self.calcLimit();
    
    // Bij resize
    window.addEventListener('resize', function(event){
      if (!self.isResizing) {
        self.isResizing = true;
        if (self.calcLimit()) self.reloadPageAfterResize();
      }
    });

    //
    // Load first page
    // 
    if (this.type!=='mediapicker') this.apiParts.formID = jdb.getUrlQueryPart('form');
    if ( !this.apiParts.formID ) {
      this.apiParts.formID = false;
      this.reloadPage({
        offset : this.offset,
        limit  : this.apiParts.limit,
        order  : this.order,
        filter : this.filter,
      });
    }
    
    //
    // Init Find
    //
    this.extendedFind = false;
    if (this.filter.substr(0,1)==='[' || this.filter.substr(0,1)==='{') {
      this.extendedFind = true;
      this.extendedTerm = JSON.parse(this.filter);
      this.filterTerm = '';
    }
    else {
      this.extendedTerm = [_.clone(this.extendedTermDefault)];
    }
  },
  
  beforeUpdate : function() {
    //
    // Selection
    //
    if (this.selection) {
      // Pas selected aan
      // console.log('flexy-grid.update',this.selection);
      var selected = [];
      for (var i = 0; i < this.selection.length; i++) {
        var src = this.selection[i];
        var key_item = false;
        for (var j = 0; j < this.items.length; j++) {
          var item = this.items[j];
          if (item['media_thumb'].value === src) {
            key_item = item['id'].value;
          }
        }
        if (key_item) selected.push(key_item);
      }
      
      // console.log( _.isEqual(selected,this.selected), selected, this.selected );
      if ( !_.isEqual(selected,this.selected) ) {
        this.selected = _.clone(selected);
      }
      // jdb.vueLog(this.items);
      
    }
  },


  data : function() {
    return {
      items             : [],
      fields            : [],
      actions           : [],
      searchable_fields : [],
      dataInfo          : {},
      selected          : [],
      mediaSelection    : this.selection, 
      apiParts    : {
        order         : this.order,
        filter        : this.filter,
        offset        : 0,
        limit         : this.limit,
        txt_abstract  : true,
        as_grid       : true,
        formID        : false,
      },
      changeUrlApi: true,
      focus       : {id:false,cell:false},
      draggable   : {
        item        : false,
        orderStart  : 0,
        oldItems    : false,
        children    : false,
        newPage     : false,
        oldIndex    : 0,
        newIndex    : 0,
      },
      findTerm            : this.filter,
      extendedFind        : true,
      extendedTermDefault : { field:'',term:'',and:'OR',equals:'exist' },
      extendedTerm        : [],
      oldExtendedTerm     : [],
      uploadFiles         : [],
      dropUploadHover     : false,
    }
  },

  
  computed:{
    
    dataName : function() {
      var name = this.name;
      if (this.gridType()==='media') {
        name = 'media_'+name;
      }
      return name;
    },
    
    /**
     * Options for draggable
     */
    draggableOptions : function() {
      return {
        draggable     : 'tr',
        handle        : '.draggable-handle',
        forceFallback : true,
      }
    },
    
  },
  
  methods:{
    
    calcLimit : function( view ) {
      // Hoeft niet als een form wordt getoond.
      if (this.apiParts.formID) return false;
      // En ook niet als er geen grid_header is
      var grid_header = document.querySelector('#content .card.grid>.card-header');
      if (_.isUndefined(grid_header) || grid_header==null) return false; 
      
      // Bepaal view
      if (_.isUndefined(view)) view = this.getMediaView();
      
      // Sizes:
      var rowHeight = 37;
      var padding = 8;
      var thumb = { width: 264, height: 292 }
      var small = { width: 136, height: 164 }
      var height = window.innerHeight - document.querySelector('#header').offsetHeight - grid_header.offsetHeight  - document.querySelector('#content .card.grid>.card-footer').offsetHeight - rowHeight - 2*padding;
      // Defaults:
      var max_items = 10;
      var rows = 1;

      // Calc new limit
      var new_limit = this.apiParts.limit;
      if (this.gridType()!=='media') view = 'list';
      switch (view) {

        case 'small':
          thumb = small;
          rows = 2;
        case 'thumbs':
          if (this.type!=='mediapicker') rows = Math.floor(height / thumb.height);
          var width = this.$el.offsetWidth - (2*padding);
          var columns = Math.floor(width / thumb.width);
          new_limit = (columns * rows) - 1;
          if (new_limit<=1) new_limit += columns;
          break;

        case 'list':
        default:
          new_limit = 10;
          if (this.type!=='mediapicker') {
            if (this.gridType()==='media') height -= rowHeight; // Extra rij eraf voor upload item
            max_items = height / rowHeight;
            var step = (max_items <= 10)?2:5;
            new_limit = Math.floor(max_items / step) * step;
          }
      }
      
      // Calc new offset
      var new_offset = this.apiParts.offset;
      if (this.apiParts.offset > 0) {
        this.apiParts.offset = Math.floor( this.apiParts.offset / this.apiParts.limit) * this.apiParts.limit
      }
      
      // Reload needed?
      var changed = (new_limit!==this.apiParts.limit || new_offset!==this.apiParts.offset);
      this.apiParts.limit = new_limit;
      this.apiParts.offset = new_offset;
      return changed;
    },
    
    reloadPageAfterResize() {
      this.reloadPage();
    },
    
    reloadPage : function(apiParts) {
      var self = this;
      flexyState.api({
        url       : self.apiUrl(apiParts),
      })
      .then(function(response){
        if (!_.isUndefined(response.data)) {
          if (response.data.success) {
            // Stel url in van browser
            self.newUrl();
            // Zijn er settings meegekomen?
            if ( !_.isUndefined(response.data.settings) ) {
              // Fields
              self.fields = response.data.settings.grid_set.field_info;
              self.searchable_fields = response.data.settings.grid_set.searchable_fields;
              // Actions?
              if (!_.isUndefined(response.data.settings.grid_set.actions)) {
                self.actions = response.data.settings.grid_set.actions;
              }
            }
            // Data en die aanvullen met data
            var data = response.data.data;
            self.items = self.addInfo( data, true );
            self.dataInfo = response.data.info;
          }
        }
        self.isResizing = false;
        return response;
      });
    },
    
    apiUrl : function(parts) {
      parts = _.extend( this.apiParts, parts );
      this.apiParts = parts;
      var url = this.api;
      if (this.gridType()==='media') {
        url += '?table=res_assets&path='+this.name;
      }
      else {
        url += '?table='+this.name + '&txt_abstract='+parts.txt_abstract + '&as_grid='+parts.as_grid;
      }
      url += '&offset='+parts.offset + '&limit='+parts.limit + '&order='+parts.order + '&filter={'+jdb.encodeURL(parts.filter)+'}';
      if (this.fields.length==0) {
        url += '&settings=grid_set';
      }
      return url;
    },
    
    newUrl : function(){
      if (this.changeUrlApi) {
        var parts = this.apiParts;
        var stateObj = parts;
        var url = location.pathname + '?options={"offset":"'+parts.offset+'","order":"'+parts.order+'","filter":"'+jdb.encodeURL(parts.filter)+'"}';
        if (this.apiParts.formID) url += '&form=' + this.apiParts.formID;
        history.pushState(stateObj, "", url);
      }
    },
    
    hasData : function() {
      return (this.items.length>0 || this.gridType()==='media');
    },
    
    /*
      Voeg (tree)info toe aan meegegeven items
    */
    addInfo : function(items,addSchema) {
      var data     = items;
      var isTree   = this.gridType()=='tree';
      if (isTree) {
        var parents    = {};
        var level      = 0;
        var parent_key = 0;
      }
      for (var i = 0; i < data.length; i++) {
        var row = data[i];
        var id = row['id'];
        // Add info to each cell
        if (addSchema===true) {
          for (var field in row) {
            // Defaults
            if ( _.isUndefined(this.fields[field]) ) {
              this.fields[field] = [];
              this.fields[field]['type']      = 'string';
              this.fields[field]['grid-type'] = 'text';
              this.fields[field]['readonly']  = false;
            }
            else {
              if ( _.isUndefined(this.fields[field]['type']) )      this.fields[field]['type'] = 'string';
              if ( _.isUndefined(this.fields[field]['grid-type']) ) this.fields[field]['grid-type'] = 'text';
              if ( _.isUndefined(this.fields[field]['readonly']) )  this.fields[field]['readonly'] = false;
            }
            data[i][field] = {
              'type'  : this.fields[field]['grid-type'] || this.fields[field]['form-type'],
              'value' : row[field]
            };
            if ( this.fields[field].type==='number' && this.fields[field]['form-type']==='select') {
              var jsonValue = JSON.parse(row[field].value);
              data[i][field] = {
                'type'  : this.fields[field]['grid-type'] || this.fields[field]['form-type'],
                'value' : Object.values(jsonValue)[0],
                'id'    : Object.keys(jsonValue)[0],
              };
            }
            data[i][field].name = field;
          }
        }
        // Add tree info to each row
        if (isTree) {
          row._info = {
            level         : 0,
            is_child      : false,
            has_children  : false,
          };
          parent_key   = row.self_parent.value;
          // if not on toplevel:
          if (parent_key>0) {
            row._info.is_child=true;
            // are we on a known level?
            if ( ! _.isUndefined(parents[parent_key]) ) {
              // yes: get that level
              level = parents[parent_key];
            }
            else {
              // no: remember new level
              level++;
              parents[parent_key] = level;
            }
          }
          else {
            // on root, so level = 0
            level=0;
          }
          // add level info
          row._info.level = level;
        }
        // Keep new row
        data[i] = row;
      }
      // Add more tree info (has_children)
      if (isTree && parents!=={}) {
        _.forEach(data,function(row,key){
          var id = row.id.value;
          var level = parents[id];
          if (level) {
            data[key]._info.has_children = true;
          }
        });
      }
      // Console
      if (isTree && flexyState.debug) {
        console.log('treeInfo:');
        _.forEach(data,function(row){ console.log('id:',row.id.value,'order:',row.order.value,'self_parent:',row.self_parent.value,'level:',row._info.level,'isChild:',row._info.is_child,'hasChildren:',row._info.has_children,'title:',row.str_title.value); });
      }
      return data;
    },
    
    gridType : function() {
      var type = this.type;
      if (type==='mediapicker') {
        type = 'media';
        this.changeUrlApi = false;
      }
      if (typeof(this.fields.order)!=='undefined' && (this.apiParts.order==='' || this.apiParts.order==='order') && this.apiParts.filter==='') type='ordered';
      if (typeof(this.fields.self_parent)!=='undefined' && (this.apiParts.order==='' || this.apiParts.order==='order') && this.apiParts.filter==='') type='tree';
      return type;
    },

    gridTypeClass : function() {
      var c = 'grid-type-'+this.gridType();
      if (this.gridType()==='media') {
        c += ' grid-media-view-'+this.getMediaView();
      }
      return c;
    },
    
    needsPagination : function(){
      return (typeof(this.dataInfo.num_pages)!=='undefined' &&  this.dataInfo.num_pages > 1);
    },
    
    isPrimaryHeader : function(field) {
      var headerType = field['grid-type'] || field['form-type'];
      return (headerType==='primary')
    },
    isNormalVisibleHeader : function(field) {
      var headerType = field['grid-type'] || field['form-type'];
      return headerType!=='hidden' && headerType!=='primary'
    },
    isSortableField : function(field) {
      return field.sortable;
    },

    isMediaThumbs : function() {
      return (this.gridType()==='media' && this.getMediaView()!=='list');
    },
    
    getMediaView : function() {
      return flexyState.getMediaView();
    },

    setMediaView : function(view) {
      this.calcLimit(view);
      this.reloadPageAfterResize();
      return flexyState.setMediaView(view);
    },

    headerClass : function(field) {
      var c = 'grid-header-type-'+field['form-type'];
      if (field['readonly']) c+=' grid-header-muted';
      return c;
    },
    
    hasSelection : function() {
      return this.selected.length>0;
    },
    
    isSelected : function(id) {
      return this.selected.indexOf(id) > -1;
    },
    
    select: function(id) {
      var index = this.selected.indexOf(id);
      if (index>-1) {
        this.selected.splice(index, 1);
        if (this.mediaSelection) {
          this.mediaSelection = _.uniq(this.mediaSelection);
          for (var i = 0; i < this.items.length; i++) {
            var item = this.items[i];
            if (item['id'].value === id) {
              var src = item['media_thumb'].value;
              if ( !_.isUndefined(src) ) {
                var key = this.mediaSelection.indexOf(src);
                this.mediaSelection.splice(key,1);
              }
            }
          }
        }
      }
      else {
        this.selected.push(id);
      }
      this.emitSelectedMedia();
    },
    
    reverseSelection:function() {
      var ids = [];
      for (var i = 0; i < this.items.length; i++) {
        ids.push(this.items[i].id.value);
      }
      this.selected = _.difference(ids,this.selected);
      this.emitSelectedMedia();
    },
    
    emitSelectedMedia : function() {
      if (this.type==='mediapicker') {
        var selectedMedia = this.mediaSelection;
        for (var i = 0; i < this.selected.length; i++) {
          for (var j = 0; j < this.items.length; j++) {
            var index = this.items[j]['id'].value;
            if (index===this.selected[i]) {
              var media = this.items[j]['media_thumb'].value;
              selectedMedia.push( media );
            }
          }
        }
        // console.log('emitSelectedMedia',selectedMedia);
        this.$emit('grid-selected',selectedMedia);
      }
    },
    
    newItem : function() {
      if (this.gridType()==='media') {
        var event = new MouseEvent('click', {
          'view': window,
          'bubbles': true,
          'cancelable': true
          });
        document.getElementById('browsefiles').dispatchEvent(event);
      }
      else {
        this.editItem(-1);
      }
    },
    
    editItem : function(id) {
      // var url = this.editUrl(id);
      // window.location.assign(url);
      this.apiParts.formID = id;
      this.newUrl();
    },
    
    updateItem : function(id,data) {
      this.apiParts.formID = false;
      this.items = [];
      this.reloadPage();
      // var itemIndex = jdb.indexOfProperty(this.items,'id',id);
      // console.log(itemIndex);
      // jdb.vueLog(this.items);
    },
    
    
    removeItems : function(removeIds) {
      var self = this;
      if ( _.isUndefined(removeIds)) {
        removeIds = this.selected;
      }
      else {
        removeIds = [removeIds];
      }
      // Only when there are items to remove
      if (removeIds.length>0) {
        // Confirm
        var message = this.$lang['confirm_delete_one'];
        if (removeIds.length>1) message = this.$options.filters.replace( this.$lang['confirm_delete_multiple'], removeIds.length );
        
        flexyState.openModal( {'title':'','body':message,'size':'modal-sm'}, function(event) {
          if ( event.state.type==='ok') {
            var data = {
              table : self.name,
              where : removeIds,
            };
            if (self.gridType()==='media') {
              data.table = 'res_assets';
              data.path = self.name;
            }
            return flexyState.api({
              url   : 'row',
              data  : data,
            }).then(function(response){
              var error = response.error || (response.data.data===false);
              if (error) {
                flexyState.addMessage( self.$lang.error_delete, 'danger');
              }
              else {
                flexyState.addMessage( self.$options.filters.replace( self.$lang.deleted, removeIds.length));
                self.reloadPage();
              }
              return response;
            });
          }
        });
        
      }
    },
    
    // Grid action
    startAction : function(url) {
      var self = this;
      flexyState.api({
        url       : url,
      })
      .then(function(response){
        var error = response.error;
        if (!error && response.data.data===false) error = true;
        if (error) {
          flexyState.addMessage( response.data.error, 'danger');
        }
        else {
          flexyState.addMessage( response.data.message, response.data.message_type || 'success' );
        }
        return response;
      });
    },
    
    // Row action
    action: function(action) {
      return flexyState.api({
        method: 'POST',
        url : action.uri,
      }).then(function(response){
        return response;
      });
    },
        
    rowLevel:function(row) {
      if (_.isUndefined(row._info)) return 0;
      return row._info.level;
    },
    
    isEditable : function(name) {
      var editable = false;
      if ( !_.isUndefined(this.fields[name]) ) editable = this.fields[name]['grid-edit'];
      return editable;
    },
    
    isReadonly : function(name) {
      var readonly = false;
      if ( !_.isUndefined(this.fields[name]) ) readonly = this.fields[name]['readonly'];
      return readonly;
    },
    
    editUrl : function(id) {
      var url = '';
      if (this.gridType()==='media') {
        url = 'admin/show/form/_media_/'+ this.name+'/'+id;
      }
      else {
        url = 'admin/show/form/'+this.name+'/'+id;
      }
      return url;
    },
    
    
    startFinding : function(event) {
      if (event) event.preventDefault();
      var self = this;
      var find = '';
      if ( !self.extendedFind ) {
        find = this.findTerm.replace(/'/g,'"');
      }
      else {
        var filled = true;
        for (var i = 0; i < this.extendedTerm.length; i++) {
          if (this.extendedTerm[i].field === '') filled = false;
          if (this.extendedTerm[i].term === '') filled = false;
        }
        if (this.extendedTerm.length<1) filled = false;
        if (!filled) return false;
        find = JSON.stringify(self.extendedTerm);
      }
      this.reloadPage({offset:0,filter:find});
    },
    
    findChanged : function() {
      if (this.findTerm==='') this.stopFind();
      this.oldExtendedTerm = [];
    },
    
    stopFind : function() {
      this.findTerm = '';
      this.extendedFind = false;
      this.extendedTerm = [_.clone(this.extendedTermDefault)];
      this.reloadPage({offset:0,filter:''});
    },
    
    toggleExtendedFind : function() {
      if (this.extendedFind) {
        this.extendedFind = false;
        this.findTerm = '';
        if (!_.isUndefined(this.extendedTerm[0])) {
          this.findTerm = this.extendedTerm[0].term;
          this.oldExtendedTerm = this.extendedTerm;
        }
      }
      else {
        this.extendedFind = true;
        if (this.oldExtendedTerm.length>0) {
          this.extendedTerm = this.oldExtendedTerm;
        }
        else {
          this.extendedTerm = [_.clone(this.extendedTermDefault)];
          this.extendedTerm[0].term = this.findTerm;
          this.extendedTerm[0].field = this.searchable_fields[0];
        }
      }
      this.startFinding();
    },
    
    extendedSearchAdd : function() {
      this.extendedTerm.push( _.clone(this.extendedTermDefault) );
    },
    extendedSearchRemove : function(index) {
      this.extendedTerm.splice(index,1);
      if (this.extendedTerm.length<1) this.extendedTerm = [_.clone(this.extendedTermDefault)];
    },
    
    dropUploadFiles : function(event) {
      event.stopPropagation();
      event.preventDefault();
      var files = event.target.files || event.dataTransfer.files;
      this._addUploadFiles(files);
    },
    addUploadFiles : function(event) {
      var files = event.target.files || event.dataTransfer.files;
      this._addUploadFiles(files);
    },
    removeUploadFile : function(index) {
      this.uploadFiles.splice(index,1);
    },
    _addUploadFiles : function(files) {
      for (var i = 0; i < files.length; i++) {
        this.uploadFiles.push( files.item(i) );
      }
      if (files.length>0) {
        this.startUpload();
      }
    },
    startUpload : function() {
      this.dropUploadHover = false;
      var self = this;
      for (var i = 0; i < self.uploadFiles.length; i++) {
        var file = self.uploadFiles[i];
        var formData = new FormData();
        formData.set( 'path', self.name );
        formData.set( 'file', self.uploadFiles[i] );
        formData.set( 'fileName', self.uploadFiles[i].name );
        flexyState.api({
          method    : 'POST',
          url       : 'media',
          data      : formData,
          formData  : true,
        }).then(function(response){
          var fileName = response.data.args.fileName;
          var error = response.data.error;
          if (!error && response.data.data===false) error = self.$lang.upload_error;
          if (error) {
            flexyState.addMessage(error + ' <b>`'+fileName+'`</b>','danger');
          }
          else {
            flexyState.addMessage(fileName + self.$lang.upload_ready);
          }
          // Uit de lijst halen
          var index = jdb.indexOfProperty(self.uploadFiles,'name',fileName);
          self.removeUploadFile(index);
          // Als alles uit de lijst is geuploade, reload
          if (self.uploadFiles.length === 0 ) {
            self.reloadPage();
          }
          return response;
        });
      }
      
    },

    /**
     * Dragging methods
     */
    isHiddenChild : function(id) {
      if (this.draggable.children===false) return false;
      return this.draggable.children.indexOf(id) >= 0;
    },
    isDragging : function(id) {
      return this.draggable.item == id;
    },
    draggable_onStart: function(event){
      var index = event.oldIndex;
      // Onthoud 'id' van draggable item
      this.draggable.item = event.item.dataset.id;
      // Onthoud 'order' van eerste item
      this.draggable.orderStart = this.items[0]['order'].value;
      // Onthoud kopie van huidige items
      this.draggable.oldItems = _.cloneDeep(this.items);
      // Als tree, onthoud dan de children als die er zijn
      this.draggable.children = false;
      if (this.gridType()==='tree' && this.items[index]._info.has_children) {
        this.draggable.children = [];
        var row = this.items[index]._info;
        var childIndex = index;
        var node_level=0;
        do {
          childIndex++;
          if ( !_.isUndefined(this.items[childIndex]) ) {
            node_level = this.items[childIndex]._info.level;
            if (node_level>row.level) this.draggable.children.push(this.items[childIndex].id.value);
          }
        } while (node_level>row.level && !_.isUndefined(this.items[childIndex]));
      }
    },
    draggable_onEnd  : function(event){
      var self = this;
      var oldIndex = event.oldIndex;
      var newIndex = event.newIndex;
      if (_.isUndefined(newIndex)) newIndex = oldIndex;

      if (oldIndex!==newIndex) {
        this.draggable.oldIndex = oldIndex;
        this.draggable.newIndex = newIndex;
        
        var items = _.cloneDeep(this.draggable.oldItems);
        var parent_id = 0; 
        if (this.gridType()==='tree') {
          var number_of_children = this.draggable.children.length || 0;
          // Pas parent van verplaatste item aan
          // Bijna altijd 0, behalve als het volgende item een hoger level heeft: dan heeft het dezelfde parent als dat item, dus als er een item na komt, neem die parent.
          // Check eerst of het niet de laatste is, want dan hoeven we al niet verder te kijken
          var plus = 0;
          if (newIndex>=oldIndex) plus=1;
          if ( newIndex+plus < self.items.length ) {
            if (!_.isUndefined(self.items[newIndex+plus])) {
              parent_id = self.items[newIndex+plus].self_parent.value;
            }
          }
          items[oldIndex].self_parent.value = parent_id;
          // Verplaats item & children
          this.items = jdb.moveMultipleArrayItems( items, oldIndex, number_of_children + 1, newIndex);
        }
        else {
          // Verplaats item
          this.items = jdb.moveMultipleArrayItems( items, oldIndex, 1, newIndex);
        }
        
        // Update 'order'
        var order = this.draggable.orderStart;
        for (var i = 0; i < this.items.length; i++) {
          this.items[i].order.value = order;
          order++;
        }
        // Vernieuw de tree info
        if (this.gridType()=='tree') this.items = this.addInfo(this.items);
        
        if (flexyState.debug) {
          console.log( 'draggable_onEnd ---------' );
          console.log(oldIndex,' => ',newIndex);
          self._log(self.items);
        }
        
        var newOrder = this.draggable.orderStart + this.items[ this.draggable.newIndex ].order.value;
        if (self.draggable.children && newIndex>oldIndex) newOrder = newOrder - self.draggable.children.length;
        self.postNewOrder( newOrder ).then(function(response){
          self.draggable.item = false;
        });
      }
      else {
        self.draggable.item = false;
      }
      
      // Laat children weer zien
      this.draggable.children = false;
    },
    
    postNewOrder : function(newOrder) {
      var self=this;
      var itemId = this.draggable.item;
      // var newOrder = this.draggable.orderStart + this.items[ this.draggable.newIndex ].order.value;
      return flexyState.api({
        method: 'POST',
        url : 'table_order',
        'data': {
          'table' : self.name,
          'id'    : itemId,
          'from'  : newOrder,
        },
      }).then(function(response){
        var error = response.error;
        if (!error && response.data.data===false) error = true;
        if (error) {
          flexyState.addMessage( self.$lang.grid_order_save_error, 'danger');
          // Terug naar oude situatie
          self.items = self.draggable.oldItems;
        }
        return response;
      });
    },
    
    
    _log : function( items ) {
      var self = this;
      _.forEach(items,function(row){
        if (self.gridType()==='tree') {
          console.log( row.id.value, row.order.value, 'tree:', row._info.level, row._info.is_child,row._info.has_children, row.str_title.value);
        }
        else {
          console.log( row.id.value, row.order.value, row.str_title.value);
        }
      });
    }
    
  }
}
</script>

<template>
  <div>
    
    <flexy-form v-if="apiParts.formID!==false" :title="title" :name="name" :primary="apiParts.formID" @formclose="updateItem(apiParts.formID,$event)"></flexy-form>
    
    <div v-if="apiParts.formID===false" class="card grid" :class="gridTypeClass()" @dragover.prevent  @drop="dropUploadFiles" @dragover="dropUploadHover=true" @dragenter="dropUploadHover=true" @dragleave="dropUploadHover=false" @dragend="dropUploadHover=false">
      <!-- MAIN HEADER -->
      <div class="card-header">
        <h1>{{title}}</h1>

        <!-- ACTIONS ?-->
        <div v-once v-if="actions.length>0" class="grid-actions">
          <div v-for="action in actions" class="grid-action">
            <flexy-button @click.native="startAction(action.url)" :icon="action.icon" :text="action.name" class="btn-default text-primary" :class="action.class" />
          </div>
        </div>
        
        <!-- FAST SEARCH -->
        <form class="form-inline" @submit="startFinding($event)">
          <div class="form-group" v-if="!extendedFind">
            <input type="text" v-model.trim="findTerm" class="form-control form-control-sm" id="grid-find" :placeholder="$lang.grid_fast_search" @input="findChanged()">
          </div>
          <div class="btn-group">
            <flexy-button @click.native.stop.prevent="startFinding($event)" icon="search" class="btn-default" />
            <flexy-button @click.native.stop.prevent="stopFind()" icon="refresh" class="btn-default text-danger" v-if="findTerm!=='' || extendedFind" />
            <flexy-button @click.native.stop.prevent="toggleExtendedFind()" :icon="{'chevron-down':!extendedFind,'chevron-up':extendedFind}" class="btn-default text-primary" />
          </div>
        </form>
      </div>

      <!-- EXTENDED SEARCH -->
      <div class="card-header grid-extended-find" v-if="extendedFind">
        <h4>{{$lang.grid_extended_search}}</h4>
        <form v-for="(term,index) in extendedTerm" class="form-inline" @submit.stop.prevent="startFinding($event)" :index="index">
          <div class="form-group grid-extended-search-and">
            <select class="form-control form-control-sm custom-select" name="grid-extended-search-and[]" v-model="term.and">
              <option value="OR" :selected="term.and=='OR'">{{$lang.grid_search_or}}</option>
              <option value="AND" :selected="term.and=='AND'">{{$lang.grid_search_and}}</option>
            </select>
          </div>
          <div class="form-group grid-extended-search-field">
            <select class="form-control form-control-sm custom-select" name="grid-extended-search-field[]" v-model="term.field">
              <option v-for="field in searchable_fields" :value="field" :selected="term.field===field">{{fields[field].name}}</option>
            </select>
          </div>
          <div class="form-group grid-extended-search-equals">
            <select class="form-control form-control-sm custom-select" name="grid-extended-search-equals[]" v-model="term.equals">
              <option value="exist" :selected="term.equals==='exist'">{{$lang.grid_search_exist}}</option>
              <option value="word" :selected="term.equals==='word'">{{$lang.grid_search_word}}</option>
              <option value="exact" :selected="term.equals==='exact'">{{$lang.grid_search_exact}}</option>
            </select>
          </div>
          <div class="form-group grid-extended-search-term">
            <input type="text" class="form-control form-control-sm" v-model="term.term" :placeholder="$lang.grid_search" name="grid-extended-search-term[]">
          </div>
          <flexy-button @click.native.stop.prevent="extendedSearchRemove(index)" icon="remove" class="btn-outline-danger" />
          <flexy-button @click.native.stop.prevent="extendedSearchAdd()" icon="plus" class="btn-outline-warning" />
        </form>
      </div>

      <!-- GRID HEADERS -->
      <div class="card-block table-responsive">
        <table class="table table-bordered table-sm grid-data">
          <thead>
            <tr>
              <template v-for="(field,key) in fields">
                
                <th v-if="isPrimaryHeader(field)" :class="headerClass(field)" class="text-primary grid-actions">
                  <flexy-button v-if="gridType()!=='media'" @click.native="newItem()" icon="plus" class="btn-outline-warning" />
                  <flexy-button v-if="type!=='mediapicker'" @click.native="removeItems()" icon="remove" :class="{disabled:!hasSelection()}" class="btn-outline-danger" />
                  <flexy-button @click.native="reverseSelection()" icon="check-square-o" class="btn-outline-info" />

                  <div v-if="isMediaThumbs()" class="dropdown" id="dropdown-sort">
                    <flexy-button icon="sort-amount-asc" class="btn-outline-info" dropdown="dropdown-sort"/>
                    <div class="dropdown-menu">
                      <a v-for="(field,key) in fields" v-if="isSortableField(field)" @click="reloadPage({'order':(key==apiParts.order?'_'+key:key)})" class="dropdown-item" :class="{'selected':(apiParts.order.indexOf(key)>=0)}">
                        <span v-if="apiParts.order==key" class="fa fa-caret-up"></span>
                        <span v-if="apiParts.order=='_'+key" class="fa fa-caret-down"></span>
                        {{field.name}}
                      </a>
                    </div>
                  </div>
                </th>
                
                <th v-if="isNormalVisibleHeader(field)" :class="headerClass(field)"  class="text-primary">
                  <a @click="reloadPage({'order':(key==apiParts.order?'_'+key:key)})"><span>{{field.name}}</span>
                    <span v-if="apiParts.order==key" class="fa fa-caret-up"></span>
                    <span v-if="apiParts.order=='_'+key" class="fa fa-caret-down"></span>
                  </a>
                </th>
                
              </template>
            </tr>
          </thead>
        
          <!-- GRID BODY -->
          <draggable v-if="hasData()" :list="items" element="tbody" :options="draggableOptions" @start="draggable_onStart" @end="draggable_onEnd">

            <!-- UPLOAD ROW -->
            <tr v-if="gridType()==='media'" class="grid-upload" :class="{'dropping':dropUploadHover}">
              <td colspan="100" class="grid-upload-dropbox">
                <flexy-button @click.native="newItem()" icon="plus" class="btn-outline-warning" />
                <span :class="{'show':uploadFiles.length>0}" class="upload-spinner fa fa-spinner fa-pulse fa-fw"></span>
                {{$lang.upload_choose}}
                <input id="browsefiles" @change="addUploadFiles"  type="file" name="files[]" multiple="multiple">
              </td>
            </tr>
          
            <!-- ROW -->
            <tr v-for="row in items" :data-id="row.id.value" :class="{'table-warning is-selected':isSelected(row.id.value)}" v-show="!isHiddenChild(row.id.value)" :level="rowLevel(row)" :key="row.id.value">
              <template v-for="cell in row">
              
                <!-- PRIMARY CELL -->
                <td v-if="cell.type=='primary'" class="action">
                  <flexy-button v-if="gridType()!=='media'" @click.native="editItem(cell.value)" icon="pencil" class="btn-outline-warning action-edit" />
                  <flexy-button v-if="type!=='mediapicker'" @click.native="removeItems(row.id.value)" icon="remove" class="btn-outline-danger action-remove" />
                  <flexy-button @click.native="select(row.id.value)" :icon="{'square-o':!isSelected(row.id.value),'check-square-o':isSelected(row.id.value)}" class="btn-outline-info action-select" />
                  <flexy-button v-if="gridType()==='tree' || gridType()==='ordered'" icon="arrows-v" class="draggable-handle btn-outline-info action-drag" :class="{'active':isDragging(row.id.value)}" />
                </td>
              
                <!-- ACTION CELL -->
                <td v-else-if="cell.type=='action'" class="action">
                  <flexy-button :icon="cell.value.icon" class="btn-outline-warning" :text="cell.value.text" @click.native="action(cell.value)"/>
                </td>
              
                <!-- CELL -->
                <flexy-grid-cell v-else
                  @select="select(row.id.value)"
                  :focus="false"
                  :type="cell.type"
                  :name="cell.name"
                  :value="cell.value"
                  :level="rowLevel(row)"
                  :primary="{ table:dataName, id:row.id.value }"
                  :editable="isEditable(cell.name)"
                  :readonly="isReadonly(cell.name)"
                  :options="fields[cell.name]">
                </flexy-grid-cell>
              
              </template>
            </tr>
          </draggable>
        </table>
      </div>
      <!-- FOOTER -->
      <div class="card-footer text-muted">
        <div class="btn-group actions" v-if="gridType() === 'media'">
          <flexy-button @click.native="setMediaView('list')"   icon="bars"       :class="{'btn-outline-primary':getMediaView()!=='list','btn-primary':getMediaView()==='list'}" border="true" />
          <flexy-button @click.native="setMediaView('small')"  icon="th"         :class="{'btn-outline-primary':getMediaView()!=='small','btn-primary':getMediaView()==='small'}" border="true" />
          <flexy-button @click.native="setMediaView('thumbs')" icon="picture-o"  :class="{'btn-outline-primary':getMediaView()!=='thumbs','btn-primary':getMediaView()==='thumbs'}" border="true" />
        </div>
        <flexy-pagination v-if="needsPagination()" :total="dataInfo.total_rows" :maxtotal="dataInfo.count_all" :pages="dataInfo.num_pages" :current="dataInfo.page + 1" :limit="dataInfo.limit" @newpage="reloadPage({offset:$event})"></flexy-pagination>
        <div v-if="!needsPagination()" class="pagination-container">
          <span class="pagination-info text-primary">{{$lang.grid_total | replace(dataInfo.total_rows)}}</span>
        </div>
      </div>
    
    </div>
    
  </div>

</template>
