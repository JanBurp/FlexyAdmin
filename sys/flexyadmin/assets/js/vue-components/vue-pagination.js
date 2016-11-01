/**
  vue-pagination
  Component for pagination, with vue & bootstrap
 */

Vue.component('vue-pagination', {
  
  /**
    total   - total number of pages
    current - current page
    limit   - items per page
    url     - template for building url, where {{offset}} will be replaced with the offset
   */
  
  props:{
    'total'   : Number,
    'current' : Number,
    'limit'   : Number,
    'url'     : String,
    'buttons' : {
      type:Number,
      default:5
    },
  },
  
  methods:{
    
    /**
      Calculates number of buttons needed, returns it as an array
     */
    totalButtons : function() {
      if (this.buttons>=this.total) return this.total;
      var min = this.current - Math.floor(this.buttons/2);
      var max = this.current + Math.floor(this.buttons/2);
      while (min<=0) {
        min++;
        max++;
      }
      while (max>=this.total) {
        min--;
        max--;
      }
      var numberButtons = [];
      for (var i = min; i <= max; i++) {
        numberButtons.push(i);
      }
      return numberButtons;
    },
    
    /**
     * Creates the URL for each button
     */
    pageUrl : function(page) {
      return this.url.replace('##',( (page-1) * this.limit ))
    },
    
  },
  template:'\
    <ul class="pagination">\
      <li v-if="current>1  && total>buttons" class="page-item"><a class="page-link" :href="pageUrl(1)"><span class="fa fa-fast-backward"></span></a></li>\
      <li v-if="current>10 && total>buttons" class="page-item"><a class="page-link" :href="pageUrl(current-10)"><span class="fa fa-chevron-left"></span><span class="fa fa-chevron-left"></span></a></li>\
      <li v-if="current>1  && total>buttons" class="page-item"><a class="page-link" :href="pageUrl(current-1)"><span class="fa fa-chevron-left"></span></a></li>\
      \
      <li v-for="page in totalButtons()" class="page-item" :class="{active:(page==current)}"><a class="page-link" :href="pageUrl(page)">{{page}}</a></li>\
      \
      <li v-if="current<total-1  && total>buttons" class="page-item"><a class="page-link" :href="pageUrl(current+1)"><span class="fa fa-chevron-right"></span></a></li>\
      <li v-if="current<total-10 && total>buttons" class="page-item"><a class="page-link" :href="pageUrl(current+10)"><span class="fa fa-chevron-right"></span><span class="fa fa-chevron-right"></span></a></li>\
      <li v-if="current<total-1  && total>buttons" class="page-item"><a class="page-link" :href="pageUrl(total-1)"><span class="fa fa-fast-forward"></span></a></li>\
    </ul>'
})