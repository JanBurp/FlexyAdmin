<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plugin_import_wordpress extends Plugin {

  public function __construct() {
    parent::__construct();
    $this->CI->load->library('form');
    $this->CI->load->library('upload');
  }

	public function _admin_api($args=NULL) {
    $this->add_message("Importing a wordpress XML export file.");
    
    $fields=array('file'=>array('type'=>'file'));
    $form = new Form();
    $form->set_data($fields);
    if ($form->validation()) {
      $tmpname=$_FILES['file']['tmp_name'];
      $xml=read_file($tmpname);
      $XMLarray=xml2array($xml);
      $this->importWPxml($XMLarray);
    }
    else {
      $this->add_message($form->render());
    }
    return $this->view('admin/plugins/plugin');
	}
  
  /**
   * Import XML->DB
   *
   * @param string $XML 
   * @return void
   * @author Jan den Besten
   */
  private function importWPxml($XML) {
    $XMLcontent=$XML['rss']['channel'];
    
    // Parse
    // $categories=$this->wp_import_tag($XMLcontent,'wp:category');
    // $tags=$this->wp_import_tag($XMLcontent,'wp:tag');
    $items=$this->wp_import_items($XMLcontent);
    
    // Put in DB

    // posts
    $table=$this->config['posts_table'];
    $commments_table=$this->config['comments_table'];
    $foreign_key='id_'.remove_prefix($table);
    $comments=$items['comments'];
    $imgcount=0;
    foreach ($items['posts'] as $old_id => $post) {
      $post['txt_text']=$this->add_images($post['txt_text']);
      if ($post['txt_text']!=$items['posts'][$old_id]) $imgcount++;
      unset($post['id']);
      $this->CI->db->set($post);
      $this->CI->db->insert($table);
      $id=$this->CI->db->insert_id();
      // comments for this post
      $commcount=0;
      foreach ($comments as $old_cid => $comm) {
        if ($comm['id_post']==$old_id) {
          $commcount++;
          unset($comm['id']);
          $comm[$foreign_key]=$id;
          unset($comm['id_post']);
          $this->CI->db->set($comm);
          $this->CI->db->insert($commments_table);
        }
      }
      $this->add_message('<p>Added: "'.$post['str_title'].'" with '.$commcount.' comments and '.$imgcount.' images.');
    }
  }


  /**
   * Get simple XML tags from WP XML
   *
   * @param string $XML 
   * @param string $tag 
   * @return array
   * @author Jan den Besten
   */
  private function wp_import_tag($XML,$tag) {
    $data=$XML[$tag];
    $arr=array();
    foreach ($data as $key => $item) {
      $arritem=array();
      $id=$item['wp:term_id'];
      unset($item['wp:term_id']);
      $arritem['id']=$id;
      foreach ($item as $field => $value) {
        $arritem[str_replace('wp:','',$field)]=$value;
      }
      $arr[$id]=$arritem;
    }
    return $arr;
  }


  /**
   * Get all items, posts and comments
   *
   * @param string $XML 
   * @return array
   * @author Jan den Besten
   */
  private function wp_import_items($XML) {
    $XMLitems=$XML['item'];
    $pages=array();
    $posts=array();
    $comments=array();
    foreach ($XMLitems as $XMLitem) {
      if (!empty($XMLitem['content:encoded']) and $XMLitem['wp:status']=='publish') {
        $item=array();
        $item['str_redirect']=$XMLitem['link'];
        $item['uri']=$this->get_uri($item['str_redirect']);
        $item['str_title']=$XMLitem['title'];
        $item['tme_date']=$XMLitem['wp:post_date'];
        $item['txt_text']=p().$XMLitem['content:encoded']._p();
        // $item['id_nieuws_type']=3;
        switch($XMLitem['wp:post_type']) {
          case 'page':
            $pages[]=$item;
            break;
          case 'post':
          // trace_($XMLitem);
            $post_id=$XMLitem['wp:post_id']+1000;
            $item['id']=$post_id;
            // add categories & tags
            $categories=$XMLitem['category'];
            $tags=array();
            if (is_array($categories)) {
              foreach ($categories as $key => $value) {
                if (is_array($value)) {
                  if ($value['domain']=='post_tag') {
                    $tags[]=$value['nicename'];
                  }
                  unset($categories[$key]);
                }
              }
              $categories=array_unique($categories);
              $categories=implode('|',$categories);
            }
            $tags=array_unique($tags);
            $tags=implode('|',$tags);
            $item['str_categories']=$categories;
            $item['str_tags']=$tags;
            // add comments
            if (isset($XMLitem['wp:comment'])) {
              $comms=$XMLitem['wp:comment'];
              foreach ($comms as $key => $c) {
                $comment=array();
                if ($c['wp:comment_approved']) {
                  $comment['id_post']=$post_id;
                  $comment['str_name']=$c['wp:comment_author'];
                  $comment['email_email']=$c['wp:comment_author_email'];
                  if (!is_array($c['wp:comment_author_url'])) $comment['url_url']=$c['wp:comment_author_url'];
                  $comment['tme_date']=$c['wp:comment_date'];
                  $comment['txt_text']=p().$c['wp:comment_content']._p();
                  $comments[]=$comment;
                }
              }
            }
            $posts[$item['id']]=$item;
            break;
        }
      }
    }
    $items['pages']=$pages;
    $items['posts']=$posts;
    $items['comments']=$comments;
    return $items;
  }
  
  

  public function image_callback($matches) {
    $defaults=array('src'=>'','alt'=>'','title'=>'');
    $img=$matches[0];
    $attributes=xml2array($img);
    $attributes=$attributes['img_attr'];
    $attributes=array_merge($defaults,$attributes);
    // keep simple
    $attributes=array_keep_keys($attributes,array_keys($defaults));
    if (empty($attributes['alt'])) $attributes['alt']=$attributes['title'];
    if (empty($attributes['title'])) $attributes['title']=$attributes['alt'];
    // upload image
    $src=$attributes['src'];
    // first get the image
    $name=$this->CI->upload->download_and_add_file($src,'pictures','wp_');
    $newsrc=$this->CI->config->item('ASSETS').'pictures/'.$name;
    $attributes['src']=$newsrc;
    $newimg='<img ';
    foreach ($attributes as $key => $value) {
      $newimg.=' '.$key.'="'.$value.'"';
    }
    $newimg.='/>';
    return $newimg;
  }
  private function add_images($txt) {
    $txt = preg_replace_callback("/<img([^>]*)\/>/uiUsm", array($this,'image_callback'), $txt);
    return $txt;
  }
  
  
  private function get_uri($url) {
    $url=trim($url,'/');
    $uris=explode('/',$url);
    return $uris[count($uris)-1];
  }

	
}

?>