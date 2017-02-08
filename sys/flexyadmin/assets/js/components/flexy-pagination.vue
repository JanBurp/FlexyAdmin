<template>
  <div class="pagination-container d-flex justify-content-between">
    <ul class="pagination">
      <li v-if="current>1  && pages>buttons" class="page-item"><a class="page-link" @click="jumpToPage(1)"><span class="fa fa-fast-backward"></span></a></li>
      <li v-if="current>10 && pages>buttons" class="page-item"><a class="page-link" @click="jumpToPage(current-10)"><span class="fa fa-backward"></span></a></li>
      <li v-if="current>1  && pages>buttons" class="page-item"><a class="page-link" @click="jumpToPage(current-1)"><span class="fa fa-chevron-left"></span></a></li>
      
      <li v-for="page in pagesButtons()" class="page-item" :class="{active:(page==current)}"><a class="page-link" @click="jumpToPage(page)">{{page}}</a></li>
      
      <li v-if="current<pages-1  && pages>buttons" class="page-item"><a class="page-link" @click="jumpToPage(current+1)"><span class="fa fa-chevron-right"></span></a></li>
      <li v-if="current<pages-10 && pages>buttons" class="page-item"><a class="page-link" @click="jumpToPage(current+10)"><span class="fa fa-forward"></span></a></li>
      <li v-if="current<pages-1  && pages>buttons" class="page-item"><a class="page-link" @click="jumpToPage(pages-1)"><span class="fa fa-fast-forward"></span></a></li>
    </ul>

    <span v-if="total===maxtotal" class="pagination-info text-primary">{{$lang.grid_pagination | replace(total,pages)}}</span>
    <span v-if="total!==maxtotal" class="pagination-info text-primary">{{$lang.grid_pagination_max | replace(total,maxtotal,pages)}}</span>

  </div>
</template>

<script>
export default {
  name : 'VuePagination',
  props:{
    'total'   : Number,     // total number of rows
    'maxtotal': Number,     // total number of rows (without searching)
    'pages'   : Number,     // pages number of pages
    'current' : Number,     // current page
    'limit'   : Number,     // items per page
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

    jumpToPage : function(page) {
      this.$emit('newpage',(page-1) * this.limit);
    }
    
  }
}
</script>