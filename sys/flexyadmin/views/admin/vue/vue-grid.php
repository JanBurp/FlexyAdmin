<vue-grid title="<?=$title?>" name="<?=$name?>" :fields='<?=array2json($fields)?>' :data='<?=array2json($data)?>' order="<?=$order?>"  :info='<?=json_encode($info)?>' inline-template>

  <div class="card grid" :class="gridTypeClass">

    <h1 class="card-header">{{title}}</h1>

    <div class="card-block table-responsive">
      <table class="table table-striped table-bordered table-hover table-sm">

        <thead>
          <tr>
            <template v-for="(field,key) in fields">
              <th v-if="field.schema['form-type']==='primary'" :class="headerClass(field)">
                <a class="btn btn-sm btn-success" :href="editUrl(-1)"><span class="fa fa-plus"></span></a>
                <div class="btn btn-sm btn-info action-select"><span class="fa fa-square-o"></span><span class="fa fa-check-square-o"></span></div>
                <div class="btn btn-sm btn-danger action-delete"><span class="fa fa-remove"></span></div>
              </th>
              <th v-if="field.schema['form-type']!=='hidden' && field.schema['form-type']!=='primary'" :class="headerClass(field)">
                <a :href="createdUrl({'order':(key==order?'_'+key:key)})"><span>{{field.name}}</span>
                  <span v-if="order==key" class="fa fa-caret-up"></span>
                  <span v-if="order=='_'+key" class="fa fa-caret-down"></span>
                </a>  
              </th>
            </template>
          </tr>
        </thead>

        <tbody>
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


</vue-grid>
