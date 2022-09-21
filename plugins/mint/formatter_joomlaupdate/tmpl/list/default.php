<h1>Generated Update XML</h1>

<p>Bellow is the list of generated special codes that you have to insert in to your install XML file.</p>

<style>
<!--
code.codepart {
	margin-bottom: 10px;
	border: 1px solid #E9E9E9;
	padding: 5px;
	background-color: #F9F9F9;
	border-radius:8px;
	display:block;
}
-->
</style>

<?php foreach ($view->items AS $list):?>
	<?php $url = preg_replace('/\/$/', '', JURI::root()).JRoute::_($list->url.'&formatter=joomlaupdate', false);?>
	<b><?php echo $list->title; ?></b>
	<code class="codepart">
		&lt;updateservers&gt;<br />
		&nbsp;&nbsp;	&lt;server type="extension" priority="1" name="<?php echo $this->params->get('server_name');?>"&gt;<br />
		&nbsp;&nbsp;&nbsp;&nbsp;		<a href="<?php echo $url;?>"><?php echo $url;?></a><br />
		&nbsp;&nbsp;	&lt;/server&gt;<br />
		&lt;/updateservers&gt;
	</code>
	<br />
<?php endforeach;?>