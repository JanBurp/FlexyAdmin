<template>
  <div class="card grid" :class="gridTypeClass">

    <div class="card-header">
      <h1>{{title}}</h1>
      <form class="form-inline" v-on:submit="startFinding($event)">
        <div class="form-group"><input type="text" v-model.trim="findTerm" class="form-control form-control-sm" id="grid-find" placeholder="zoeken"></div>
        <button type="submit" class="btn btn-sm btn-primary"><span class="fa fa-search"></span></button>
      </form>
    </div>

    <div class="card-block table-responsive">
      <table class="table table-bordered table-hover table-sm">

        <thead>
          <tr>
            <template v-for="(field,key) in fields">
              <th v-if="field.schema['form-type']==='primary'" :class="headerClass(field)" class="text-primary">
                <a class="btn btn-sm btn-success" :href="editUrl(-1)"><span class="fa fa-plus"></span></a>
                <div class="btn btn-sm btn-info action-select"><span class="fa fa-square-o"></span><span class="fa fa-check-square-o"></span></div>
                <div class="btn btn-sm btn-danger action-delete"><span class="fa fa-remove"></span></div>
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

        <tbody id="grid-body">
          <tr v-for="row in gridData" :data-id="row.id.value">
            <template v-for="cell in row">
              <td v-if="cell.type=='primary'" class="action">
                <a class="btn btn-sm btn-success" :href="editUrl(cell.value)"><span class="fa fa-pencil"></span></a>
                <div class="btn btn-sm btn-info action-select"><span class="fa fa-square-o"></span><span class="fa fa-check-square-o"></span></div>
                <div class="btn btn-sm btn-danger action-delete"><span class="fa fa-remove"></span></div>
                <div v-if="gridType!=='table'"class="btn btn-sm btn-warning action-move"><span class="fa fa-bars"></span></div>
              </td>
              <vue-grid-cell v-else :type="cell.type" :name="cell.name" :value="cell.value"></vue-grid-cell>
            </template>
          </tr>
        </tbody>
        
      </table>
    </div>
    
    <div v-if="needsPagination" class="card-footer text-muted">
      <vue-pagination :total="info.total_rows" :pages="info.num_pages" :current="info.page + 1" :limit="info.limit" :url="createdUrl({'offset':'##'})"></vue-pagination>
    </div>
    
  </div>
</template>

<script>

import VuePagination from './vue-pagination.vue'
import VueGridCell from './vue-grid-cell.vue'

export default {
  name: 'VueGrid',
  components: { VueGridCell,VuePagination },
  props:{
    'title':String,
    'name':String,
    'fields':[Object,Array],
    'data':Array,
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
  
  computed:{
    
    /**
     * Bepaal het type grid: table, ordered of tree
     */
    gridType : function() {
      var type='table';
      if (typeof(this.fields.order)!=='undefined') type='ordered';
      if (typeof(this.fields.self_parent)!=='undefined') type='tree';
      return type;
    },
    
    gridTypeClass : function() {
      return 'grid-type-'+this.gridType
    },
    
    /**
     * Prepare gridData (schema)
     */
    gridData : function() {
      var data = this.data;
      for (var i = 0; i < data.length; i++) {
        var row = data[i];
        var id = row['id'];
        for (var field in row) {
          var schema = {
            'type'      : 'string',
            'form-type' : 'text',
            'readonly'  : false,
          };
          if ( this.fields[field] ) schema = this.fields[field].schema;
          data[i][field] = {
            'type'  : schema['form-type'],
            'value' : row[field]
          };
          if ( schema.type==='number' && schema['form-type']==='select') {
            var jsonValue = JSON.parse(row[field].value);
            data[i][field] = {
              'type'  : schema['form-type'],
              'value' : Object.values(jsonValue)[0],
              'id'    : Object.keys(jsonValue)[0],
            };
          }
          data[i][field].name = field;
        }
        data[i] = row;
      }
      return data;
    },

    /**
     * Test if grid needs pagination
     */
    needsPagination : function(){
      return (typeof(this.info.num_pages)!=='undefined' &&  this.info.num_pages > 1);
    },
    
  },
  
  data : function() {
    return {
      findTerm : this.find
    }
  },
  
  methods:{
    
    headerClass : function(field) {
      return 'grid-header-type-'+field.schema['form-type'];
    },
    
    /**
     * Create url, used for all links (pagination, edit, sort etc..)
     */
    createdUrl : function(parts){
      var defaults = {
        order   :this.order,
        find    :this.find,
        offset  :this.info.offset
      };
      parts = _.extend( defaults, parts );
      return location.pathname + '?options={"offset":"'+parts.offset+'","order":"'+parts.order+'","find":"'+parts.find+'"}';
    },
    
    editUrl : function(id) {
      return 'admin/show/form/'+this.name+'/'+id;
    },
    
    startFinding : function(event) {
      if (event) event.preventDefault();
      var url = this.createdUrl({find:this.findTerm});
      window.location.assign(url);
    },
    
  }
  
}
</script>

<style>
  .grid .card-block {padding:0;}
  .grid .card-header h1 {margin:0;float:left;}
  .grid .card-header form {float:right;}
  .grid .card-footer {padding:.25rem .25rem 0;}
  .grid th {overflow:hidden;text-overflow:ellipsis;}
  .grid th a {text-decoration:none;}
  .grid th span {white-space:nowrap;text-transform:uppercase;}
  .grid th > span.fa {position:relative;float:right;margin-top:.25rem;}
  .grid th.grid-header-type-primary {width:10rem;max-width:10rem;}
  .grid.grid-type-tree th.grid-header-type-primary {width:10rem;max-width:10rem;}
  .grid.grid-type-table th.grid-header-type-primary {width:7.75rem;max-width:7.75rem;}
</style>