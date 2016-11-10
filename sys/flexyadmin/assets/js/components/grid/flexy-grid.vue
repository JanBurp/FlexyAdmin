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
    'info':Object,
    'order': {
      type   :String,
      default:''
    },
    'find':{
      type   :[String,Object],
      default:'',
    }
  },
  
 /**
  * Maak items klaar voor tonen in het grid:
  * - Voeg informatie van een veld toe aan elke cell
  * - Bij een tree: voeg informatie aan elke row toe: {level:(int),is_child:(bool),has_children:(bool)}
  */
  created : function() {
    this.items = this.addTreeInfo( this.data, true );
  },
  
  data : function() {
    return {
      items       : [],
      findTerm    : this.find,
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
    }
  },
  
  computed:{
    /**
     * Bepaal het type grid: table, ordered of tree
     */
    gridType : function() {
      var type='table';
      if (typeof(this.fields.order)!=='undefined' && this.order==='' && this.find==='') type='ordered';
      if (typeof(this.fields.self_parent)!=='undefined' && this.order==='' && this.find==='') type='tree';
      return type;
    },
    /**
     * Geeft class van type grid
     */
    gridTypeClass : function() {
      return 'grid-type-'+this.gridType
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
        draggable     : 'tr',
        handle        : '.draggable-handle',
        forceFallback : true,
      }
    },
    
  },
  
  methods:{
    
    /*
      Voeg (tree)info toe aan meegegeven items
    */
    addTreeInfo : function(items,addSchema) {
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
      for (var i = 0; i < this.data.length; i++) {
        ids.push(this.data[i].id.value);
      }
      this.selected = _.difference(ids,this.selected);
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
    
    
    headerClass : function(field) {
      return 'grid-header-type-'+field.schema['form-type'];
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
      return location.pathname + '?options={"offset":"'+parts.offset+'","order":"'+parts.order+'","find":"'+parts.find+'"}';
    },
    
    editUrl : function(id) {
      return 'admin/show/form/'+this.name+'/'+id;
    },
    
    startFinding : function(event) {
      if (event) event.preventDefault();
      var url = this.createdUrl({offset:0,find:this.findTerm});
      window.location.assign(url);
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
        if (this.gridType=='tree') this.items = this.addTreeInfo(this.items);
        
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
        <div class="form-group"><input type="text" v-model.trim="findTerm" class="form-control form-control-sm" id="grid-find" :placeholder="$lang.grid_search"></div>
        <button type="submit" class="btn btn-sm btn-secundary"><span class="fa fa-search"></span></button>
      </form>
    </div>
    <!-- GRID HEADERS -->
    <div class="card-block table-responsive">
      <table class="table table-bordered table-hover table-sm">
        <thead>
          <tr>
            <template v-for="(field,key) in fields">
              <th v-if="field.schema['form-type']==='primary'" :class="headerClass(field)" class="text-primary">
                <a class="btn btn-sm btn-warning" :href="editUrl(-1)"><span class="fa fa-plus"></span></a>
                <div :class="{disabled:!hasSelection()}" class="btn btn-sm btn-danger action-delete"><span class="fa fa-remove"></span></div>
                <div v-on:click="reverseSelection()" class="btn btn-sm btn-info action-select"><span class="fa fa-circle-o"></span></div>
              </th>
              <th v-if="field.schema['form-type']!=='hidden' && field.schema['form-type']!=='primary'" :class="headerClass(field)"  class="text-primary">
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
          <tr v-for="row in items" :data-id="row.id.value" :class="{'table-danger':isSelected(row.id.value)}" v-show="!isHiddenChild(row.id.value)" :level="rowLevel(row)" :key="row.id.value">
            <template v-for="cell in row">
              <!-- PRIMARY CELL -->
              <td v-if="cell.type=='primary'" class="action">
                <a class="btn btn-sm btn-outline-warning" :href="editUrl(cell.value)"><span class="fa fa-pencil"></span></a>
                <div class="btn btn-sm btn-outline-danger action-delete"><span class="fa fa-remove"></span></div>
                <div v-on:click="select(row.id.value)" class="btn btn-sm btn-outline-info action-select"><span v-if="!isSelected(row.id.value)" class="fa fa-circle-o"></span><span v-if="isSelected(row.id.value)" class="fa fa-circle"></span></div>
                <div v-if="gridType!=='table'"class="draggable-handle btn btn-sm btn-outline-info action-move" :class="{'active':isDragging(row.id.value)}"><span class="fa fa-reorder"></span></div>
              </td>
              <!-- CELL -->
              <flexy-grid-cell v-else :type="cell.type" :name="cell.name" :value="cell.value" :level="rowLevel(row)" :primary="{'table':name,'id':row.id.value}" :editable="isEditable(cell.name)"></flexy-grid-cell>
            </template>
          </tr>
        </draggable>
      </table>
    </div>
    <!-- FOOTER -->
    <div v-if="needsPagination" class="card-footer text-muted">
      <flexy-pagination :total="info.total_rows" :pages="info.num_pages" :current="info.page + 1" :limit="info.limit" :url="createdUrl({'offset':'##'})"></flexy-pagination>
    </div>
  </div>
</template>

<style>
  .grid .card-block {padding:0;}
  .grid .card-footer {padding:.25rem .25rem 0;}
  .grid th {overflow:hidden;text-overflow:ellipsis;}
  .grid th a {text-decoration:none;}
  .grid th span {white-space:nowrap;text-transform:uppercase;}
  .grid th > span.fa {position:relative;float:right;margin-top:.25rem;}
  .grid th.grid-header-type-primary {width:10rem;max-width:10rem;white-space:nowrap;}
  .grid.grid-type-tree th.grid-header-type-primary {width:10rem;max-width:10rem;}
  .grid.grid-type-table th.grid-header-type-primary {width:9rem;max-width:9rem;}
  .grid .draggable-handle {cursor:move;}
  .grid .sortable-fallback {display:none;}
</style>
