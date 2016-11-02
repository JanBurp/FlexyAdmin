<vue-grid title="<?=$title?>" :fields='<?=array2json($fields)?>' :data='<?=array2json($data)?>' order="<?=$order?>"  :info='<?=json_encode($info)?>' inline-template>

  <div class="card grid">

    <h1 class="card-header">{{title}}</h1>

    <div class="card-block table-responsive">
      <table class="table table-striped table-bordered table-hover table-sm">

        <thead>
          <tr>
            <th v-for="(field,key) in fields">
              <a :href="createdUrl({'order':(key==order?'_'+key:key)})">{{field.name}}
                <span v-if="order==key" class="fa fa-caret-up"></span>
                <span v-if="order=='_'+key" class="fa fa-caret-down"></span>
              </a>  
            </th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="row in gridData" :data-id="row.id">
            <td v-for="cell in row" :data-id="row.id" :data-type="cell.type" :data-value="cell.value">{{cell.value}}</td>
          </tr>
        </tbody>
        
      </table>
    </div>
    
    <div v-if="needsPagination" class="card-footer text-muted">
      <vue-pagination :total="info.num_pages" :current="info.page + 1" :limit="info.limit" :url="createdUrl({'offset':'##'})"></vue-pagination>
    </div>
    
  </div>


</vue-grid>
