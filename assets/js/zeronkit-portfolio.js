/* ZeronKit Portfolio: lightbox, portfolio filters, layout toggle, form focus. No dependencies. */
(function () {
	'use strict';
	var text = window.zeronkitPortfolioText || { viewer: 'Image viewer', prev: 'Previous image', next: 'Next image', close: 'Close' };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

	/* Portfolio filters. Emits "zkt:filter" on the portfolio container so themes can animate. */
	$$('[data-zkt-filters]').forEach(function (bar) {
		var grid = document.querySelector('[data-zkt-portfolio]');
		if (!grid) return;
		/* Each button group (categories, places) keeps its own choice. An item shows when it matches all of them. */
		var groups = $$('[role="group"]', bar).filter(function (g) { return g.querySelector('[data-zkt-filter]'); });
		function active() {
			return groups.map(function (g) { var b = g.querySelector('[data-zkt-filter][aria-pressed="true"]'); return b ? b.dataset.zktFilter : 'all'; });
		}
		function apply(btn) {
			var group = btn.closest('[role="group"]');
			$$('[data-zkt-filter]', group).forEach(function (b) { b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'); });
			var wanted = active().filter(function (s) { return s !== 'all'; });
			$$('[data-zkt-cats]', grid).forEach(function (item) {
				var cats = ' ' + item.dataset.zktCats + ' ';
				item.hidden = !wanted.every(function (s) { return cats.indexOf(' ' + s + ' ') > -1; });
			});
			grid.dispatchEvent(new CustomEvent('zkt:filter', { detail: { slug: btn.dataset.zktFilter, filters: wanted }, bubbles: true }));
		}
		$$('[data-zkt-filter]', bar).forEach(function (b) {
			b.addEventListener('click', function () { apply(b); });
		});
		var pressed = bar.querySelector('[data-zkt-filter][aria-pressed="true"]:not([data-zkt-filter="all"])');
		if (pressed) apply(pressed);

		$$('[data-zkt-layout]', bar).forEach(function (b) {
			b.addEventListener('click', function () {
				grid.setAttribute('data-layout', b.dataset.zktLayout);
				$$('[data-zkt-layout]', bar).forEach(function (o) { o.setAttribute('aria-pressed', o === b ? 'true' : 'false'); });
			});
		});
	});

	/* Lightbox built on <dialog>, which gives focus trapping and Esc for free. */
	var links = $$('a[data-zkt-lightbox]');
	if (links.length && typeof HTMLDialogElement === 'function') {
		var dlg = document.createElement('dialog');
		dlg.className = 'zkt-lightbox';
		dlg.setAttribute('aria-label', text.viewer);
		dlg.innerHTML =
			'<span class="zkt-lightbox__count" aria-live="polite"></span>' +
			'<figure class="zkt-lightbox__figure"><img alt=""><figcaption class="zkt-lightbox__caption"></figcaption></figure>' +
			'<button type="button" class="zkt-lightbox__btn zkt-lightbox__prev" aria-label="">&#8592;</button>' +
			'<button type="button" class="zkt-lightbox__btn zkt-lightbox__next" aria-label="">&#8594;</button>' +
			'<button type="button" class="zkt-lightbox__btn zkt-lightbox__close" aria-label="">&#215;</button>';
		dlg.querySelector('.zkt-lightbox__prev').setAttribute('aria-label', text.prev);
		dlg.querySelector('.zkt-lightbox__next').setAttribute('aria-label', text.next);
		dlg.querySelector('.zkt-lightbox__close').setAttribute('aria-label', text.close);
		document.body.appendChild(dlg);
		var img = dlg.querySelector('img'), cap = dlg.querySelector('figcaption'), count = dlg.querySelector('.zkt-lightbox__count');
		var group = [], index = 0, opener = null;

		function visible(a) { return !a.closest('[hidden]'); }
		function show(i) {
			index = (i + group.length) % group.length;
			var a = group[index], thumb = a.querySelector('img');
			img.src = a.href;
			img.alt = thumb ? thumb.alt : '';
			cap.textContent = a.dataset.caption || '';
			count.textContent = (index + 1) + ' / ' + group.length;
			var nav = group.length > 1;
			dlg.querySelector('.zkt-lightbox__prev').hidden = !nav;
			dlg.querySelector('.zkt-lightbox__next').hidden = !nav;
			new Image().src = group[(index + 1) % group.length].href;
		}
		links.forEach(function (a) {
			a.addEventListener('click', function (e) {
				if (e.metaKey || e.ctrlKey || e.shiftKey) return;
				e.preventDefault();
				opener = a;
				group = links.filter(function (l) { return l.dataset.zktLightbox === a.dataset.zktLightbox && visible(l); });
				show(group.indexOf(a));
				dlg.showModal();
				document.documentElement.style.overflow = 'hidden';
			});
		});
		dlg.addEventListener('close', function () {
			document.documentElement.style.overflow = '';
			img.removeAttribute('src');
			if (opener) opener.focus();
		});
		dlg.querySelector('.zkt-lightbox__prev').addEventListener('click', function () { show(index - 1); });
		dlg.querySelector('.zkt-lightbox__next').addEventListener('click', function () { show(index + 1); });
		dlg.querySelector('.zkt-lightbox__close').addEventListener('click', function () { dlg.close(); });
		dlg.addEventListener('click', function (e) { if (e.target === dlg || e.target.classList.contains('zkt-lightbox__figure')) dlg.close(); });
		dlg.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowRight') show(index + 1);
			if (e.key === 'ArrowLeft') show(index - 1);
		});
		var startX = null;
		dlg.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
		dlg.addEventListener('touchend', function (e) {
			if (startX === null) return;
			var dx = e.changedTouches[0].clientX - startX;
			if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
			startX = null;
		});
	}

	/* Move focus to the form result after a submit redirect. */
	var notice = document.querySelector('.zkt-form__notice');
	if (notice) notice.focus();
})();
