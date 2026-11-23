/* Gallery picker for the project Details box. */
jQuery(function ($) {
	$(document).on('click', '.zeronkit-portfolio-pick', function (e) {
		e.preventDefault();
		var input = $('#' + $(this).data('target'));
		var preview = input.siblings('.zeronkit-portfolio-preview');
		var frame = wp.media({ title: (window.zeronkitPortfolioAdminText || {}).title || 'Project gallery', multiple: 'add', library: { type: 'image' } });
		frame.on('open', function () {
			var selection = frame.state().get('selection');
			input.val().split(',').filter(Boolean).forEach(function (id) {
				var att = wp.media.attachment(id);
				att.fetch();
				selection.add(att);
			});
		});
		frame.on('select', function () {
			var items = frame.state().get('selection').toJSON();
			input.val(items.map(function (a) { return a.id; }).join(','));
			preview.empty();
			items.forEach(function (a) {
				var src = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
				preview.append($('<img>', { src: src, alt: '' }));
			});
		});
		frame.open();
	});
});
