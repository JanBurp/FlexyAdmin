<div class="card form">
  <h1 class="card-header"><?=$title?></h1>

  <div class="card-block">
    
    <vf-form action="<?=$this->uri->uri_string()?>" method="POST">
      <vf-status-bar ref="statusbar"></vf-status-bar>
      
      <vf-text label="User name:" required name="username" ref="username"></vf-text>
      <vf-password label="Password:" required name="password" ref="password"></vf-password>
      
      
      <vf-submit></vf-submit>
    </vf-form>


  </div>
  
</div>
