var Joomcck = {};
var _gaq = _gaq || [];

(function($) {
	var floatnum = [];

	Joomcck.typeahead = function(el, post, options) {
		if(!$(el).length) return;

		var labels = [], mapped = [];

		options = options || {};

		$(el).typeahead({
			items:   options.limit || 10,
			source:  function(query, process) {
				post.q = query;
				post.limit = options.limit || 10;
				return $.get(options.url || Joomcck.field_call_url, post, function(data) {
					if(!data) return;
					if(!data.result) return;

					labels[el] = [];
					mapped[el] = {};

					$.each(data.result, function(i, item) {
						mapped[el][item.label] = item.value
						labels[el].push(item.label)
					});

					return process(labels[el]);
				}, 'json');
			},
			updater: function(item) {
				return mapped[el][item];
			}
		});
	};

	Joomcck.formatInt = function(el) {
		var cur = el.value;
		reg = /[^\d]+/;
		cur = cur.replace(reg, "");
		el.value = cur;
	};

	Joomcck.formatFloat = function(obj, decimal, max, val_max, val_min, field_id, msg) {

		if(floatnum[obj.id] == obj.value) {
			return;
		}

		var cur = obj.value;

		cur = cur.replace(',', '.');
		cur = cur.replace('..', '.');

		var sign = '';

		if(cur.indexOf('-') == 0) {
			sign = '-';
			cur = cur.substr(1, cur.length);
		} else if(cur.indexOf('+') == 0) {
			sign = '+';
			cur = cur.substr(1, cur.length);
		}

		if(decimal > 0) {
			reg = /[^\d\.]+/;
		} else {
			reg = /[^\d]+/;
		}
		cur = cur.replace(reg, '');

		cur = sign + cur;

		if((cur.lastIndexOf('.') >= 0) && (cur.indexOf('.') > 0) && (cur.indexOf('.') < cur.lastIndexOf('.'))) {
			reg2 = /\.$/;
			cur = cur.replace(reg2, '');
		}

		if(cur) {

			var myRe = /^([^\.]+)(.*)/i;
			var myArray = myRe.exec(cur);
			number = myArray[1];
			rest = myArray[2];

			if(number.length > decimal) {
				cur = number.substr(0, max) + rest;
			}

			if(decimal > 0 && (cur.indexOf('.') > 0)) {
				myRe = /([^\.]+)\.([^\.]*)/i;
				myArray = myRe.exec(cur);
				number = myArray[1];
				float = myArray[2];

				if(float.length > decimal) {
					cur = number + '.' + float.substr(0, decimal);
				}
			}

			if(val_max && val_min) {
				if(parseFloat(cur) > val_max) {
					cur = val_max;
					Joomcck.fieldError(field_id, msg);
				}
				if(parseFloat(cur) < val_min) {
					//cur = val_min;
					Joomcck.fieldError(field_id, msg);
				}
			}
		}

		obj.value = cur;
		floatnum[obj.id] = obj.value;
	};

	Joomcck.redrawBS = function() {

		// load tooltips with tooltip rel everywhere
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('*[rel^="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		})


		//$('').tooltip();
		// $('*[rel="popover"]').popover();
		//$('.tip-bottom').tooltip({placement: "bottom"});

		$('.radio.btn-group label').addClass('btn');
		$(".btn-group label:not(.active)").click(function() {
			var label = $(this);
			var input = $('#' + label.attr('for'));

			if(!input.prop('checked')) {
				label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
				if(input.val() == '') {
					label.addClass('active btn-primary');
				} else if(input.val() == 0) {
					label.addClass('active btn-danger');
				} else {
					label.addClass('active btn-success');
				}
				input.prop('checked', true);
			}
		});
		$(".btn-group input[checked=checked]").each(function() {
			if($(this).val() == '') {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
			} else if($(this).val() == 0) {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
			} else {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
			}
		});
	};

	Joomcck.setAndSubmit = function(el, val) {
		var elm = $('#' + el);
		elm.val(val).attr('selected', true);
		elm.parents('form').submit();
	};

	Joomcck.checkAndSubmit = function(el) {
		var elm = $(el);
		elm.attr('checked', true);
		setTimeout(function() {
			elm.parents('form').submit();
		}, 200);
	};

	Joomcck.yesno = function(yes, no) {
		var y = $(yes);
		var n = $(no);
		y.on('click', function() {
			y.addClass('btn-primary').removeClass('btn-light');
			n.removeClass('btn-primary').addClass('btn-light');
			$('input[type="radio"]', n).removeAttr('checked', 'checked');
			$('input[type="radio"]', y).attr('checked', 'checked');
		});
		n.on('click', function() {
			n.addClass('btn-primary').removeClass('btn-light');;
			y.removeClass('btn-primary').addClass('btn-light');
			$('input[type="radio"]', y).removeAttr('checked', 'checked');
			$('input[type="radio"]', n).attr('checked', 'checked');
		});

	}

	Joomcck.hidehead = function() {
		$('header.header').css('display', 'none');
		$('div.container-main').css('margin-top', '55px');
	};

	/**
	 * This method allow you create link or toolbar button to make an action on
	 * selected records.
	 *
	 * @param task
	 *            Joomla task. eg: resords.delete
	 */
	Joomcck.submitTask = function(task) {
		if($('input[name="boxchecked"]').val() == 0) {
			alert('Please first make a selection from the list');
		} else {
			Joomla.submitbutton(task);
		}
	};

	Joomcck.orderTable = function(ordr) {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if(order != ordr) {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	};

	Joomcck.addTmplEditLink = function(type, field_id, inside, root) {
		var el = $('#' + field_id + "_link");
		el.html('');
		$('select[id=' + field_id + '] option:selected').each(function() {
			if(this.value == 0) return true;
			var config = this.value.split('.')[1];
			var name = this.value.split('.')[0];
			var btn = $(document.createElement('button'))
				.attr({
					'class': 'ms-2 btn btn-light border',
					'type':  'button'
				})
				.html('<i class="icon-options"></i> ' + name)
				.bind('click', function() {
					var url = root + 'index.php?option=com_joomcck&view=templates&layout=form&cid[]=[' + name + '],[' + type + ']&config=' + config + '&tmpl=component';
					if(inside == 'component') {
						url += '&inner=1';
						window.location = url;
					}
					else {

						$('<div id="ejbIframeModal"></div>').appendTo('body');

						$('#ejbIframeModal').iziModal({
							title: name,
							overlayClose: true,
							fullscreen: true,
							closeButton: true,
							width: '60%',
							iframe: true,
							iframeHeight: '600',
							iframeURL: url,
							'onClosed': function(){
								$('#ejbIframeModal').remove();
							}
						});

						$('#ejbIframeModal').iziModal('open');


					}
				});

			el.append(btn);



			el.append('<br>');
		});
	};

	Joomcck.CleanCompare = function(return_url, section) {
		$('#compare').slideUp('fast');
		$.ajax({
			url:  '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.compareclean&tmpl=component", FALSE); ?>',
			type: 'POST',
			data:     {
				section_id:  section
			}
		}).done(function(json) {
			if(return_url) {
				window.location = return_url;
			}
			$("a[id^=compare_]").each(function() {
				$(this).show();
				$(this).removeClass('hide');
				$(this).removeAttr('style');
			});
		});
	};

	Joomcck.CompareRecord = function(id, section) {
		var button = $('#compare_' + id);
		$('img', button).attr('src', '<?php echo JURI::root(TRUE);?>/components/com_joomcck/images/load.gif');

		$.ajax({
			url:      '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.compare&tmpl=component", FALSE); ?>',
			dataType: 'json',
			type:     'POST',
			data:     {
				record_id:  id,
				section_id: section
			}
		}).done(function(json) {
			button.hide();

			if(!json) {
				return;
			}

			$('img', button).attr('src', '<?php echo JURI::root(TRUE);?>/media/mint/icons/16/edit-diff.png');
			$('#compare div.alert').removeClass('alert-warning').addClass('alert-info');

			$('#compare').slideDown('fast', function() {
				$('html, body').animate({scrollTop: $("#compare").offset().top}, 500);
			});

			if(!json.success) {
				$('#compare div.alert h4').html(json.error);
				$('#compare div.alert').addClass('alert-warning').removeClass('alert-info');
				//alert(json.error);
				return;
			}

			$('#compare div.alert h4').html('<?php echo JText::sprintf("CCOMPAREMSG", "' + json.result + '") ?>');

			if(_gaq) {
				_gaq.push(['_trackEvent', 'Compare', id]);
			}
		});
	};
	Joomcck.RepostRecord = function(id, section) {
		var button = $('#repost_' + id);
		$('img', button).attr('src', '<?php echo JURI::root(TRUE);?>/components/com_joomcck/images/load.gif');

		$.ajax({
			url:      '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.repost&tmpl=component", FALSE); ?>',
			dataType: 'json',
			type:     'POST',
			data:     {
				record_id:  id,
				section_id: section
			}
		}).done(function(json) {
			button.hide();

			if(!json) {
				return;
			}
			if(!json.success) {
				alert(json.error);
				return;
			}

			if(_gaq) {
				_gaq.push(['_trackEvent', 'Repost', id]);
			}
		});
	};

	Joomcck.followRecord = function(id, section) {
		$('#follow_record_' + id).attr('src', '<?php echo JURI::root(TRUE);?>/components/com_joomcck/images/load.gif');
		$.ajax({
			url:      '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.follow&tmpl=component", FALSE); ?>',
			context:  $('#follow_record_' + id),
			dataType: 'json',
			type:     'POST',
			data:     {
				record_id:  id,
				section_id: section
			}
		}).done(function(json) {
			if(!json) {
				return;
			}
			if(!json.success) {
				alert(json.error);
				return;
			}
			$(this)
				.attr('src', '<?php echo JURI::root(TRUE);?>/media/mint/icons/16/follow' + json.state + '.png')
				.attr('data-original-title', json.title);
			Joomcck.redrawBS();

			if(_gaq) {
				_gaq.push(['_trackEvent', 'Follow', json.state == 1 ? 'Unfollow Record' : 'Follow Record', json.rtitle]);
			}
		});
	};

	Joomcck.bookmarkRecord = function(id, img) {
		$('#bookmark_' + id).attr('src', '<?php echo JURI::root(TRUE);?>/components/com_joomcck/images/load.gif');
		$.ajax({
			url:      '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.bookmark&tmpl=component", FALSE); ?>',
			context:  $('#bookmark_' + id),
			dataType: 'json',
			type:     'POST',
			data:     {
				record_id: id
			}
		}).done(function(json) {
			if(!json) {
				return;
			}
			if(!json.success) {
				alert(json.error);
				return;
			}
			$(this)
				.attr('src', '<?php echo JURI::root(TRUE);?>/media/mint/icons/bookmarks/' + img + '/state' + json.state + '.png')
				.attr('data-original-title', json.title);
			Joomcck.redrawBS();
			if(_gaq) {
				_gaq.push(['_trackEvent', 'Bookmark', json.state == 1 ? 'Remove' : 'Add', json.rtitle]);
			}
		});
	};

	Joomcck.followSection = function(id) {
		$('#follow_' + id).attr('src', '<?php echo JURI::root(TRUE);?>/components/com_joomcck/images/load.gif');

		$.ajax({
			url:      '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.followsection&tmpl=component", FALSE); ?>',
			context:  $('#follow_' + id),
			dataType: 'json',
			type:     'POST',
			data:     {
				section_id: id
			}
		}).done(function(json) {
			if(!json) {
				return;
			}
			if(!json.success) {
				alert(json.error);
				return;
			}
			$('#follow_' + id).attr('src', '<?php echo JURI::root(TRUE);?>/media/mint/icons/16/follow' + (json.state.toInt() ? 0 : 1) + '.png');
			$('#followtext_' + id).html(json.title);
			if(json.state == 0) {
				$('#followsec-' + id).addClass('btn-primary').bind('mouseleave',function() {
					$(this).removeClass('btn-danger').addClass('btn-primary');
					$('#followtext_' + id).html('<?php echo JText::_("CFOLLOWINGSECION");?>');
				}).bind('mouseenter', function() {
					$(this).removeClass('btn-primary').addClass('btn-danger');
					$('#followtext_' + id).html('<?php echo JText::_("CSECUNFOLLOW");?>');
				});
			}
			else {
				$('#followsec-' + id).removeClass('btn-primary btn-danger').unbind('mouseenter mouseleave').prop("onmouseover", null).prop("onmouseout", null);
			}
			if(_gaq) {
				_gaq.push(['_trackEvent', 'Follow', json.state == 1 ? 'Unfollow Section' : 'Follow Section', json.name]);
			}
		});
	};

	Joomcck.followUser = function(id, section) {
		$('#followuser_' + id).attr('src', '<?php echo JURI::root(TRUE);?>/components/com_joomcck/images/load.gif');
		$.ajax({
			url:      '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.followuser&tmpl=component", FALSE); ?>',
			dataType: 'json',
			type:     'POST',
			data:     {
				user_id:    id,
				section_id: section
			}
		}).done(function(json) {
			if(!json) {
				return;
			}
			if(!json.success) {
				alert(json.error);
				return;
			}
			$('#followuser_' + id).attr('src', '<?php echo JURI::root(TRUE);?>/media/mint/icons/16/follow' + (json.state.toInt() ? 0 : 1) + '.png');
			$('#followtext_' + id).html(json.title);
			if(json.state == 0) {
				$('#followuser-' + id).addClass('btn-primary').bind('mouseleave',function() {
					$(this).removeClass('btn-danger').addClass('btn-primary');
					$('#followtext_' + id).html(json.title);
				}).bind('mouseenter', function() {
					$(this).removeClass('btn-primary').addClass('btn-danger');
					$('#followtext_' + id).html(json.title2);
				});
			}
			else {
				$('#followuser-' + id).removeClass('btn-primary btn-danger').unbind('mouseenter mouseleave').prop("onmouseover", null).prop("onmouseout", null);
			}
			if(_gaq) {
				_gaq.push(['_trackEvent', 'Follow', json.state == 1 ? 'Unfollow User' : 'Follow User', json.name]);
			}
		});
	};

	Joomcck.followCat = function(id, section) {
		$('#follow_' + id).attr('src', '<?php echo JURI::root(TRUE);?>/components/com_joomcck/images/load.gif');

		$.ajax({
			url:      '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.followcat&tmpl=component", FALSE); ?>',
			dataType: 'json',
			type:     'POST',
			data:     {
				cat_id:     id,
				section_id: section
			}
		}).done(function(json) {
			if(!json) {
				return;
			}
			if(!json.success) {
				alert(json.error);
				return;
			}
			$('#follow_' + id).attr('src', '<?php echo JURI::root(TRUE);?>/media/mint/icons/16/follow' + (json.state.toInt() ? 0 : 1) + '.png');
			$('#followtext_' + id).html(json.title);
			if(json.state == 0) {
				$('#followcat-' + id).addClass('btn-primary').bind('mouseleave',function() {
					$(this).removeClass('btn-danger').addClass('btn-primary');
					$('#followtext_' + id).html('<?php echo JText::_("CCATFOLLOWING");?>');
				}).bind('mouseenter', function() {
					$(this).removeClass('btn-primary').addClass('btn-danger');
					$('#followtext_' + id).html('<?php echo JText::_("CCATUNFOLLOW");?>');
				});
			}
			else {
				$('#followcat-' + id).removeClass('btn-primary btn-danger').unbind('mouseenter mouseleave').prop("onmouseover", null).prop("onmouseout", null);
			}

			if(_gaq) {
				_gaq.push(['_trackEvent', 'Follow', json.state == 1 ? 'Unfollow Category' : 'Follow Category', json.name]);
			}
		});
	};

	Joomcck.ItemRatingCallBackMulti = function(vote, ident, index) {
		Joomcck.ItemRatingCallBackSingle(vote, ident, index, true);
	};

	Joomcck.ItemRatingCallBackSingle = function(vote, ident, index, multi) {
		var old_html = $('#rating-text-' + ident).html();

		$('#rating-text-' + ident).addClass('progress progress-striped active').html('<div class="bar" style="width: 100%;"><?php echo JText::_("CPROCESS") ?></div>');
		$.ajax({
			url:      '<?php echo JRoute::_("index.php?option=com_joomcck&task=rate.record&tmpl=component", FALSE); ?>',
			dataType: 'json',
			type:     'POST',
			data:     {vote: vote, id: ident, index: index}
		}).done(function(json) {
			$('#rating-text-' + ident).removeClass('progress progress-striped active').html('&nbsp;');

			if(!json) {
				return;
			}

			if(!json.success) {
				$('#rating-text-' + ident).html(old_html);
				alert(json.error);
				return;
			}

			$('#rating-text-' + ident).html('<?php echo JText::sprintf("CRAINGDATA", "' + json.result + '", "' + json.votes + '");?>');

			if(json.result) {
				if(multi) {
					var fname = eval('newRating' + index + '_' + ident);
					fname.setCurrentStar(vote);
				}

				var fname = eval('newRating500_' + ident);
				fname.setCurrentStar(json.result);
			}
			if(_gaq) {
				_gaq.push(['_trackEvent', 'Record', 'Rated', json.name, vote]);
			}
		});
	};

	Joomcck.cleanFilter = function(name) {
		$('#' + name).val(1);
		Joomla.submitbutton('records.clean');
	};

	Joomcck.applyFilter = function(name, val, type) {
		var el = $('#adminForm');

		if(type) {
			var inp3 = $(document.createElement('input'))
				.attr('type', 'hidden')
				.attr('value', 'filter_type')
				.attr('name', 'filter_name[1]');

			var inp4 = $(document.createElement('input'))
				.attr('type', 'hidden')
				.attr('value', type)
				.attr('name', 'filter_val[1]');
			el.append(inp3);
			el.append(inp4);
		}

		var inp1 = $(document.createElement('input'))
			.attr('type', 'hidden')
			.attr('value', name)
			.attr('name', 'filter_name[0]');

		var inp2 = $(document.createElement('input'))
			.attr('type', 'hidden')
			.attr('value', val)
			.attr('name', 'filter_val[0]');

		el.append(inp1);
		el.append(inp2);

		Joomla.submitbutton('records.filter');
		if(_gaq) {
			_gaq.push(['_trackEvent', 'Filter', name, val]);
		}
	};

	Joomcck.showAddForm = function(id) {
		var link = $('#show_variant_link_' + id).clone();
		var data = Joomla.getOptions('com_joomcck.variant_link_' + id);
		var container = $('#variant_' + id);

		var input = $(document.createElement('input'))
			.attr('name', 'your_variant_' + data.id)
			.attr('type', 'text')
			.attr('class', 'form-control form-control-sm d-inline');
		var ba = $(document.createElement('button'))
			.attr('type', 'button')
			.attr('class', 'btn btn-sm btn-outline-secondary')
			.html('<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/plus-button.png" /> <?php echo JText::_("Add");?>');
		var bc = $(document.createElement('button'))
			.attr('type', 'button')
			.attr('class', 'btn btn-sm btn-outline-secondary')
			.html('<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/minus-button.png" /> <?php echo JText::_("Close");?>');

		var bg = $(document.createElement('div'))
			.attr('class', 'input-group w-50')
			.append(input).append(ba).append(bc);

		bc.click(function(el) {
			input.val('');
			container.html('');
			container.append(link);
		});

		ba.click(function(el) {
			if(!input.val()) {
				alert('<?php echo JText::_("CENTERVAL")?>');
				return;
			}

			var inpname = 'jform[fields][' + data.id + ']';
			if(data.inputtype == 'checkbox') {
				inpname += '[]';
			}

			if(data.inputtype == 'option') {
				var inpt = $(document.createElement('option'))
					.attr('value', input.val())
					.attr('selected', 'selected')
					.html(input.val())
					.click(function() {
						Joomcck.countFieldValues(this, data.id, data.limit, data.inputtype);
					});

				var sel = $('#form_field_list_' + data.id);

				sel.append(inpt);
				sel.trigger("liszt:updated");

				if(data.field_type == 'multiselect') {
					sel.attr('size', (parseInt(sel.attr('size')) + 1));
				}
			}
			else {
				var inpt = $(document.createElement('input'))
					.attr({
						value:    input.val(),
						selected: 'selected',
						checked:  'checked',
						type:     data.inputtype,
						name:     inpname
					})
					.click(function() {
						Joomcck.countFieldValues(this, data.id, data.limit, data.inputtype);
					});

				$(document.createElement('div')).attr({'class': 'row-fluid'})
					.append($(document.createElement('div'))
						.attr({'class': 'span12'})
						.append($(document.createElement('label'))
							.attr({'class': data.inputtype})
							.append(inpt, input.val())))
					.appendTo($('#elements-list-' + data.id));
			}

			Joomcck.countFieldValues(inpt, data.id, data.limit, data.inputtype);
			bc.trigger('click');
		});

		container.html('');
		container.append(bg);
	};

	Joomcck.countFieldValues = function(val, field_id, limit, type) {
		Joomcck.fieldError(field_id);
		if(limit <= 0) {
			return;
		}

		var field = $('[name^=jform\\[fields\\]\\[' + field_id + '\\]]');
		var selected = 0;
		if(type == 'checkbox') {
			$.each(field, function(key, obj) {
				if(obj.checked) {
					selected++;
				}
			});
		}
		if(type == 'option') {
			$.each(field[0].options, function(key, obj) {
				if(obj.selected) {
					selected++;
				}
			});
		}
		if(type == 'select') {
			selected = val.getSelected().length;
		}

		if(selected > limit) {

			var msg = '<?php echo JText::sprintf("CERRJSMOREOPTIONS")?>';
			Joomcck.fieldError(field_id, msg);

			if(type == 'checkbox') {
				val.removeAttr('checked', '');
			}
			else if(type == 'option') {
				val.removeAttr('selected', '');
			}
			else if(type == 'select') {
				$.each(val.getSelected(), function(k, v) {
					if(k + 1 > limit) {
						v.selected = false;
					}
				});
			}
		}
	};

	Joomcck.fieldError = function(id, msg) {
		var box = $('#field-alert-' + id);
		var control = box.closest('.control-group');

		if(msg) {
			box.html(msg);
			box.slideDown('quick', function() {
				control.addClass('error').click(function() {
					Joomcck.fieldErrorClear(id);
				});
			});
		} else {
			Joomcck.fieldErrorClear(id);
		}
	};

	Joomcck.fieldErrorClear = function(id) {
		var box = $('#field-alert-' + id);
		var control = box.closest('.control-group');

		box.html('').slideUp('quick');
		control.unbind('click').removeClass('error');
	};


	Joomcck.setAndSubmit = function(el, val) {
		var elm = $('#' + el);
		elm.val(val);
		elm.parents('form').submit();
	};

	Joomcck.editComment = function(id, parent, record) {
		var url = '<?php echo JRoute::_("index.php?option=com_joomcck&view=comment&tmpl=component", FALSE);?>' + '&id=' + id;
		if(parent) {
			url += '&parent_id=' + parent + '&record_id=' + record;
		}
		var iframe = $(document.createElement('iframe')).attr({
			'src':         url,
			'frameborder': "0",
			'width':       "100%",
			'height':      "600px"
		});
		$('#commentframe').html(iframe);

		if(id) {
			$('#commentlabel').html('<?php echo htmlentities(JText::_("CEDITCOMMENT"), ENT_QUOTES, "UTF-8")?>');
		}
		else {
			$('#commentlabel').html('<?php echo htmlentities(JText::_("CADDCOMMENT"), ENT_QUOTES, "UTF-8")?>');
		}
	};

	Joomcck.field_call_url = '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.field_call&tmpl=component", FALSE);?>';

}($));


function trackComment(comment, id) {
	$.ajax({
		url:  '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.trackcomment&tmpl=component", FALSE); ?>',
		data: {
			record_id: id
		}
	});
}

function getSelectionHtml() {
	var html = "";
	if(typeof window.getSelection != "undefined") {
		var sel = window.getSelection();
		if(sel.rangeCount) {
			var container = document.createElement("div");
			for(var i = 0, len = sel.rangeCount; i < len; ++i) {
				container.appendChild(sel.getRangeAt(i).cloneContents());
			}
			html = container.innerHTML;
		}
	} else if(typeof document.selection != "undefined") {
		if(document.selection.type == "Text") {
			html = document.selection.createRange().htmlText;
		}
	}
	return html;
}