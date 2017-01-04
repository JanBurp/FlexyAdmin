<script>
import draggable        from 'vuedraggable'

import jdb              from '../../jdb-tools.js'
import flexyState       from '../../flexy-state.js'
import flexyButton      from '../flexy-button.vue'

import FlexyPagination  from '../flexy-pagination.vue'
import FlexyGridCell    from './flexy-grid-cell.vue'

export default {
  name: 'FlexyGrid',
  components: {draggable,flexyButton,FlexyGridCell,FlexyPagination },
  props:{
    'title':String,
    'name':String,
    'fields':[Object,Array],
    'data':{
      type: [Array,Boolean],
      default:false
    },
    'order': {
      type   :String,
      default:''
    },
    'find':{
      type   :[String,Object],
      default:'',
    },
    'info':Object,
    'type':{
      type:String,
      default:'table',
    },
  },
  
 /**
  * Maak items klaar voor tonen in het grid:
  * - Voeg informatie van een veld toe aan elke cell
  * - Bij een tree: voeg informatie aan elke row toe: {level:(int),is_child:(bool),has_children:(bool)}
  */
  created : function() {
    this.items = this.addInfo( this.data, true );
    // window.addEventListener('keyup', this.key);
  },
  
  data : function() {
    return {
      items       : [],
      selected    : [],
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
      findTerm            : this.find,
      extendedFind        : true,
      extendedTermDefault : { field:'',term:'',and:'OR',equals:'exist' },
      extendedTerm        : [],
      uploadFiles         : [],
      uploadProgress      : {},
      uploadStatus        : {},
    }
  },
  
  mounted : function() {
    this.extendedFind = false;
    if (this.find.substr(0,1)==='[' || this.find.substr(0,1)==='{') {
      this.extendedFind = true;
      this.extendedTerm = JSON.parse(this.find);
      this.findTerm = '';
    }
    else {
      this.extendedTerm = [_.clone(this.extendedTermDefault)];
    }
  },
  
  computed:{
    
    dataName : function() {
      var name = this.name;
      if (this.gridType==='media') {
        name = 'media_'+name;
      }
      return name;
    },
    
    /**
     * Bepaal het type grid: table, ordered of tree
     */
    gridType : function() {
      var type=this.type;
      if (typeof(this.fields.order)!=='undefined' && (this.order==='' || this.order==='order') && this.find==='') type='ordered';
      if (typeof(this.fields.self_parent)!=='undefined' && (this.order==='' || this.order==='order') && this.find==='') type='tree';
      return type;
    },
    /**
     * Geeft class van type grid
     */
    gridTypeClass : function() {
      var c = 'grid-type-'+this.gridType;
      if (this.gridType==='media') {
        c += ' grid-media-view-'+this.getMediaView();
      }
      return c;
    },
    /**
     * Test if grid needs pagination
     */
    needsPagination : function(){
      return (typeof(this.info.num_pages)!=='undefined' &&  this.info.num_pages > 1);
    },
    /**
     * Options for draggable
     */
    draggableOptions : function() {
      return {
        // group         : { name:'tree', pull:true},
        draggable     : 'tr',
        handle        : '.draggable-handle',
        forceFallback : true,
        // scroll        : true,
        // scrollFn      : function(offsetX, offsetY, originalEvent) {
        //   console.log(offsetY);
        //
        // },
      }
    },
    
  },
  
  methods:{
    
    /*
      Voeg (tree)info toe aan meegegeven items
    */
    addInfo : function(items,addSchema) {
      var data     = items;
      var isTree   = this.gridType=='tree';
      if (isTree) {
        var parents    = {};
        var level      = 0;
        var parent_key = 0;
      }
      for (var i = 0; i < data.length; i++) {
        var row = data[i];
        var id = row['id'];
        // Add schema to each cell
        if (addSchema===true) {
          for (var field in row) {
            var schema = {
              'type'      : 'string',
              'grid-type' : 'text',
              'readonly'  : false,
            };
            if ( this.fields[field] ) schema = this.fields[field].schema;
            data[i][field] = {
              'type'  : schema['grid-type'] || schema['form-type'],
              'value' : row[field]
            };
            if ( schema.type==='number' && schema['form-type']==='select') {
              var jsonValue = JSON.parse(row[field].value);
              data[i][field] = {
                'type'  : schema['grid-type'] || schema['form-type'],
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
        _.forEach(data,function(row){ console.log('id:',row.id.value,'order:',row.order.value,'level:',row._info.level,'isChild:',row._info.is_child,'hasChildren:',row._info.has_children,'title:',row.str_title.value); });
      }
      return data;
    },
    
    isPrimaryHeader : function(field) {
      var headerType = field.schema['grid-type'] || field.schema['form-type'];
      return headerType==='primary'
    },
    isNormalVisibleHeader : function(field) {
      var headerType = field.schema['grid-type'] || field.schema['form-type'];
      return headerType!=='hidden' && headerType!=='primary'
    },

    isMediaThumbs : function() {
      return (this.gridType==='media' && this.getMediaView()==='thumbs');
    },
    
    getMediaView : function() {
      return flexyState.getMediaView();
    },

    setMediaView : function(view) {
      return flexyState.setMediaView(view);
    },

    
    headerClass : function(field) {
      return 'grid-header-type-'+field.schema['form-type'];
    },
    
    // setFocus : function(id,cell) {
    //   this.focus = {id:id,cell:cell};
    // },
    
    // setFocusNext : function() {
    //   if (this.focus.id===false) {
    //     this.focus.id = this.items[Object.keys(this.items)[0]]['id'].value;
    //   }
    //   if (this.focus.cell===false) {
    //     console.log(Object.keys(this.fields)[4]);
    //     this.focus.cell = Object.keys(this.fields)[4];
    //   }
    //   console.log('setFocusNext',this.focus.id,this.focus.cell);
    // },
    
    // key : function(event) {
    //   console.log('key',event);
    //   switch (event.key) {
    //     case 'Tab':
    //     case 'ArrowRight':
    //       this.setFocusNext();
    //       break;
    //     case 'ArrowLeft':
    //       this.setFocusPrev();
    //       break;
    //   }
    // },
    
    // hasFocus : function(id,cell) {
    //   var hasFocus = true;
    //   if (this.focus.id !== id) hasFocus = false;
    //   if (this.focus.cell !== cell) hasFocus = false;
    //   return hasFocus;
    // },
    
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
      }
      else {
        this.selected.push(id);
      }
    },
    
    reverseSelection:function() {
      var ids = [];
      for (var i = 0; i < this.items.length; i++) {
        ids.push(this.items[i].id.value);
      }
      this.selected = _.difference(ids,this.selected);
    },
    
    newItem : function() {
      if (this.gridType==='media') {
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
      var url = this.editUrl(id);
      window.location.assign(url);
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
        if (window.confirm(message)) {
          var data = {
            table : self.name,
            where : removeIds,
          };
          if (self.gridType==='media') data.table = 'res_assets';
          return flexyState.api({
            url   : 'row',
            data  : data,
          }).then(function(response){
            var error = response.error || (response.data.data===false);
            if (error) {
              flexyState.addMessage( self.$lang.error_delete, 'danger');
            }
            else {
              flexyState.addMessage( self.$options.filters.replace( self.$lang.deleted, removeIds.length), 'danger');
              self.reloadPage();
            }
            return response;
          });
        }
      }
    },
    
    reloadPage : function() {
      location.reload();
    },
    
    rowLevel:function(row) {
      if (_.isUndefined(row._info)) return 0;
      return row._info.level;
    },
    
    isEditable : function(name) {
      var editable = false;
      if ( !_.isUndefined(this.fields[name]) ) editable = this.fields[name].schema['grid-edit'];
      return editable;
    },
    
    isReadonly : function(name) {
      var readonly = false;
      if ( !_.isUndefined(this.fields[name]) ) readonly = this.fields[name].schema['readonly'];
      return readonly;
    },
    
    
    /**
     * Create url, used for all links (pagination, edit, sort etc..)
     */
    createdUrl : function(parts){
      var defaults = {
        order   : _.isUndefined( this.order ) ? '':this.order,
        find    : _.isUndefined( this.find ) ? '':this.find,
        offset  : _.isUndefined( this.info.offset) ? 0:this.info.offset,
      };
      parts = _.extend( defaults, parts );
      return location.pathname + '?options={"offset":"'+parts.offset+'","order":"'+parts.order+'","find":"'+jdb.encodeURL(parts.find)+'"}';
    },
    
    
    editUrl : function(id) {
      var url = '';
      if (this.gridType==='media') {
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
      var url = this.createdUrl({offset:0,find:find});
      window.location.assign(url);
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
    },
    startUpload : function() {
      var self = this;
      for (var i = 0; i < self.uploadFiles.length; i++) {
        var file = self.uploadFiles[i];
        self.uploadProgress[file.name] = 10;
        self.uploadStatus[file.name] = self.$lang.upload_status_uploading;
        var formData = new FormData();
        formData.set( 'path', self.name );
        formData.set( 'file', self.uploadFiles[i] );
        formData.set( 'fileName', self.uploadFiles[i].name );
        flexyState.api({
          method    : 'POST',
          url       : 'media',
          data      : formData,
          formData  : true,
          onUploadProgress : function(progressEvent) {
            var uploadPercentage = Math.round(progressEvent.loaded * 100 / progressEvent.total);
            self.uploadProgress[file.name] = uploadPercentage;
          },
        }).then(function(response){
          var error = response.data.error;
          var fileName = response.data.args.fileName;
          if (!error && response.data.data===false) error = self.$lang.upload_error;
          if (error) {
            self.uploadStatus[fileName] = error;
          }
          else {
            var fileName = response.data.args.fileName;
            var index = jdb.indexOfProperty(self.uploadFiles,'name',fileName);
            self.uploadProgress[fileName] = 100;
            self.removeUploadFile(index);
          }
          // Als alles is geuploade, reload
          if (self.uploadFiles.length === 0 ) {
            // self.reloadPage();
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
      if (this.gridType==='tree' && this.items[index]._info.has_children) {
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
        
        var parent_id = 0; 
        if (this.gridType==='tree') {
          var items = _.cloneDeep(this.draggable.oldItems);
          var number_of_children = this.draggable.children.length || 0;
          // Pas parent van verplaatste item aan
          // Bijna altijd 0, behalve als het volgende item een hoger level heeft: dan heeft het dezelfde parent als dat item, dus als er een item na komt, neem die parent.
          // Check eerst of het niet de laatste is, want dan hoeven we al niet verder te kijken
          if (newIndex+1 < this.items.length) {
            parent_id = this.items[newIndex+1].self_parent.value;
          }
          items[oldIndex].self_parent.value = parent_id;
          // Verplaats item & children
          this.items = jdb.moveMultipleArrayItems( items, oldIndex, number_of_children + 1, newIndex);
        }
        
        // Update 'order'
        var order = this.draggable.orderStart;
        for (var i = 0; i < this.items.length; i++) {
          this.items[i].order.value = order;
          order++;
        }
        // Vernieuw de tree info
        if (this.gridType=='tree') this.items = this.addInfo(this.items);
        
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
        if (self.gridType==='tree') {
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
  <div class="card grid" :class="gridTypeClass" @dragover.prevent  @drop="dropUploadFiles">
    <!-- MAIN HEADER -->
    <div class="card-header">
      <h1>{{title}}</h1>
      <form class="form-inline" @submit="startFinding($event)">
        <div class="form-group" v-if="!extendedFind"><input type="text" v-model.trim="findTerm" class="form-control form-control-sm" id="grid-find" :placeholder="$lang.grid_fast_search"></div>
        <div class="btn-group">
          <flexy-button @click.native.stop.prevent="startFinding($event)" icon="search" class="btn-default" />
          <flexy-button @click.native.stop.prevent="extendedFind=!extendedFind" :icon="{'chevron-up':extendedFind,'chevron-down':!extendedFind}" class="btn-default" />
        </div>
      </form>
    </div>

    <!-- EXTENDED SEARCH -->
    <div class="card-header grid-extended-find" v-if="extendedFind">
      <h4>{{$lang.grid_extended_search}}</h4>
      <form v-for="(term,index) in extendedTerm" class="form-inline" @submit.stop.prevent="startFinding($event)" :index="index">
        <div class="form-group grid-extended-search-and">
          <select class="form-control form-control-sm" name="grid-extended-search-and[]" v-model="term.and">
            <option value="OR" :selected="term.and=='OR'">{{$lang.grid_search_or}}</option>
            <option value="AND" :selected="term.and=='AND'">{{$lang.grid_search_and}}</option>
          </select>
        </div>
        <div class="form-group grid-extended-search-field" :class="{'has-danger':!term.field}">
          <select class="form-control form-control-sm" name="grid-extended-search-field[]" v-model="term.field">
            <option v-for="(field,key) in fields" :value="key" :selected="term.field===key">{{field.name}}</option>
          </select>
        </div>
        <div class="form-group grid-extended-search-equals">
          <select class="form-control form-control-sm" name="grid-extended-search-equals[]" v-model="term.equals">
            <option value="exist" :selected="term.equals==='exist'">{{$lang.grid_search_exist}}</option>
            <option value="word" :selected="term.equals==='word'">{{$lang.grid_search_word}}</option>
            <option value="exact" :selected="term.equals==='exact'">{{$lang.grid_search_exact}}</option>
          </select>
        </div>
        <div class="form-group grid-extended-search-term" :class="{'has-danger':term.term===''}">
          <input type="text" class="form-control form-control-sm" v-model="term.term" :placeholder="$lang.grid_search" name="grid-extended-search-term[]">
        </div>
        <flexy-button @click.native.stop.prevent="extendedSearchRemove(index)" icon="remove" class="btn-outline-danger" />
        <flexy-button @click.native.stop.prevent="extendedSearchAdd()" icon="plus" class="btn-outline-warning" />
      </form>
    </div>

    <!-- UPLOAD BOX -->
    <div v-if="gridType==='media'" v-show="uploadFiles.length > 0" class="card-block grid-upload">
      <input id="browsefiles" @change="addUploadFiles"  type="file" name="files[]" multiple="multiple">
      <table class="table table-sm">
        <thead>
          <tr><th><flexy-button @click.native="startUpload" icon="upload" :text="$lang.upload" class="btn-primary" /></th><th>{{$lang.upload_file}}</th><th>{{$lang.upload_size}}</th><th>{{$lang.upload_progress}}</th><th>{{$lang.upload_status}}</th></tr>
        </thead>
        <tbody>
          <tr v-for="(file,index) in uploadFiles"><td><flexy-button @click.native="removeUploadFile(index)" icon="remove" class="btn-outline-danger" /></td><td>{{file.name}}</td><td>{{Math.floor(file.size / 1024)}}k</td><td><progress class="progress progress-success progress-striped progress-animated" :value="uploadProgress[file.name] || 0" max="100"></td><td>{{uploadStatus[file.name]}}</td></tr>
        </tbody>
      </table>
    </div>
    
    <!-- GRID HEADERS -->
    <div class="card-block table-responsive">
      <table class="table table-bordered table-sm grid-data">
        <thead>
          <tr>
            <template v-for="(field,key) in fields">
              <th v-if="isPrimaryHeader(field)" :class="headerClass(field)" class="text-primary grid-actions">
                <flexy-button @click.native="newItem()" icon="plus" class="btn-outline-warning" />
                <flexy-button @click.native="removeItems()" icon="remove" :class="{disabled:!hasSelection()}" class="btn-outline-danger" />
                <flexy-button @click.native="reverseSelection()" icon="dot-circle-o" class="btn-outline-info" />
                
                <div v-if="isMediaThumbs()" class="dropdown" id="dropdown-sort">
                  <flexy-button icon="sort-amount-asc" class="btn-outline-info" dropdown="dropdown-sort"/>
                  <div class="dropdown-menu">
                    <a v-for="(field,key) in fields" v-if="field.schema.sortable" :href="createdUrl({'order':(key==order?'_'+key:key)})" class="dropdown-item" :class="{'selected':(order.indexOf(key)>=0)}">
                      <span v-if="order==key" class="fa fa-caret-up"></span>
                      <span v-if="order=='_'+key" class="fa fa-caret-down"></span>
                      {{field.name}}
                    </a>
                  </div>
                </div>
                
              </th>
              <th v-if="isNormalVisibleHeader(field)" :class="headerClass(field)"  class="text-primary">
                <a :href="createdUrl({'order':(key==order?'_'+key:key)})"><span>{{field.name}}</span>
                  <span v-if="order==key" class="fa fa-caret-up"></span>
                  <span v-if="order=='_'+key" class="fa fa-caret-down"></span>
                </a>  
              </th>
            </template>
          </tr>
        </thead>
        <!-- GRID BODY -->
        <draggable  :list="items" element="tbody" :options="draggableOptions" @start="draggable_onStart" @end="draggable_onEnd">
          <!-- ROW -->
          <tr v-for="row in items" :data-id="row.id.value" :class="{'table-warning is-selected':isSelected(row.id.value)}" v-show="!isHiddenChild(row.id.value)" :level="rowLevel(row)" :key="row.id.value">
            <template v-for="cell in row">
              
              <!-- PRIMARY CELL -->
              <td v-if="cell.type=='primary'" class="action">
                <flexy-button @click.native="editItem(cell.value)" icon="pencil" class="btn-outline-warning" />
                <flexy-button @click.native="removeItems(row.id.value)" icon="remove" class="btn-outline-danger" />
                <flexy-button @click.native="select(row.id.value)" :icon="{'circle-o':!isSelected(row.id.value),'circle':isSelected(row.id.value)}" class="btn-outline-info" />
                <flexy-button v-if="gridType==='tree' || gridType==='ordered'" icon="reorder" class="draggable-handle btn-outline-info" :class="{'active':isDragging(row.id.value)}" />
              </td>
              
              <!-- CELL -->
              <flexy-grid-cell v-else
                @select="select(row.id.value)"
                :focus="false"
                :type="cell.type"
                :name="cell.name"
                :value="cell.value"
                :level="rowLevel(row)"
                :primary="{'table':dataName,'id':row.id.value}"
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
      <div class="btn-group actions" v-if="gridType === 'media'">
        <template v-if="getMediaView()==='list'">
          <flexy-button icon="bars" class="btn-primary" border="true" />
          <flexy-button icon="picture-o" @click.native="setMediaView('thumbs')" class="btn-outline-primary" border="true"/>
        </template>
        <template v-if="getMediaView()==='thumbs'">
          <flexy-button icon="bars" @click.native="setMediaView('list')" class="btn-outline-primary" border="true"/>
          <flexy-button icon="picture-o" class="btn-primary" border="true"/>
        </template>
      </div>
      <flexy-pagination v-if="needsPagination" :total="info.total_rows" :pages="info.num_pages" :current="info.page + 1" :limit="info.limit" :url="createdUrl({'offset':'##'})"></flexy-pagination>
      <div v-if="!needsPagination" class="pagination-container">
        <span class="pagination-info text-primary">{{$lang.grid_total | replace(info.total_rows)}}</span>
      </div>
    </div>
  </div>
</template>
