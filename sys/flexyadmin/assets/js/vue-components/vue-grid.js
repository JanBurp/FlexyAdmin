
Vue.component('vue-grid', {
  
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
    
    
  }

});