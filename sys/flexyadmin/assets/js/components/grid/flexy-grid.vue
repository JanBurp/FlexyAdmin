<script>
import draggable        from 'vuedraggable'

import jdb              from '../../jdb-tools.js'
import flexyState       from '../../flexy-state.js'

import FlexyPagination  from '../flexy-pagination.vue'
import FlexyGridCell    from './flexy-grid-cell.vue'

export default {
  name: 'FlexyGrid',
  components: {draggable,FlexyGridCell,FlexyPagination },
  props:{
    'title':String,
    'name':String,
    'fields':[Object,Array],
    'data':{
      type: [Array,Boolean],
      default:false
    },
    // 'data-url':{
    //   type: [String,Boolean],
    //   default:false
    // },
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
  },
  
  data : function() {
    return {
      items       : [],
      selected    : [],
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
    
    getMediaView : function() {
      return flexyState.getMediaView();
    },

    setMediaView : function(view) {
      return flexyState.setMediaView(view);
    },

    
    headerClass : function(field) {
      return 'grid-header-type-'+field.schema['form-type'];
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
    
    removeItems : function(removeIds) {
      var self = this;
      if ( _.isUndefined(removeIds)) {
        removeIds = this.selected;
      }
      else {
        removeIds = [removeIds];
      }
      // Confirm
      var message = this.$lang['confirm_delete_one'];
      if (removeIds.length>1) message = this.$options.filters.replace( this.$lang['confirm_delete_multiple'], removeIds.length );
      if (window.confirm(message)) {
        return this.api({
          url   : 'row',
          data  : {
            table : self.name,
            where : removeIds,
          },
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
      return 'admin/show/form/'+this.name+'/'+id;
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

      if (oldIndex!==newIndex) {
        this.draggable.oldIndex = oldIndex;
        this.draggable.newIndex = newIndex;
        
        if (this.gridType==='tree') {
          var items = _.cloneDeep(this.draggable.oldItems);
          var number_of_children = this.draggable.children.length || 0;
          // Pas parent van verplaatste item aan
          // Bijna altijd 0, behalve als het volgende item een hoger level heeft: dan heeft het dezelfde parent als dat item, dus als er een item na komt, neem die parent.
          // Check eerst of het niet de laatste is, want dan hoeven we al niet verder te kijken
          var parent_id = 0; 
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
        
        // Laat children weer zien
        this.draggable.children = false;

        if (flexyState.debug) {
          console.log( 'draggable_onEnd ---------' );
          console.log(oldIndex,' => ',newIndex);
          self._log(self.items);
        }
        
        self.postNewOrder().then(function(response){
          self.draggable.item = false;
        });
      }
      else {
        self.draggable.item = false;
      }
    },
    
    postNewOrder : function() {
      var self=this;
      var itemId = this.draggable.item;
      var newOrder = this.draggable.orderStart + this.items[ this.draggable.newIndex ].order.value;
      return this.api({
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
  <div class="card grid" :class="gridTypeClass">
    <!-- MAIN HEADER -->
    <div class="card-header">
      <h1>{{title}}</h1>
      <form class="form-inline" v-on:submit="startFinding($event)">
        <div class="form-group" v-if="!extendedFind"><input type="text" v-model.trim="findTerm" class="form-control form-control-sm" id="grid-find" :placeholder="$lang.grid_search"></div>
        <div class="btn-group">
          <button type="submit" class="btn btn-warning"><span class="fa fa-search"></span></button>
          <button type="button" class="btn btn-warning" v-if="!extendedFind" @click="extendedFind=true"><span class="fa fa-chevron-down"></span></button>
          <button type="button" class="btn btn-warning" v-if="extendedFind" @click="extendedFind=false"><span class="fa fa-chevron-up"></span></button>
        </div>
      </form>
    </div>

    <!-- EXTENDED SEARCH -->
    <div class="card-header grid-extended-find" v-if="extendedFind">
      <form v-for="(term,index) in extendedTerm" class="form-inline">
        <div class="form-group grid-extended-search-and">
          <select class="form-control form-control-sm" name="grid-extended-search-and[]" v-model="term.and">
            <option value="OR" :selected="term.and=='OR'">of</option>
            <option value="AND" :selected="term.and=='AND'">en</option>
          </select>
        </div>
        <div class="form-group grid-extended-search-field" :class="{'has-danger':!term.field}">
          <select class="form-control form-control-sm" name="grid-extended-search-field[]" v-model="term.field">
            <option v-for="(field,key) in fields" :value="key" :selected="term.field===key">{{field.name}}</option>
          </select>
        </div>
        <div class="form-group grid-extended-search-equals">
          <select class="form-control form-control-sm" name="grid-extended-search-equals[]" v-model="term.equals">
            <option value="exist" :selected="term.equals==='exist'">bevat</option>
            <option value="word" :selected="term.equals==='word'">bevat woord</option>
            <option value="exact" :selected="term.equals==='exact'">is gelijk aan</option>
          </select>
        </div>
        <div class="form-group grid-extended-search-term" :class="{'has-danger':term.term===''}">
          <input type="text" class="form-control form-control-sm" v-model="term.term" :placeholder="$lang.grid_search" name="grid-extended-search-term[]">
        </div>
        <button type="button" class="btn btn-danger" @click="extendedSearchRemove(index)"><span class="fa fa-remove"></span></button>
        <button type="button" class="btn btn-warning" @click="extendedSearchAdd()"><span class="fa fa-plus"></span></button>
      </form>
    </div>
    
    <!-- GRID HEADERS -->
    <div class="card-block table-responsive">
      <table class="table table-bordered table-sm">
        <thead>
          <tr>
            <template v-for="(field,key) in fields">
              <th v-if="isPrimaryHeader(field)" :class="headerClass(field)" class="text-primary">
                <a class="btn btn-outline-warning" :href="editUrl(-1)"><span class="fa fa-plus"></span></a>
                <div :class="{disabled:!hasSelection()}" class="btn btn-outline-danger action-delete" @click="removeItems()"><span class="fa fa-remove"></span></div>
                <div v-on:click="reverseSelection()" class="btn btn-outline-info action-select"><span class="fa fa-square-o"></span></div>
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
          <tr v-for="row in items" :data-id="row.id.value" :class="{'table-warning':isSelected(row.id.value)}" v-show="!isHiddenChild(row.id.value)" :level="rowLevel(row)" :key="row.id.value">
            <template v-for="cell in row">
              <!-- PRIMARY CELL -->
              <td v-if="cell.type=='primary'" class="action">
                <a class="btn btn-outline-warning" :href="editUrl(cell.value)"><span class="fa fa-pencil"></span></a>
                <div class="btn btn-outline-danger action-delete" @click="removeItems(row.id.value)"><span class="fa fa-remove"></span></div>
                <div v-on:click="select(row.id.value)" class="btn btn-outline-info action-select"><span v-if="!isSelected(row.id.value)" class="fa fa-square-o"></span><span v-if="isSelected(row.id.value)" class="fa fa-check-square-o"></span></div>
                <div v-if="gridType==='tree' || gridType==='ordered'"class="draggable-handle btn btn-outline-info action-move" :class="{'active':isDragging(row.id.value)}"><span class="fa fa-reorder"></span></div>
              </td>
              <!-- CELL -->
              <flexy-grid-cell v-else :type="cell.type" :name="cell.name" :value="cell.value" :level="rowLevel(row)" :primary="{'table':name,'id':row.id.value}" :editable="isEditable(cell.name)" :options="fields[cell.name]"></flexy-grid-cell>
            </template>
          </tr>
        </draggable>
      </table>
    </div>
    <!-- FOOTER -->
    <div class="card-footer text-muted">
      <div class="btn-group actions" v-if="gridType === 'media'">
        <template v-if="getMediaView()==='list'">
          <button class="btn btn-primary"><span class="fa fa-bars fa-fw"></span></button>
          <button class="btn btn-outline-primary" v-on:click="setMediaView('thumbs')"><span class="fa fa-picture-o fa-fw"></span></button>
        </template>
        <template v-if="getMediaView()==='thumbs'">
          <button class="btn btn-outline-primary" v-on:click="setMediaView('list')"><span class="fa fa-bars fa-fw"></span></button>
          <button class="btn btn-primary"><span class="fa fa-picture-o fa-fw"></span></button>
        </template>
      </div>
      <flexy-pagination v-if="needsPagination" :total="info.total_rows" :pages="info.num_pages" :current="info.page + 1" :limit="info.limit" :url="createdUrl({'offset':'##'})"></flexy-pagination>
      <div v-if="!needsPagination" class="pagination-container">
        <span class="pagination-info text-primary">{{$lang.grid_total | replace(info.total_rows)}}</span>
      </div>
    </div>
  </div>
</template>

<style lang="sass">

  @import "../../../scss/theme";

  .grid .card-block {padding:0;}
  .grid .card-header.grid-extended-find {background-color:$gray-lighter!important;color:$brand-primary!important;}
  .grid .card-header.grid-extended-find form {clear:both;float:right!important;margin:0 0 .25rem;}
  .grid .card-header.grid-extended-find form:first-child>div.grid-extended-search-and {display:none;}
  .grid .card-header.grid-extended-find form:last-child{margin-bottom:0;}
  .grid .card-footer {padding:.35rem .35rem;}
  .grid .card-footer .actions {float:left;margin-top:.25rem;}
  .grid .pagination-info {margin-right:.25rem;float:right;}
  
  .grid table {margin-bottom:0;}
  .grid th {overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
  .grid th a {text-decoration:none;}
  .grid th span {white-space:nowrap;text-transform:uppercase;}
  .grid th > span.fa {position:relative;float:right;margin-top:1px;}
  .grid th.grid-header-type-primary {width:7rem;max-width:7rem;min-width:7rem;white-space:nowrap;}
  .grid.grid-type-tree th.grid-header-type-primary {width:8rem;max-width:8rem;min-width:8rem;}
  .grid .draggable-handle {cursor:move;}
  .grid .sortable-fallback {display:none;}
  
  .grid .btn {width:1.85rem;height:1.6rem;padding:.1rem 0;text-align:center;}
  .grid .btn .fa {width:1rem;}
  
  .grid option, .grid select {text-transform:uppercase;}
  
  .grid.grid-media-view-thumbs tbody tr {
    display:block!important;
    position:relative;
    float:left;
    width:102px;
    height:130px;
    margin:.5rem;
    padding:0;
    overflow:hidden;
    border:solid 1px $brand-primary;
    border-radius:$border-radius;
    overflow:visible;
  }
  .grid.grid-media-view-thumbs tbody td { position:absolute;float:left; border:none; padding:0px; background-color:transparent!important}
  .grid.grid-media-view-thumbs tbody td[name="media_thumb"] img {width:auto;height:100%;}
  .grid.grid-media-view-thumbs tbody td.action {width:100%;margin-top:102px;text-align:center;}
  .grid.grid-media-view-thumbs tbody td[name="alt"] {bottom:2rem;text-align:center;width:100%;}
  
  .grid.grid-media-view-thumbs tbody td[name='name'],
  .grid.grid-media-view-thumbs tbody td[name='path'],
  .grid.grid-media-view-thumbs tbody td[name='type'],
  .grid.grid-media-view-thumbs tbody td[name='date'],
  .grid.grid-media-view-thumbs tbody td[name='size'],
  .grid.grid-media-view-thumbs tbody td[name='width'],
  .grid.grid-media-view-thumbs tbody td[name='height'] {display:none}

  
</style>
