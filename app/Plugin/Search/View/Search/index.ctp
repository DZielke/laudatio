<?php
$this->set('navbarArray',array(array('Search','search')));

//echo '<span id="objects_url" style="display:none">'.Router::url(array('controller' => 'XMLObjects','action' => 'objects')).'<span>';
$objects_url = Router::url(array('plugin' => null, 'admin' => false, 'controller' => 'XMLObjects','action' => 'corpus'));
echo $this->Html->script('/search/js/jquery-1.7.1.min.js',array('inline' => false));
echo $this->Html->script('/search/js/bootstrap.min.js',array('inline' => false));
echo $this->Html->script('/search/js/jquery.linkify-1.0-min.js',array('inline' => false));
echo $this->Html->script('/search/js/jquery-ui-1.8.18.custom.min.js',array('inline' => false));

echo $this->Html->script('/search/js/jquery.facetview_300113.js',array('inline' => false));
#echo $this->Html->script('{% if request.is_secure %}https{% else %}http{% endif %}://{{ request.get_host }}/search/js/jquery.facetview_300113.js',array('inline' => false));

echo $this->Html->script('/search/js/jquery.truncatorMod.js',array('inline' => false));

echo $this->Html->css('/search/css/facetview.css',null,array('inline' => false));
echo $this->Html->css('/search/css/bootstrapMod.css',null,array('inline' => false));
echo $this->Html->css('/search/css/jquery-ui-1.8.18.custom.css',null,array('inline' => false));

echo $this->Html->script('/search/js/index.js',array('inline' => false));

?>
<script language="javascript" type="text/javascript">
    objects_url = '<?php echo $objects_url; ?>';
</script>
<!-- <!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>LAUDATIO Suche</title>

  <script type="text/javascript" src="vendor/jquery/1.7.1/jquery-1.7.1.min.js"></script>

  <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
  <script type="text/javascript" src="vendor/bootstrap/js/bootstrap.min.js"></script>  

  <script type="text/javascript" src="vendor/linkify/1.0/jquery.linkify-1.0-min.js"></script>  
  
  <link rel="stylesheet" href="vendor/jquery-ui-1.8.18.custom/jquery-ui-1.8.18.custom.css">
  <script type="text/javascript" src="vendor/jquery-ui-1.8.18.custom/jquery-ui-1.8.18.custom.min.js"></script>

  <script type="text/javascript" src="jquery.facetview_300113.js"></script>
  <script type="text/javascript" src="jquery.truncatorMod.js"></script>

  <link rel="stylesheet" href="css/facetview.css">

  <script type="text/javascript">

  </script>
-->
<div class="facet-view-simple"></div>


