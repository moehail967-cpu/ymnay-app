(function () {
    'use strict';

    var widget = document.getElementById('fomo-widget');
    if (!widget) return;

    var cfg     = JSON.parse(widget.dataset.config || '{}');
    var delay   = (cfg.delay    || 5)  * 1000;
    var duration= (cfg.duration || 6)  * 1000;
    var interval= (cfg.interval || 15) * 1000;
    var loop    = cfg.loop !== false;
    var apiUrl  = cfg.apiUrl || '';

    var entries = [];
    var idx     = 0;
    var timer   = null;
    var toast   = null;

    function timeSince(minutes) {
        if (minutes < 60) return minutes + ' minute' + (minutes !== 1 ? 's' : '') + ' ago';
        var h = Math.round(minutes / 60);
        return h + ' hour' + (h !== 1 ? 's' : '') + ' ago';
    }

    function buildToast(entry) {
        var t = document.createElement('div');
        t.className = 'fomo-toast';

        var imgHtml = entry.product_image
            ? '<img class="fomo-img" src="' + entry.product_image + '" alt="">'
            : '<div class="fomo-img-placeholder">&#128722;</div>';

        var variant = entry.variant ? ' &middot; ' + entry.variant : '';

        t.innerHTML = imgHtml +
            '<div class="fomo-body">' +
                '<div class="fomo-name">' + entry.name + (entry.location ? ' <span style="font-weight:400;color:#6b7280;">from ' + entry.location + '</span>' : '') + '</div>' +
                '<div class="fomo-product">bought <strong>' + entry.product_name + '</strong>' + variant + '</div>' +
                '<div class="fomo-time">' + timeSince(entry.minutes_ago) + '</div>' +
            '</div>' +
            '<button class="fomo-dismiss" aria-label="Dismiss">&times;</button>';

        t.querySelector('.fomo-dismiss').addEventListener('click', function () {
            hideToast(t, true);
        });

        return t;
    }

    function showEntry(entry) {
        if (toast) {
            widget.removeChild(toast);
        }
        toast = buildToast(entry);
        widget.appendChild(toast);

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                toast.classList.add('fomo-visible');
            });
        });

        timer = setTimeout(function () {
            hideToast(toast, false);
        }, duration);
    }

    function hideToast(t, userDismissed) {
        clearTimeout(timer);
        t.classList.add('fomo-hiding');
        t.classList.remove('fomo-visible');
        setTimeout(function () {
            if (t.parentNode) t.parentNode.removeChild(t);
            if (toast === t) toast = null;
            if (!userDismissed) scheduleNext();
        }, 350);
    }

    function scheduleNext() {
        idx++;
        if (idx >= entries.length) {
            if (!loop) return;
            idx = 0;
        }
        timer = setTimeout(function () {
            showEntry(entries[idx]);
        }, interval);
    }

    function start() {
        if (!entries.length) return;
        idx = 0;
        setTimeout(function () {
            showEntry(entries[idx]);
        }, delay);
    }

    fetch(apiUrl)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            entries = data.entries || [];
            if (entries.length) start();
        })
        .catch(function () { /* silently fail */ });
})();
