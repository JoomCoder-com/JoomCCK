<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckupload.php';

class JFormFieldCGallery extends CFormFieldUpload
{
	public function getInput()
	{
		$params['width']  = $this->params->get('params.original_width', 1024);
		$params['height'] = $this->params->get('params.original_height', 1024);

		$params['max_size']         = ($this->params->get('params.max_size', 4000) * 1024);
		$params['method']           = $this->params->get('params.method', 'auto');
		$params['max_count']        = $this->params->get('params.max_count', 0);
		$params['file_formats']     = $this->params->get('params.file_formats', 'jpg, png, gif, bmp, jpeg');
		$params['allow_edit_title'] = $this->params->get('params.allow_edit_title', 1);
		// 		$params['allow_add_descr'] = $this->params->get('params.allow_add_descr', 1);;
		if($this->params->get('params.show_mode', 'gallerybox') == 'gallerybox')
		{
			$params['allow_add_descr'] = $this->params->get('params.allow_add_descr', 1);
		}

		$this->options = $params;
		$this->upload  = parent::getInput();

		return $this->_display_input();
	}

	public function onRenderFull($record, $type, $section)
	{
		$this->_sort();

		return $this->_display_output('full', $record, $type, $section);
	}

	public function _sort()
	{

		$sort  = $this->params->get('params.sort', 0);
		$parts = explode(' ', $sort);
		if(!isset($parts[0]))
		{
			$parts[0] = 0;
		}

		if(!isset($parts[1]))
		{
			$parts[1] = 'ASC';
		}

		$sortArray = array();

		foreach($this->value as $val)
		{
			switch($parts[0])
			{
				case 0:
					$title       = $this->params->get('params.allow_edit_title', 0);
					$sortArray[] = strtolower($title && $val['title'] ? $val['title'] : $val['realname']);
					break;

				case 1:
					$sortArray[] = $val['size'];
					break;
				case 2:
					$sortArray[] = $val['hits'];
					break;
				case 3:
					$sortArray[] = $val['id'];
					break;
			}
		}

		if($sortArray)
		{
			array_multisort($sortArray, constant('SORT_' . $parts[1]), $this->value);
		}
	}

	public function _custom($client = 'full')
	{
		if(empty($this->value))
		{
			return;
		}

		$this->_sort();

		$f_key = $this->id . '-' . $this->record->id;

		$style = "
	    .image-element {
        	opacity:0;
        }
        .image-wrapper{
        	display: inline-block;
        }
        .images-row {
        	white-space:nowrap;
        	text-align:center;
        	overflow:hidden;
        }";

		JFactory::getDocument()->addStyleDeclaration($style);

		$rel = '';
		if($this->params->get('params.lightbox_click_' . $client, 0) == 0)
		{
			$rel = 'rel="lightbox" data-lightbox="' . $this->id . '_' . $this->record->id . '"';
			if($this->params->get('params.show_mode', 'gallerybox') == 'gallerybox')
			{
				$rel = 'rel="gallerybox' . $this->id . '_' . $this->record->id . '"';
			}
			if($this->params->get('params.show_mode', 'gallerybox') == 'rokbox')
			{
				$rel = 'data-rokbox data-rokbox-album="' . htmlentities($this->record->title, ENT_COMPAT, 'UTF-8') . '"';
			}
		}

		$patern_img = '<div class="image-wrapper" style="background-image:url(\'%s\');"><a id="%d" title="%s" href="%s" ' . $rel . ' data-title="%s"><img class="image-element" src="%s" border="0" title="%s" alt="%s" /></a></div>';
		$out        = array();

		$dir = JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR;

		foreach($this->value as $picture_index => $file)
		{
			$picture = $dir . $file['fullpath'];

			$options = array(
			'mode'       => $this->params->get('params.thumbs_mode', 1),
			'strache'    => $this->params->get('params.thumbs_stretch', 1),
			'background' => $this->params->get('params.thumbs_background_color', "#000000"),
			'quality'    => $this->params->get('params.thumbs_quality', 80)
			);
			$url =  CImgHelper::getThumb($picture, $this->params->get('params.thumbs_width', 100), $this->params->get('params.thumbs_height', 100), 'gallery' . $f_key, $this->record->user_id, $options);
			/********************/
			$options = array(
			'strache'    => $this->params->get('params.full_stretch', 1),
			'background' => $this->params->get('params.thumbs_background_color', "#000000"),
			'quality'    => $this->params->get('params.full_quality', 80)
			);
			$url_to_original =  CImgHelper::getThumb($picture, $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $f_key, $this->record->user_id, $options);

			$key = md5($picture.$this->params->get('params.full_width', 100).$this->params->get('params.full_height', 100).implode('-', $options));

			if($this->params->get('params.count_views'))
			{
				$url_to_original = sprintf('%s/index.php?option=com_joomcck&task=files.show&id=%d&user_id='.$this->record->user_id.'&fldr=gallery'.$f_key.'&file_key=%s&tmpl=component', JURI::root(TRUE), $file['id'], $key);
			}

			$title = htmlspecialchars(empty($file['title']) ? $file['realname'] : $file['title'], ENT_COMPAT, 'UTF-8');

			$out[] = sprintf($patern_img, $url, $picture_index, $title, $url_to_original, $title, $url, $title, $title);
		}
		$class = $this->params->get('core.field_class', FALSE);

		return '<div id="gallery' . $this->id . '" class="mainwrap' . ($class ? ' ' . $class : '') . '">' . implode('', $out) . "</div>";
	}

	public function _auto($client = 'full')
	{
		if(!$this->value)
		{
			return;
		}
		$total_width = $this->params->get('params.column_width');
		$max_per_row = $this->params->get('params.image_in_row');
		$padding     = $this->params->get('params.image_padding', 0);
		$max_height  = $this->params->get('params.max_height', 0);
		$count_files = count($this->value);

		$rows  = ceil($count_files / $max_per_row);
		$count = 0;
		$out   = array();

		for($i = 0; $i < $rows; $i++)
		{
			$base_height = (int)$this->value[$count]['height'];
			$base_width  = 0;
			$pictures    = array();

			for($c = 0; $c < $max_per_row; $c++)
			{
				if(!isset($this->value[$count]))
				{
					break;
				}
				$image = $this->value[$count];

				$base_width += $this->resizer->calculateByHeight($base_height, $image['height'], $image['width']);
				$pictures[$count] = $image;

				$count++;
			}
			$new_height = $this->resizer->calculateHeightProportionalByWidth($base_width, $base_height, $total_width, count($pictures), ($padding + $this->params->get('params.image_border', 0)));
			if($new_height > $max_height)
			{
				$new_height = $max_height;
			}

			$out[] = $this->_drawRow($pictures, $new_height, $client);
		}

		$this->_setGalleryStyle();
		$class = $this->params->get('core.field_class', FALSE);

		return '<div id="gallery' . $this->id . '" class="mainwrap' . ($class ? ' ' . $class : '') . '">' . implode('', $out) . "</div>";
	}

	private function _setGalleryStyle()
	{
		$total_width = $this->params->get('params.column_width');
		$padding     = $this->params->get('params.image_padding', 0);

		$border_radius_style = $image_shadow_style = '';
		if($this->params->get('params.image_border_radius', 0))
		{
			$border_radius_style =
				"-webkit-border-radius: " . $this->params->get('params.image_border_radius', 0) . "px;
			-moz-border-radius: " . $this->params->get('params.image_border_radius', 0) . "px;
			border-radius: " . $this->params->get('params.image_border_radius', 0) . "px;";
		}

		if($this->params->get('params.image_shadow', 0))
		{
			$image_shadow_style =
				"-webkit-box-shadow: " . $this->params->get('params.image_shadow') . ";
			-moz-box-shadow: " . $this->params->get('params.image_shadow') . ";
			box-shadow: " . $this->params->get('params.image_shadow') . ";";
		}

		$style = "
			#gallery{$this->id} {
				width:{$total_width}px;
				overflow:hidden;
			}
			#gallery{$this->id} .image-element {
				opacity:0;
			}
			#gallery{$this->id} div.image-wrapper{
				float:left;
				border:" . $this->params->get('params.image_border', 0) . "px solid " . $this->params->get('params.image_border_color', '#e0e0e0') . ";
				margin:{$padding}px !important;
				{$border_radius_style}
				{$image_shadow_style}
			}
			#gallery{$this->id} div.images-row {
				white-space:nowrap;
				text-align:center;
				overflow:hidden;
				width:" . ($total_width + 1) . "px;
		}";

		JFactory::getDocument()->addStyleDeclaration($style);

	}

	private function _drawRow($pictures, $height, $client)
	{
		$rel = '';
		if($this->params->get('params.lightbox_click_' . $client, 0) == 0)
		{
			$rel = 'data-lightbox="' . $this->id . '_' . $this->record->id . '"';
			if($this->params->get('params.show_mode', 'gallerybox') == 'gallerybox')
			{
				$rel = 'rel="gallerybox' . $this->id . '_' . $this->record->id . '"';
			}
			if($this->params->get('params.show_mode', 'gallerybox') == 'rokbox')
			{
				$rel = 'data-rokbox data-rokbox-album="' . htmlentities($this->record->title, ENT_COMPAT, 'UTF-8') . '"';
			}
		}

		$f_key = $this->id . '-' . $this->record->id;

		$patern_img   = '<div class="image-wrapper" style="width:%spx;background-size: cover;background-image:url(\'%s\');"><a id="%s" title="%s" href="%s" ' . $rel . '><img class="image-element" src="%s" border="0" /></a></div>';
		$patern_row   = '<div class="images-row" style="height:%dpx;" >%s</div>';
		$out          = array();
		$widths       = array();

		$dir = JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR;

		foreach($pictures as $picture_index => $file)
		{
			$picture = $dir . $file['fullpath'];

			$options = array(
			'mode'       => CImgHelper::RESIZE_HEIGHTBASED
			);

			$url[$picture_index] =  CImgHelper::getThumb($picture, 0, $height, 'gallery' . $f_key, $this->record->user_id, $options);

			$info                   = CImgHelper::getThumbSize();
			$widths[$picture_index] = $info[0];
			/*************/
			$options = array(
			'strache'    => $this->params->get('params.full_stretch', 1),
			'background' => $this->params->get('params.thumbs_background_color', "#000000"),
			'quality'    => $this->params->get('params.full_quality', 80),
			'mode'       => $this->params->get('params.full_mode', CImgHelper::RESIZE_HEIGHTBASED)
			);

			$url_to_original[$picture_index] =  CImgHelper::getThumb($picture, $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $f_key, $this->record->user_id, $options);

			$key = md5($picture.$this->params->get('params.full_width', 100).$this->params->get('params.full_height', 100).implode('-', $options));

			if($this->params->get('params.count_views'))
			{
				$url_to_original[$picture_index] = sprintf('%s/index.php?option=com_joomcck&task=files.show&id=%d&user_id='.$this->record->user_id.'&fldr=gallery'.$f_key.'&file_key=%s&tmpl=component', JURI::root(TRUE), $file['id'], $key);
			}
		}

		$widths_sum = array_sum($widths);
		$padding    = ($this->params->get('params.image_padding', 0) + $this->params->get('params.image_border', 0)) * 2;

		if(count($pictures) == $this->params->get('params.image_in_row', 3) && $this->params->get('params.column_width') > ($widths_sum + count($pictures) * $padding))
		{
			$last             = array_pop($widths);
			$sum_without_last = array_sum($widths);
			$widths[]         = $this->params->get('params.column_width') - ($sum_without_last + count($pictures) * $padding);
		}

		foreach($widths as $key => $width)
		{
			$out[] = sprintf($patern_img, $width, $url[$key], $key, $pictures[$key]['title'], $url_to_original[$key], $url[$key]);
		}

		return sprintf($patern_row, ($height + $padding), implode('', $out));
	}

	public function onRenderList($record, $type, $section)
	{
		$this->_sort();

		return $this->_display_output('list', $record, $type, $section);
	}

	public function onGetThumbs($post)
	{
		$this->_init();
		$this->_getRecord($post['record_id']);

		$dir = JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR;
		$f_key = $this->id . '-' . $this->record->id;


		$patern_img = '<div class="image-wrapper" style="background-image:url(\'%s\');"><a title="%s" href="%s" rel="gallerybox_ajax' . $this->id . '_' . $this->record->id . '"><img class="image-element" src="%s" border="0" /></a></div>';
		$out        = array();
		$subfolder  = $this->params->get('params.subfolder', $this->field_type);

		$this->_sort();

		foreach($this->value as $file)
		{
			$picture = $dir . $file['fullpath'];

			$options = array(
			'mode'       => $this->params->get('params.full_mode', CImgHelper::RESIZE_HEIGHTBASED)
			);

			$url =  CImgHelper::getThumb($picture, 0, 25, 'gallery' . $f_key, $this->record->user_id, $options);
			/****************/
			$options = array(
			'strache'    => $this->params->get('params.full_stretch', 1),
			'background' => $this->params->get('params.thumbs_background_color', "#000000"),
			'quality'    => $this->params->get('params.full_quality', 80)
			);

			$url_to_original =  CImgHelper::getThumb($picture, $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $f_key, $this->record->user_id, $options);

			$key = md5($picture.$this->params->get('params.full_width', 100).$this->params->get('params.full_height', 100).implode('-', $options));

			if($this->params->get('params.count_views'))
			{
				$url_to_original = sprintf('%s/index.php?option=com_joomcck&task=files.show&id=%d&user_id='.$this->record->user_id.'&fldr=gallery'.$f_key.'&file_key=%s&tmpl=component', JURI::root(TRUE), $file['id'], $key);
			}
			$out[] = sprintf($patern_img, $url, $file['realname'], $url_to_original, $url);
		}

		$result['text']  = implode('', $out);
		$result['count'] = count($this->value);

		return $result;
	}

	public function onGetImageInfo($post)
	{
		$this->_sort();

		if(!isset($post['image_index']))
		{
			AjaxHelper::error(JText::_('G_NOIMAGEINDEX'));

			return;
		}

		if(!isset($this->value[$post['image_index']]['id']))
		{
			AjaxHelper::error(JText::_('G_IMAGENOTININDEX'));

			return;
		}

		$this->user = JFactory::getUser();

		$this->_getFile($this->value[$post['image_index']]['id']);
		$this->_getRecord($post['record_id']);

		$out['title'] = $this->_getTitle();
		// 		$out['description'] = $this->_getDescription();

		$out['comments']     = $this->_getComments();
		$out['commentsform'] = $this->_getCommentsForm();

		$out['info']     = $this->_getInfo();
		$out['latlng']   = $this->_getLatLng();
		$out['rating']   = $this->_getRating();
		$out['download'] = $this->_getDownloadLink();

		return $out;
	}

	function onSaveData($post)
	{
		$this->_sort();
		if(!isset($post['image_index']))
		{
			AjaxHelper::error(JText::_('G_NOIMAGEINDEX'));

			return;
		}

		if(!isset($post['type']))
		{
			AjaxHelper::error(JText::_('G_SAVETYPENOTSET'));

			return;
		}

		if(!isset($post['context']))
		{
			AjaxHelper::error(JText::_('G_CONTEXTNOTSET'));

			return;
		}

		if(!isset($this->value[$post['image_index']]['id']))
		{
			AjaxHelper::error(JText::_('G_IMAGENOTININDEX'));

			return;
		}

		$files_table = JTable::getInstance('Files', 'JoomcckTable');
		$files_table->load($this->value[$post['image_index']]['id']);
		$this->_getRecord($post['record_id']);

		$out = JFilterOutput::cleanText($post['context']);
		$out = CensorHelper::cleanText($out);
		switch($post['type'])
		{
			case 'title':
				$files_table->title = $out;
				break;
			case 'description':
				$files_table->description = $out;
				break;
			case 'comment':
				$com             = array();
				$author_delete   = $this->params->get('params.comment_author', 0);
				$r_author_delete = $this->params->get('params.record_author', 0);
				$user            = JFactory::getUser();
				$date            = JFactory::getDate();
				$com['ctime']    = $date->toSql();
				$com['username'] = $user->get('username', 'guest');
				$com['user_id']  = $user->get('id', 0);
				$com['name']     = $user->get('name', 'guest');
				$com['comment']  = CensorHelper::cleanText($post['context']);
				$comments        = json_decode($files_table->comments, TRUE);
				$comments[]      = $com;
				end($comments);
				$k                     = key($comments);
				$files_table->comments = json_encode($comments);

				$delete_link = "<a href='javascript:void(0);' class=\"delete-comment\" onclick='gb" . $this->id . '_' . $this->record->id . ".deleteComment(" . $k . ");'></a>";
				$delete      = FALSE;

				if($com['user_id'] == $user->get('id') && $author_delete && $user->get('id'))
				{
					$delete = TRUE;
				}

				if($this->record->user_id == $user->get('id') && $r_author_delete && $user->get('id'))
				{
					$delete = TRUE;
				}

				$show_username = $this->params->get('params.show_username', 0);
				if($this->params->get('params.show_comment_avatar', 0))
				{
					$ava_link = CCommunityHelper::getAvatar($com['user_id'], $this->params->get('params.comment_avatar_width'), $this->params->get('params.comment_avatar_height'));
					$avatar   = "<div class='gallery-avatar'><img src='{$ava_link}' title='" . ($show_username ? $com[$show_username] : '') . "' alt='" . ($show_username ? $com[$show_username] : '') . "' /></div>";
				}

				if($show_username)
				{
					$user_str = JText::sprintf('G_POSTEDBY', $com[$show_username], $date->format('Y-m-d'));
				}
				else
				{
					$user_str = JText::sprintf('G_POSTEDON', $date->format('Y-m-d'));
				}

				$out = sprintf("{$avatar}%s<div class='commentUser'>%s</div>
                	<div class='commentText'>%s</div>", ($delete ? $delete_link : ''), $user_str, $out);
				$out = array('key' => $k, 'text' => $out);
				break;
		}

		$files_table->store();

		return $out;
	}

	public function onDeleteComment($post)
	{
		$this->_sort();

		if(!isset($post['context']))
		{
			AjaxHelper::error(JText::_('G_NOCOMMENTINDEX'));

			return;
		}

		$user = JFactory::getUser();

		if(!$user->get('id'))
		{
			AjaxHelper::error(JText::_('G_NOTAUTHORIZED'));

			return;
		}

		$id = $post['context'];

		$record_table = JTable::getInstance('Record', 'JoomcckTable');
		$record_table->load($post['record_id']);

		$file = $this->value[$post['image_index']];
		$this->_getFile($file['id']);

		$comments = json_decode($this->file->comments, TRUE);
		$comment  = $comments[$id];

		if(!$comment)
		{
			AjaxHelper::error(JText::_('G_COMMENTNOTFOUND'));

			return;
		}

		if(($user->get('id') && $user->get('id') == $record_table->user_id && $this->params->get('params.record_author', 1)) || (@$comment['user_id'] && @$comment['user_id'] == $user->get('id') && $this->params->get('params.comment_author', 1)))
		{
			unset($comments[$id]);
			$this->file->comments = json_encode($comments);
			$this->file->store();

			return 1;
		}

		AjaxHelper::error(JText::_('G_NORIGHTSTODELETECOMMENT'));

		return;
	}

	private function _getFile($id)
	{
		$this->file = JTable::getInstance('Files', 'JoomcckTable');
		$this->file->load($id);
	}

	private function _getRecord($id)
	{
		$this->record = ItemsStore::getRecord($id);
	}

	private function _getTitle()
	{
		$titlebox = $this->file->title;

		if(!$titlebox)
		{
			$titlebox = $this->file->realname;
		}

		if($this->file->user_id == $this->user->get('id') && $this->params->get('params.allow_edit_title', 0))
		{
			$titlebox = sprintf('<div id="titlebox"><span id="titletextspan">%s</span> <a href="javascript:void(0);" onclick="gb' . $this->id . '_' . $this->record->id . '.editTitle()">%s</a></div>', $titlebox,
				JHtml::image(JURI::root() . 'media/com_joomcck/icons/16/sticky-note--pencil.png', JText::_('G_EDIT'), array('align' => 'absmiddle')));
			$titlebox .= '<div id="titleedit" class="disactiveInput">
    	    	<input id="savetitletext" class="form-control" value="">
        	    <a href="javascript:void(0);" onclick="gb' . $this->id . '_' . $this->record->id . '.saveTitle();">' . JHtml::image(JURI::root() . 'media/com_joomcck/icons/16/disk.png', JText::_('G_SAVE'), array('align' => 'absmiddle')) . '</a></div>';
		}
		$avatar = '';
		if($this->params->get('params.show_avatar', 0))
		{
			$ava_link = CCommunityHelper::getAvatar($this->record->user_id, $this->params->get('params.avatar_width'), $this->params->get('params.avatar_height'));
			$avatar   = "<div class='gallery-avatar'><img src='{$ava_link}' /></div>";
		}
		$name = '';
		if($this->params->get('params.show_username', 0))
		{
			$section = ItemsStore::getSection($this->record->section_id);
			$name    = CCommunityHelper::getName($this->record->user_id, $section);
			$name    = "<div class='gallery-name'>{$name}</div>";
		}

		return $avatar . $name . '<div id="titletext" style="float:left; width:200px; height:40px; overflow-x: hidden;">' . $titlebox . '</div>';

	}

	private function _getDescription()
	{
		$description = $this->file->description;

		if($this->file->user_id == $this->user->get('id'))
		{
			$description = '<div id="descrbox"><span id="descrtextspan">' . $description . '</span>
	        	<a href="javascript:void(0);" onclick="Gallerybox.editDescription();">' . JText::_('G_EDIT') . '</a></div>

	        	<div id="descredit" class="disactiveInput">
    	    		<input id="savedescrtext" class="form-control" size="40">
        	    	<a href="javascript:void(0);" onclick="Gallerybox.saveDescription();">
        	    		' . JText::_('G_SAVE') . '
        	    	</a>
        		</div>
        	';
		}

		return '<div id="descrtext">' . $description . '</div>';
	}

	private function _getComments()
	{
		if(!$this->params->get('params.allow_comments', 0))
		{
			return 0;
		}
		$comment_li = array();

		$comments = json_decode($this->file->comments);

		if(!empty($comments))
		{
			$author_delete   = $this->params->get('params.comment_author', 0);
			$r_author_delete = $this->params->get('params.record_author', 0);
			$user            = JFactory::getUser();

			foreach($comments as $key => $comment)
			{
				$date = JFactory::getDate($comment->ctime);
				$date = $date->format('Y-m-d');

				$delete_link = "<a href='javascript:void(0);' class=\"delete-comment\" onclick='gb" . $this->id . '_' . $this->record->id . ".deleteComment({$key});'></a>";
				$delete      = FALSE;

				if(@$comment->user_id == $user->get('id') && $author_delete && $user->get('id'))
				{
					$delete = TRUE;
				}

				if($this->record->user_id == $user->get('id') && $r_author_delete && $user->get('id'))
				{
					$delete = TRUE;
				}
				$avatar        = '';
				$show_username = $this->params->get('params.show_username', 0);
				if($this->params->get('params.show_comment_avatar', 0))
				{
					$ava_link = CCommunityHelper::getAvatar(@$comment->user_id, $this->params->get('params.comment_avatar_width'), $this->params->get('params.comment_avatar_height'));
					$avatar   = "<div class='gallery-avatar'><img src='{$ava_link}' title='" . ($show_username ? $comment->$show_username : '') . "' alt='" . ($show_username ? $comment->$show_username : '') . "' /></div>";
				}

				if($show_username)
				{
					$user_str = JText::sprintf('G_POSTEDBY', $comment->$show_username, $date);
				}
				else
				{
					$user_str = JText::sprintf('G_POSTEDON', $date);
				}

				$comment_li[$key] = sprintf("<li id='comment{$key}'>{$avatar}" . ($delete ? $delete_link : '') . "
                	 <div class='commentUser'>%s</div>
                	<div class='commentText'>%s</div></li>", $user_str, $comment->comment);
			}
		}

		return implode('', $comment_li);
	}

	private function _getCommentsForm()
	{
		if(in_array($this->params->get('params.allow_comments'), $this->user->getAuthorisedViewLevels()))
		{
			return '<textarea id="savecommenttext" class="form-control"></textarea>
				<a class="btn btn-sm btn-light border" href="javascript:void(0);" onclick="if($(\'savecommenttext\').value == \'\'){alert(\'' . JText::_('CCOMMENT_EMPTY') . '\');return false;}gb' . $this->id . '_' . $this->record->id . '.saveComment();">
        	    ' . JText::_('G_SAVE') . '</a>';
		}
		else
		{
			return 0;
		}
	}

	private function _getLatLng()
	{
		$data = new JRegistry();
		$exif = json_decode($this->file->params, TRUE);

		if(!isset($exif["GPSLatitudeRef"]))
		{
			return array('lat' => 0, 'lng' => 0);
		}

		$LatM = $LongM = 1;
		if($exif["GPSLatitudeRef"] == 'S')
		{
			$LatM = -1;
		}
		if($exif["GPSLongitudeRef"] == 'W')
		{
			$LongM = -1;
		}
		$gps['LatDegree']   = $exif["GPSLatitude"][0];
		$gps['LatMinute']   = $exif["GPSLatitude"][1];
		$gps['LatgSeconds'] = $exif["GPSLatitude"][2];
		$gps['LongDegree']  = $exif["GPSLongitude"][0];
		$gps['LongMinute']  = $exif["GPSLongitude"][1];
		$gps['LongSeconds'] = $exif["GPSLongitude"][2];

		foreach($gps as $key => $value)
		{
			$pos = strpos($value, '/');
			if($pos !== FALSE)
			{
				$temp      = explode('/', $value);
				$gps[$key] = $temp[0] / $temp[1];
			}
		}

		//calculate the decimal degree
		$result['lat'] = $LatM * ($gps['LatDegree'] + ($gps['LatMinute'] / 60) + ($gps['LatgSeconds'] / 3600));
		$result['lng'] = $LongM * ($gps['LongDegree'] + ($gps['LongMinute'] / 60) + ($gps['LongSeconds'] / 3600));

		return $result;
	}

	private function _getInfo()
	{
		// 	    if(!$this->file->params) return;

		$data = new JRegistry($this->file->params);
		// 	    $data->loadJSON($this->file->params);
		$user = JFactory::getUser();
		$td   = $out = array();

		$td[JText::_('G_SIZE')] = $this->file->width . 'x' . $this->file->height . ' (' . HTMLFormatHelper::formatSize($this->file->size) . ')';
		if($this->params->get('params.count_views'))
		{
			$td[JText::_('G_VIEWS')] = (int)$this->file->views;
		}

		if(in_array($this->params->get('params.allow_download'), $user->getAuthorisedViewLevels()))
		{
			$td[JText::_('G_DOWNLOADS')] = $this->file->hits;
		}
		if($data->get('Make'))
		{
			$td[JText::_('G_DEVICE')] = $data->get('Make') . ' ' . $data->get('Model');
		}

		if($data->get('FileDateTime'))
		{
			$td[JText::_('G_CREATED')] = date('Y-m-d', $data->get('FileDateTime'));
		}

		if($data->get('COMPUTED.ApertureFNumber'))
		{
			$td[JText::_('G_APERTURE')] = $data->get('COMPUTED.ApertureFNumber');
		}
		if($data->get('ExposureTime'))
		{
			$fl                             = explode('/', $data->get('ExposureTime'));
			$td[JText::_('G_EXPOSURETIME')] = round(($fl[0] / $fl[1]) * 1000, 2) . ' s';
		}
		if($data->get('ShutterSpeedValue'))
		{
			$fl                             = explode('/', $data->get('ShutterSpeedValue'));
			$td[JText::_('G_SHUTTERSPEED')] = round(($fl[0] / $fl[1]), 2);
		}
		if($data->get('FocalLength'))
		{
			$fl                            = explode('/', $data->get('FocalLength'));
			$td[JText::_('G_FOCALLENGTH')] = $fl[0] / $fl[1] . ' mm';
		}
		if($data->get('BrightnessValue'))
		{
			$fl                           = explode('/', $data->get('BrightnessValue'));
			$td[JText::_('G_BRIGHTNESS')] = round(($fl[0] / $fl[1]), 2);
		}
		if($data->get('ISOSpeedRatings'))
		{
			$td[JText::_('G_ISOSPEED')] = $data->get('ISOSpeedRatings');
		}

		$td[JText::_('Flash Used')] = ($data->get('Flash') ? JText::_('Yes') : JText::_('No'));

		if($data->get('XResolution'))
		{
			//$td[JText::_('G_RESOLUTION')] = (int)$data->get('XResolution').'x'.(int)$data->get('YResolution');
		}

		$out[] = '<table class="tbl" align="center" width="100%">';
		foreach($td AS $label => $value)
		{
			$out[] = sprintf('<tr><td class="tblbl">%s</td><td>%s</td></tr>', $label, $value);
		}
		$out[] = '</table>';

		return implode(' ', $out);
	}

	private function _getRating()
	{
		if($this->params->get('params.rate_access', 1) == 0)
		{
			return ' ';
		}
		$can = RatingHelp::canRate('file', $this->file->user_id, $this->file->id, $this->params->get('params.rate_access', 1));

		return RatingHelp::loadRating($this->params->get('params.tmpl_rating', 'default'), round(@$this->file->rating), $this->file->id, 0, 'FileRatingCallBack', $can);
	}

	private function _getDownloadLink()
	{
		$user = JFactory::getUser();
		if(!in_array($this->params->get('params.allow_download', 1), $user->getAuthorisedViewLevels()))
		{
			return '';
		}
		$link  = $this->getDownloadUrl($this->record, $this->file, 0);
		$out[] = '<div class="download-button"><img src="' . JURI::root(TRUE) . '/media/com_joomcck/icons/16/disk.png" alt="Download" />';
		$out[] = '<a href="' . $link . '">' . JText::_('G_DOWNLOAD');
		$out[] = sprintf('<div>%dx%d (%s)</div>', $this->file->width, $this->file->height, HTMLFormatHelper::formatSize($this->file->size));
		$out[] = '</a></div>';

		return implode('', $out);
	}

	public function _init()
	{
		jimport('mint.resizeimage');
		$this->resizer = new JS_Image_Resizer();

		$allow_comments = $this->params->get('params.allow_comments', 0);
		$allow_info     = $this->params->get('params.allow_info', 0);
		$show_rate      = $this->params->get('params.rate_access', 0);

		if($this->request->getCmd('tmpl') != 'component')
		{
			if($this->params->get('params.show_mode', 'gallerybox') == 'gallerybox')
			{
				MapHelper::loadGoogleMapAPI();
				JFactory::getDocument()->addScript(JURI::root(TRUE) . '/components/com_joomcck/fields/gallery/gallerybox/gallerybox.js');
				RatingHelp::loadFile();
				JFactory::getDocument()->addStyleSheet(JURI::root(TRUE) . '/components/com_joomcck/fields/gallery/gallerybox/gallerybox.css');
				JFactory::getDocument()->addStyleSheet(JURI::root(TRUE) . '/components/com_joomcck/fields/gallery/gallerybox/gallerybox-' . $this->params->get('params.theme', 'Dark') . '.css');
				JFactory::getDocument()->addScriptDeclaration('
	    	    	window.addEvent("domready", function(){
		    	    	gb' . $this->id . '_' . $this->record->id . ' = new Gallerybox(
		    	    		{
	    	    				field_id:' . $this->id . ',
	    	    				record_id:' . $this->record->id . ',
	    	    				show_comments:' . $allow_comments . ',
	    	    				show_info:' . $allow_info . ',
	    	    				httpurl: Joomcck.field_call_url,
	    	    				show_location:' . $this->params->get('params.show_location', 1) . ',
	    	    				show_rate:' . $show_rate . ',
	    	    				texts:{
									counter:"' . JText::_('CCOUNTER') . '",
	    	    					sure: "' . JText::_('CSURE') . '"
	    						}
	    	    			}
	    	    		);
	    			});
				');
			}
			else
			{
				JHtml::_('lightbox.init', $this->id);
			}
		}

		$this->path = JPATH_ROOT . DIRECTORY_SEPARATOR . JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . 'thumbs_cache' . DIRECTORY_SEPARATOR;
		if(!JFolder::exists($this->path))
		{
			JFolder::create($this->path, 0755);
			$index = '<html><body></body></html>';
			JFile::write($this->path . DIRECTORY_SEPARATOR . 'index.html', $index);
		}

		$root      = JPath::clean(JComponentHelper::getParams('com_joomcck')->get('general_upload'));
		$url       = str_replace(JPATH_ROOT, '', $root);
		$url       = str_replace("\\", '/', $url);
		$url       = preg_replace('#^\/#iU', '', $url);
		$this->url = JURI::root(TRUE) . '/' . str_replace("//", "/", $url);
	}
}