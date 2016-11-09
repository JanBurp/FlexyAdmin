<template>
  <div class="pagination-container">
    <ul class="pagination">
      <li v-if="current>1  && pages>buttons" class="page-item"><a class="page-link" :href="pageUrl(1)"><span class="fa fa-fast-backward"></span></a></li>
      <li v-if="current>10 && pages>buttons" class="page-item"><a class="page-link" :href="pageUrl(current-10)"><span class="fa fa-chevron-left"></span><span class="fa fa-chevron-left"></span></a></li>
      <li v-if="current>1  && pages>buttons" class="page-item"><a class="page-link" :href="pageUrl(current-1)"><span class="fa fa-chevron-left"></span></a></li>
      
      <li v-for="page in pagesButtons()" class="page-item" :class="{active:(page==current)}"><a class="page-link" :href="pageUrl(page)">{{page}}</a></li>
      
      <li v-if="current<pages-1  && pages>buttons" class="page-item"><a class="page-link" :href="pageUrl(current+1)"><span class="fa fa-chevron-right"></span></a></li>
      <li v-if="current<pages-10 && pages>buttons" class="page-item"><a class="page-link" :href="pageUrl(current+10)"><span class="fa fa-chevron-right"></span><span class="fa fa-chevron-right"></span></a></li>
      <li v-if="current<pages-1  && pages>buttons" class="page-item"><a class="page-link" :href="pageUrl(pages-1)"><span class="fa fa-fast-forward"></span></a></li>
    </ul>
    <span class="pagination-info text-primary">{{$lang.grid_pagination | replace(total,pages)}}</span>
  </div>
</template>

<script>
export default {
  name : 'VuePagination',
  props:{
    'total'   : Number,     // total number of rows
    'pages'   : Number,     // pages number of pages
    'current' : Number,     // current page
    'limit'   : Number,     // items per page
    'url'     : String,     // template for building url, where {{offset}} will be replaced with the offset
    'buttons' : {           // Number of page-buttons used for pagination
      type:Number,
      default:5
    },
  },
  
  methods:{
    
    /**
      Calculates number of buttons needed, returns it as an array
     */
    pagesButtons : function() {
      if (this.buttons>=this.pages) return this.pages;
      var min = this.current - Math.floor(this.buttons/2);
      var max = this.current + Math.floor(this.buttons/2);
      while (min<=0) {
        min++;
        max++;
      }
      while (max>=this.pages) {
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
      return this.url.replace('##',( (page-1) * this.limit ));
    },
    
  }
}
</script>

<style>
  .pagination-container {width:100%;}
  .pagination {margin:0;}
  .pagination-info {
    margin-top:.5rem;
    float:right;
  }
</style>