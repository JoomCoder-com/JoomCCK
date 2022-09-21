(function($){
	$(function(){
		var title = document.title;
		var notifications = {};

		Joomcck.Notif = function(options){

			var defaults = {
				position: 'bottom-left',
				type: 'blackgloss',
				width: '320',
				limit: 5,
				url: 'http://' + document.domain + '/'
			};

			this.options  = $.extend(true, {}, defaults, options);

			var box = $(document.createElement('div')).addClass('notifications ' + this.options.position).width(this.options.width);
			$('body').append(box);

			var self = this;

			updateState(this);
			setInterval(function(){
				updateState(self);
			}, 60000);
		}

		function updateState(self) {
			$.ajax({
				url: self.options.url + 'index.php?option=com_joomcck&task=ajax.get_notifications&tmpl=component&lang=en',
				type: 'post',
				dataType:'json',
				data:{notiflimit:self.options.limit}
			}).done(function(json){
				if(!json.success){
					return;
				}
				$.each(json.result, function (k, v) {
					if(!notifications.hasOwnProperty(v.id)){

						notifications[v.id] = v.id;

						$('.' + self.options.position).notify({
							closable: true,
							type: self.options.type,
							fadeOut: { enabled: false},
							message: { html: v.html},
							onClose: function(){
								$.ajax({
									url: self.options.url + 'index.php?option=com_joomcck&task=ajax.mark_notification&tmpl=component&lang=en',
									type: 'post',
									dataType:'json',
									data:{id: v.id}
								}).done(function(data){
									if(!json.success) {
										alert(json.error);
										return;
									}
									updateState(self);
								});
							}
						}).show();
					}
				});
				updateTitle();
			});
		}

		function updateTitle(id) {
			if(id) {
				delete notifications[id];
			}

			var count = countNotification();

			document.title = (count ? '(' + count + ') ' : '') + title;
		}

		function countNotification()
		{
			var count = 0;
			for (i in notifications) {
				count++;
			}

			return count;
		}
	});
}(jQuery));