      <div class="row">
        <div class="col-md-12">
          <h2>Available languages <a href="https://github.com/bcit-ci/codeigniter3-translations" target="_blank"><img src="<?php echo base_url('sys/flexyadmin/assets/img/github.png'); ?>"></a></h2>

          <div class="row">
<?php for ($i = 0; $i < $nb_col && $i < ceil($nb_lang / $nb_per_col); $i++) : ?>
            <div class="<?php echo $class_col; ?>">
              <div class="list-group">
<?php for ($j = 0; $j < $nb_per_col && ($i * $nb_per_col) + $j < $nb_lang; $j++) : ?>
                <?php echo $languages[($i * $nb_per_col) + $j]['link'] . "\n"; ?>
<?php endfor; ?>
              </div>
            </div><!-- col (lang) -->
<?php endfor; ?>
          </div><!-- row (lang) -->

        </div><!-- col (main) -->
      </div><!-- row (main) -->