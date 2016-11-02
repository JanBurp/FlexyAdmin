
Vue.component('vue-grid', {
  
  props:{
    'title':String,
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
     * Prepare gridData
     */
    gridData : function() {
      var data = this.data;
      for (var i = 0; i < data.length; i++) {
        var row = data[i];
        var id = row['id'];
        for (var field in row) {
          var schema = this.fields[field].schema;
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
    }
  }

});