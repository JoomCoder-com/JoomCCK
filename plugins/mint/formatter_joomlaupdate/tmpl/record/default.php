<?php

$name = htmlentities($view->item->title, ENT_QUOTES, 'utf-8');
$url = preg_replace('/\/$/', '', JURI::root()).JRoute::_(Url::record($view->item));

$fields = $view->item->fields;
$version = $fields[$this->params->get('field_version')];
if(!$version)
{
	echo "No Version";
	return;
}
$tag = implode('', $fields[$this->params->get('field_tag')]);
if(!$tag)
{
	echo "No Tag";
	return;
}
$element = implode('', $fields[$this->params->get('field_element')]);
if(!$element)
{
	echo "No Element";
	return;
}
$type = implode('', (array)$fields[$this->params->get('field_type')]);
if(!$type)
{
	echo "No Type";
	return;
}

$folder = @$fields[$this->params->get('field_folder')];
if($type == 'plugin' && !$folder)
{
	echo "No Folder";
	return;
}

$client = implode('', (array)@$fields[$this->params->get('field_client')]);

if($type == 'plugin' && !$folder)
{
	//echo "No Folder";
	//return;
}

$files = (array)$fields[$this->params->get('field_down')];

if(isset($files['files']))
{
	$files = $files['files'];
}

$file = array_shift($files);
if(!$file)
{
	echo "No file";
	return;
}
$download = sprintf('%s/index.php?option=com_joomcck&amp;task=files.download&amp;no_html=1&amp;id=%d&amp;fid=%d&amp;fidx=0&amp;rid=%d',
	JURI::root(), $file['id'], $this->params->get('field_down'), $view->item->id);


$description = htmlentities(@$fields[$this->params->get('field_descr')], ENT_COMPAT, 'utf-8');

header('content-type:text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<updates>
  <update>
     <name><?php echo strip_tags($name);?></name>
     <description><![CDATA[ <?php echo $description?> ]]></description>
     <element><?php echo $element;?></element>
     <type><?php echo $type;?></type>
     <version><?php echo $version;?></version>
     <?php if($folder):?>
       <folder><?php echo $folder;?></folder>
     <?php endif;?>
     <?php if($client):?>
       <client><?php echo strtolower($client);?></client>
     <?php endif;?>
     <infourl title="<?php echo strip_tags($name);?>"><?php echo $url;?></infourl>
     <downloads>
         <downloadurl type="full" format="zip"><?php echo $download;?></downloadurl>
     </downloads>
     <tags>
         <tag><?php echo $tag;?></tag>
     </tags>
     <maintainer><?php echo $this->params->get('maintainer'); ?></maintainer>
     <maintainerurl><?php echo $this->params->get('maintainerurl'); ?></maintainerurl>
     <targetplatform name="joomla" version="<?php echo $this->params->get('targetplatform'); ?>"/>
  </update>
</updates>
<?php exit;?>