"use strict";

/*!
 * Generated using the Bootstrap Customizer (http://getbootstrap.com/customize/?id=f4b4c9cb85df757ca08c)
 * Config saved to config.json and https://gist.github.com/f4b4c9cb85df757ca08c
 */
if (typeof jQuery === 'undefined') {
  throw new Error('Bootstrap\'s JavaScript requires jQuery');
}
+function ($) {
  'use strict';

  var version = $.fn.jquery.split(' ')[0].split('.');
  if (version[0] < 2 && version[1] < 9 || version[0] == 1 && version[1] == 9 && version[2] < 1) {
    throw new Error('Bootstrap\'s JavaScript requires jQuery version 1.9.1 or higher');
  }
}(jQuery);

/* ========================================================================
 * Bootstrap: modal.js v3.3.5
 * http://getbootstrap.com/javascript/#modals
 * ========================================================================
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */

+function ($) {
  'use strict';

  // MODAL CLASS DEFINITION
  // ======================
  var Modal = function (element, options) {
    this.options = options;
    this.$body = $(document.body);
    this.$element = $(element);
    this.$dialog = this.$element.find('.modal-dialog');
    this.$backdrop = null;
    this.isShown = null;
    this.originalBodyPad = null;
    this.scrollbarWidth = 0;
    this.ignoreBackdropClick = false;
    if (this.options.remote) {
      this.$element.find('.modal-content').load(this.options.remote, $.proxy(function () {
        this.$element.trigger('loaded.wpbc.modal');
      }, this));
    }
  };
  Modal.VERSION = '3.3.5';
  Modal.TRANSITION_DURATION = 300;
  Modal.BACKDROP_TRANSITION_DURATION = 150;
  Modal.DEFAULTS = {
    backdrop: true,
    keyboard: true,
    show: true
  };
  Modal.prototype.toggle = function (_relatedTarget) {
    return this.isShown ? this.hide() : this.show(_relatedTarget);
  };
  Modal.prototype.show = function (_relatedTarget) {
    var that = this;
    var e = $.Event('show.wpbc.modal', {
      relatedTarget: _relatedTarget
    });
    this.$element.trigger(e);
    if (this.isShown || e.isDefaultPrevented()) return;
    this.isShown = true;
    this.checkScrollbar();
    this.setScrollbar();
    this.$body.addClass('modal-open');
    this.escape();
    this.resize();
    this.$element.on('click.dismiss.wpbc.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this));
    this.$dialog.on('mousedown.dismiss.wpbc.modal', function () {
      that.$element.one('mouseup.dismiss.wpbc.modal', function (e) {
        if ($(e.target).is(that.$element)) that.ignoreBackdropClick = true;
      });
    });
    this.backdrop(function () {
      var transition = $.support.transition && that.$element.hasClass('fade');
      if (!that.$element.parent().length) {
        that.$element.appendTo(that.$body); // don't move modals dom position
      }
      that.$element.show().scrollTop(0);
      that.adjustDialog();
      if (transition) {
        that.$element[0].offsetWidth; // force reflow
      }
      that.$element.addClass('in');
      that.enforceFocus();
      var e = $.Event('shown.wpbc.modal', {
        relatedTarget: _relatedTarget
      });
      transition ? that.$dialog // wait for modal to slide in
      .one('bsTransitionEnd', function () {
        that.$element.trigger('focus').trigger(e);
      }).emulateTransitionEnd(Modal.TRANSITION_DURATION) : that.$element.trigger('focus').trigger(e);
    });
  };
  Modal.prototype.hide = function (e) {
    if (e) e.preventDefault();
    e = $.Event('hide.wpbc.modal');
    this.$element.trigger(e);
    if (!this.isShown || e.isDefaultPrevented()) return;
    this.isShown = false;
    this.escape();
    this.resize();
    $(document).off('focusin.wpbc.modal');
    this.$element.removeClass('in').off('click.dismiss.wpbc.modal').off('mouseup.dismiss.wpbc.modal');
    this.$dialog.off('mousedown.dismiss.wpbc.modal');
    $.support.transition && this.$element.hasClass('fade') ? this.$element.one('bsTransitionEnd', $.proxy(this.hideModal, this)).emulateTransitionEnd(Modal.TRANSITION_DURATION) : this.hideModal();
  };
  Modal.prototype.enforceFocus = function () {
    $(document).off('focusin.wpbc.modal') // guard against infinite focus loop
    .on('focusin.wpbc.modal', $.proxy(function (e) {
      if (this.$element[0] !== e.target && !this.$element.has(e.target).length) {
        this.$element.trigger('focus');
      }
    }, this));
  };
  Modal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard) {
      this.$element.on('keydown.dismiss.wpbc.modal', $.proxy(function (e) {
        e.which == 27 && this.hide();
      }, this));
    } else if (!this.isShown) {
      this.$element.off('keydown.dismiss.wpbc.modal');
    }
  };
  Modal.prototype.resize = function () {
    if (this.isShown) {
      $(window).on('resize.wpbc.modal', $.proxy(this.handleUpdate, this));
    } else {
      $(window).off('resize.wpbc.modal');
    }
  };
  Modal.prototype.hideModal = function () {
    var that = this;
    this.$element.hide();
    this.backdrop(function () {
      that.$body.removeClass('modal-open');
      that.resetAdjustments();
      that.resetScrollbar();
      that.$element.trigger('hidden.wpbc.modal');
    });
  };
  Modal.prototype.removeBackdrop = function () {
    this.$backdrop && this.$backdrop.remove();
    this.$backdrop = null;
  };
  Modal.prototype.backdrop = function (callback) {
    var that = this;
    var animate = this.$element.hasClass('fade') ? 'fade' : '';
    if (this.isShown && this.options.backdrop) {
      var doAnimate = $.support.transition && animate;
      this.$backdrop = $(document.createElement('div')).addClass('modal-backdrop ' + animate).appendTo(this.$body);
      this.$element.on('click.dismiss.wpbc.modal', $.proxy(function (e) {
        if (this.ignoreBackdropClick) {
          this.ignoreBackdropClick = false;
          return;
        }
        if (e.target !== e.currentTarget) return;
        this.options.backdrop == 'static' ? this.$element[0].focus() : this.hide();
      }, this));
      if (doAnimate) this.$backdrop[0].offsetWidth; // force reflow

      this.$backdrop.addClass('in');
      if (!callback) return;
      doAnimate ? this.$backdrop.one('bsTransitionEnd', callback).emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) : callback();
    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.removeClass('in');
      var callbackRemove = function () {
        that.removeBackdrop();
        callback && callback();
      };
      $.support.transition && this.$element.hasClass('fade') ? this.$backdrop.one('bsTransitionEnd', callbackRemove).emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) : callbackRemove();
    } else if (callback) {
      callback();
    }
  };

  // these following methods are used to handle overflowing modals

  Modal.prototype.handleUpdate = function () {
    this.adjustDialog();
  };
  Modal.prototype.adjustDialog = function () {
    var modalIsOverflowing = this.$element[0].scrollHeight > document.documentElement.clientHeight;
    this.$element.css({
      paddingLeft: !this.bodyIsOverflowing && modalIsOverflowing ? this.scrollbarWidth : '',
      paddingRight: this.bodyIsOverflowing && !modalIsOverflowing ? this.scrollbarWidth : ''
    });
  };
  Modal.prototype.resetAdjustments = function () {
    this.$element.css({
      paddingLeft: '',
      paddingRight: ''
    });
  };
  Modal.prototype.checkScrollbar = function () {
    var fullWindowWidth = window.innerWidth;
    if (!fullWindowWidth) {
      // workaround for missing window.innerWidth in IE8
      var documentElementRect = document.documentElement.getBoundingClientRect();
      fullWindowWidth = documentElementRect.right - Math.abs(documentElementRect.left);
    }
    this.bodyIsOverflowing = document.body.clientWidth < fullWindowWidth;
    this.scrollbarWidth = this.measureScrollbar();
  };
  Modal.prototype.setScrollbar = function () {
    var bodyPad = parseInt(this.$body.css('padding-right') || 0, 10);
    this.originalBodyPad = document.body.style.paddingRight || '';
    if (this.bodyIsOverflowing) this.$body.css('padding-right', bodyPad + this.scrollbarWidth);
  };
  Modal.prototype.resetScrollbar = function () {
    this.$body.css('padding-right', this.originalBodyPad);
  };
  Modal.prototype.measureScrollbar = function () {
    // thx walsh
    var scrollDiv = document.createElement('div');
    scrollDiv.className = 'modal-scrollbar-measure';
    this.$body.append(scrollDiv);
    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
    this.$body[0].removeChild(scrollDiv);
    return scrollbarWidth;
  };

  // MODAL PLUGIN DEFINITION
  // =======================

  function Plugin(option, _relatedTarget) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data('wpbc.modal');
      var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option);
      if (!data) $this.data('wpbc.modal', data = new Modal(this, options));
      if (typeof option == 'string') data[option](_relatedTarget);else if (options.show) data.show(_relatedTarget);
    });
  }
  var old = $.fn.wpbc_my_modal;
  $.fn.wpbc_my_modal = Plugin;
  $.fn.wpbc_my_modal.Constructor = Modal;

  // MODAL NO CONFLICT
  // =================

  $.fn.wpbc_my_modal.noConflict = function () {
    $.fn.wpbc_my_modal = old;
    return this;
  };

  // MODAL DATA-API
  // ==============

  $(document).on('click.wpbc.modal.data-api', '[data-toggle="wpbc_my_modal"]', function (e) {
    var $this = $(this);
    var href = $this.attr('href');
    var $target = $($this.attr('data-target') || href && href.replace(/.*(?=#[^\s]+$)/, '')); // strip for ie7
    var option = $target.data('wpbc.modal') ? 'toggle' : $.extend({
      remote: !/#/.test(href) && href
    }, $target.data(), $this.data());
    if ($this.is('a')) e.preventDefault();
    $target.one('show.wpbc.modal', function (showEvent) {
      if (showEvent.isDefaultPrevented()) return; // only register focus restorer if modal will actually get shown
      $target.one('hidden.wpbc.modal', function () {
        $this.is(':visible') && $this.trigger('focus');
      });
    });
    Plugin.call($target, option, this);
  });
}(jQuery);
+function ($) {
  'use strict';

  // DROPDOWN CLASS DEFINITION
  // =========================
  var backdrop = '.dropdown-backdrop';
  var toggle = '[data-toggle="wpbc_dropdown"]';
  var Dropdown = function (element) {
    $(element).on('click.wpbc.dropdown', this.toggle);
  };
  Dropdown.VERSION = '3.3.5';
  function getParent($this) {
    var selector = $this.attr('data-target');
    if (!selector) {
      selector = $this.attr('href');
      selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, ''); // strip for ie7
    }
    var $parent = selector && $(selector);
    return $parent && $parent.length ? $parent : $this.parent();
  }
  function clearMenus(e) {
    if (e && e.which === 3) return;
    $(backdrop).remove();
    $(toggle).each(function () {
      var $this = $(this);
      var $parent = getParent($this);
      var relatedTarget = {
        relatedTarget: this
      };
      if (!$parent.hasClass('open')) return;
      if (e && e.type == 'click' && /input|textarea/i.test(e.target.tagName) && $.contains($parent[0], e.target)) return;
      $parent.trigger(e = $.Event('hide.wpbc.dropdown', relatedTarget));
      if (e.isDefaultPrevented()) return;
      $this.attr('aria-expanded', 'false');
      $parent.removeClass('open').trigger('hidden.wpbc.dropdown', relatedTarget);
    });
  }
  Dropdown.prototype.toggle = function (e) {
    var $this = $(this);
    if ($this.is('.disabled, :disabled')) return;
    var $parent = getParent($this);
    var isActive = $parent.hasClass('open');
    clearMenus();
    if (!isActive) {
      if ('ontouchstart' in document.documentElement && !$parent.closest('.navbar-nav').length) {
        // if mobile we use a backdrop because click events don't delegate
        $(document.createElement('div')).addClass('dropdown-backdrop').insertAfter($(this)).on('click', clearMenus);
      }
      var relatedTarget = {
        relatedTarget: this
      };
      $parent.trigger(e = $.Event('show.wpbc.dropdown', relatedTarget));
      if (e.isDefaultPrevented()) return;
      $this.trigger('focus').attr('aria-expanded', 'true');
      $parent.toggleClass('open').trigger('shown.wpbc.dropdown', relatedTarget);
    }
    return false;
  };
  Dropdown.prototype.keydown = function (e) {
    if (!/(38|40|27|32)/.test(e.which) || /input|textarea/i.test(e.target.tagName)) return;
    var $this = $(this);
    e.preventDefault();
    e.stopPropagation();
    if ($this.is('.disabled, :disabled')) return;
    var $parent = getParent($this);
    var isActive = $parent.hasClass('open');
    if (!isActive && e.which != 27 || isActive && e.which == 27) {
      if (e.which == 27) $parent.find(toggle).trigger('focus');
      return $this.trigger('click');
    }
    var desc = ' li:not(.disabled):visible a';
    var $items = $parent.find('.dropdown-menu' + desc + ',.ui_dropdown_menu' + desc);
    if (!$items.length) return;
    var index = $items.index(e.target);
    if (e.which == 38 && index > 0) index--; // up
    if (e.which == 40 && index < $items.length - 1) index++; // down
    if (!~index) index = 0;
    $items.eq(index).trigger('focus');
  };

  // DROPDOWN PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this);
      var data = $this.data('wpbc.dropdown');
      if (!data) $this.data('wpbc.dropdown', data = new Dropdown(this));
      if (typeof option == 'string') data[option].call($this);
    });
  }
  var old = $.fn.wpbc_dropdown;
  $.fn.wpbc_dropdown = Plugin;
  $.fn.wpbc_dropdown.Constructor = Dropdown;

  // DROPDOWN NO CONFLICT
  // ====================

  $.fn.wpbc_dropdown.noConflict = function () {
    $.fn.wpbc_dropdown = old;
    return this;
  };

  // APPLY TO STANDARD DROPDOWN ELEMENTS
  // ===================================

  $(document).on('click.wpbc.dropdown.data-api', clearMenus).on('click.wpbc.dropdown.data-api', '.dropdown form', function (e) {
    e.stopPropagation();
  }).on('click.wpbc.dropdown.data-api', toggle, Dropdown.prototype.toggle).on('keydown.wpbc.dropdown.data-api', toggle, Dropdown.prototype.keydown).on('keydown.wpbc.dropdown.data-api', '.dropdown-menu', Dropdown.prototype.keydown).on('keydown.wpbc.dropdown.data-api', '.ui_dropdown_menu', Dropdown.prototype.keydown);
}(jQuery);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidmVuZG9ycy9fY3VzdG9tL2Ryb3Bkb3duX21vZGFsL19vdXQvZHJvcGRvd25fbW9kYWwuanMiLCJuYW1lcyI6WyJqUXVlcnkiLCJFcnJvciIsIiQiLCJ2ZXJzaW9uIiwiZm4iLCJqcXVlcnkiLCJzcGxpdCIsIk1vZGFsIiwiZWxlbWVudCIsIm9wdGlvbnMiLCIkYm9keSIsImRvY3VtZW50IiwiYm9keSIsIiRlbGVtZW50IiwiJGRpYWxvZyIsImZpbmQiLCIkYmFja2Ryb3AiLCJpc1Nob3duIiwib3JpZ2luYWxCb2R5UGFkIiwic2Nyb2xsYmFyV2lkdGgiLCJpZ25vcmVCYWNrZHJvcENsaWNrIiwicmVtb3RlIiwibG9hZCIsInByb3h5IiwidHJpZ2dlciIsIlZFUlNJT04iLCJUUkFOU0lUSU9OX0RVUkFUSU9OIiwiQkFDS0RST1BfVFJBTlNJVElPTl9EVVJBVElPTiIsIkRFRkFVTFRTIiwiYmFja2Ryb3AiLCJrZXlib2FyZCIsInNob3ciLCJwcm90b3R5cGUiLCJ0b2dnbGUiLCJfcmVsYXRlZFRhcmdldCIsImhpZGUiLCJ0aGF0IiwiZSIsIkV2ZW50IiwicmVsYXRlZFRhcmdldCIsImlzRGVmYXVsdFByZXZlbnRlZCIsImNoZWNrU2Nyb2xsYmFyIiwic2V0U2Nyb2xsYmFyIiwiYWRkQ2xhc3MiLCJlc2NhcGUiLCJyZXNpemUiLCJvbiIsIm9uZSIsInRhcmdldCIsImlzIiwidHJhbnNpdGlvbiIsInN1cHBvcnQiLCJoYXNDbGFzcyIsInBhcmVudCIsImxlbmd0aCIsImFwcGVuZFRvIiwic2Nyb2xsVG9wIiwiYWRqdXN0RGlhbG9nIiwib2Zmc2V0V2lkdGgiLCJlbmZvcmNlRm9jdXMiLCJlbXVsYXRlVHJhbnNpdGlvbkVuZCIsInByZXZlbnREZWZhdWx0Iiwib2ZmIiwicmVtb3ZlQ2xhc3MiLCJoaWRlTW9kYWwiLCJoYXMiLCJ3aGljaCIsIndpbmRvdyIsImhhbmRsZVVwZGF0ZSIsInJlc2V0QWRqdXN0bWVudHMiLCJyZXNldFNjcm9sbGJhciIsInJlbW92ZUJhY2tkcm9wIiwicmVtb3ZlIiwiY2FsbGJhY2siLCJhbmltYXRlIiwiZG9BbmltYXRlIiwiY3JlYXRlRWxlbWVudCIsImN1cnJlbnRUYXJnZXQiLCJmb2N1cyIsImNhbGxiYWNrUmVtb3ZlIiwibW9kYWxJc092ZXJmbG93aW5nIiwic2Nyb2xsSGVpZ2h0IiwiZG9jdW1lbnRFbGVtZW50IiwiY2xpZW50SGVpZ2h0IiwiY3NzIiwicGFkZGluZ0xlZnQiLCJib2R5SXNPdmVyZmxvd2luZyIsInBhZGRpbmdSaWdodCIsImZ1bGxXaW5kb3dXaWR0aCIsImlubmVyV2lkdGgiLCJkb2N1bWVudEVsZW1lbnRSZWN0IiwiZ2V0Qm91bmRpbmdDbGllbnRSZWN0IiwicmlnaHQiLCJNYXRoIiwiYWJzIiwibGVmdCIsImNsaWVudFdpZHRoIiwibWVhc3VyZVNjcm9sbGJhciIsImJvZHlQYWQiLCJwYXJzZUludCIsInN0eWxlIiwic2Nyb2xsRGl2IiwiY2xhc3NOYW1lIiwiYXBwZW5kIiwicmVtb3ZlQ2hpbGQiLCJQbHVnaW4iLCJvcHRpb24iLCJlYWNoIiwiJHRoaXMiLCJkYXRhIiwiZXh0ZW5kIiwib2xkIiwid3BiY19teV9tb2RhbCIsIkNvbnN0cnVjdG9yIiwibm9Db25mbGljdCIsImhyZWYiLCJhdHRyIiwiJHRhcmdldCIsInJlcGxhY2UiLCJ0ZXN0Iiwic2hvd0V2ZW50IiwiY2FsbCIsIkRyb3Bkb3duIiwiZ2V0UGFyZW50Iiwic2VsZWN0b3IiLCIkcGFyZW50IiwiY2xlYXJNZW51cyIsInR5cGUiLCJ0YWdOYW1lIiwiY29udGFpbnMiLCJpc0FjdGl2ZSIsImNsb3Nlc3QiLCJpbnNlcnRBZnRlciIsInRvZ2dsZUNsYXNzIiwia2V5ZG93biIsInN0b3BQcm9wYWdhdGlvbiIsImRlc2MiLCIkaXRlbXMiLCJpbmRleCIsImVxIiwid3BiY19kcm9wZG93biJdLCJzb3VyY2VzIjpbInZlbmRvcnMvX2N1c3RvbS9kcm9wZG93bl9tb2RhbC9fc3JjL2Ryb3Bkb3duX21vZGFsLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qIVxyXG4gKiBHZW5lcmF0ZWQgdXNpbmcgdGhlIEJvb3RzdHJhcCBDdXN0b21pemVyIChodHRwOi8vZ2V0Ym9vdHN0cmFwLmNvbS9jdXN0b21pemUvP2lkPWY0YjRjOWNiODVkZjc1N2NhMDhjKVxyXG4gKiBDb25maWcgc2F2ZWQgdG8gY29uZmlnLmpzb24gYW5kIGh0dHBzOi8vZ2lzdC5naXRodWIuY29tL2Y0YjRjOWNiODVkZjc1N2NhMDhjXHJcbiAqL1xyXG5pZiAodHlwZW9mIGpRdWVyeSA9PT0gJ3VuZGVmaW5lZCcpIHtcclxuICB0aHJvdyBuZXcgRXJyb3IoJ0Jvb3RzdHJhcFxcJ3MgSmF2YVNjcmlwdCByZXF1aXJlcyBqUXVlcnknKVxyXG59XHJcbitmdW5jdGlvbiAoJCkge1xyXG4gICd1c2Ugc3RyaWN0JztcclxuICB2YXIgdmVyc2lvbiA9ICQuZm4uanF1ZXJ5LnNwbGl0KCcgJylbMF0uc3BsaXQoJy4nKVxyXG4gIGlmICgodmVyc2lvblswXSA8IDIgJiYgdmVyc2lvblsxXSA8IDkpIHx8ICh2ZXJzaW9uWzBdID09IDEgJiYgdmVyc2lvblsxXSA9PSA5ICYmIHZlcnNpb25bMl0gPCAxKSkge1xyXG4gICAgdGhyb3cgbmV3IEVycm9yKCdCb290c3RyYXBcXCdzIEphdmFTY3JpcHQgcmVxdWlyZXMgalF1ZXJ5IHZlcnNpb24gMS45LjEgb3IgaGlnaGVyJylcclxuICB9XHJcbn0oalF1ZXJ5KTtcclxuXHJcbi8qID09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXG4gKiBCb290c3RyYXA6IG1vZGFsLmpzIHYzLjMuNVxyXG4gKiBodHRwOi8vZ2V0Ym9vdHN0cmFwLmNvbS9qYXZhc2NyaXB0LyNtb2RhbHNcclxuICogPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcbiAqIENvcHlyaWdodCAyMDExLTIwMTUgVHdpdHRlciwgSW5jLlxyXG4gKiBMaWNlbnNlZCB1bmRlciBNSVQgKGh0dHBzOi8vZ2l0aHViLmNvbS90d2JzL2Jvb3RzdHJhcC9ibG9iL21hc3Rlci9MSUNFTlNFKVxyXG4gKiA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT0gKi9cclxuXHJcblxyXG4rZnVuY3Rpb24gKCQpIHtcclxuICAndXNlIHN0cmljdCc7XHJcblxyXG4gIC8vIE1PREFMIENMQVNTIERFRklOSVRJT05cclxuICAvLyA9PT09PT09PT09PT09PT09PT09PT09XHJcblxyXG4gIHZhciBNb2RhbCA9IGZ1bmN0aW9uIChlbGVtZW50LCBvcHRpb25zKSB7XHJcbiAgICB0aGlzLm9wdGlvbnMgICAgICAgICAgICAgPSBvcHRpb25zXHJcbiAgICB0aGlzLiRib2R5ICAgICAgICAgICAgICAgPSAkKGRvY3VtZW50LmJvZHkpXHJcbiAgICB0aGlzLiRlbGVtZW50ICAgICAgICAgICAgPSAkKGVsZW1lbnQpXHJcbiAgICB0aGlzLiRkaWFsb2cgICAgICAgICAgICAgPSB0aGlzLiRlbGVtZW50LmZpbmQoJy5tb2RhbC1kaWFsb2cnKVxyXG4gICAgdGhpcy4kYmFja2Ryb3AgICAgICAgICAgID0gbnVsbFxyXG4gICAgdGhpcy5pc1Nob3duICAgICAgICAgICAgID0gbnVsbFxyXG4gICAgdGhpcy5vcmlnaW5hbEJvZHlQYWQgICAgID0gbnVsbFxyXG4gICAgdGhpcy5zY3JvbGxiYXJXaWR0aCAgICAgID0gMFxyXG4gICAgdGhpcy5pZ25vcmVCYWNrZHJvcENsaWNrID0gZmFsc2VcclxuXHJcbiAgICBpZiAodGhpcy5vcHRpb25zLnJlbW90ZSkge1xyXG4gICAgICB0aGlzLiRlbGVtZW50XHJcbiAgICAgICAgLmZpbmQoJy5tb2RhbC1jb250ZW50JylcclxuICAgICAgICAubG9hZCh0aGlzLm9wdGlvbnMucmVtb3RlLCAkLnByb3h5KGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgIHRoaXMuJGVsZW1lbnQudHJpZ2dlcignbG9hZGVkLndwYmMubW9kYWwnKVxyXG4gICAgICAgIH0sIHRoaXMpKVxyXG4gICAgfVxyXG4gIH1cclxuXHJcbiAgTW9kYWwuVkVSU0lPTiAgPSAnMy4zLjUnXHJcblxyXG4gIE1vZGFsLlRSQU5TSVRJT05fRFVSQVRJT04gPSAzMDBcclxuICBNb2RhbC5CQUNLRFJPUF9UUkFOU0lUSU9OX0RVUkFUSU9OID0gMTUwXHJcblxyXG4gIE1vZGFsLkRFRkFVTFRTID0ge1xyXG4gICAgYmFja2Ryb3A6IHRydWUsXHJcbiAgICBrZXlib2FyZDogdHJ1ZSxcclxuICAgIHNob3c6IHRydWVcclxuICB9XHJcblxyXG4gIE1vZGFsLnByb3RvdHlwZS50b2dnbGUgPSBmdW5jdGlvbiAoX3JlbGF0ZWRUYXJnZXQpIHtcclxuICAgIHJldHVybiB0aGlzLmlzU2hvd24gPyB0aGlzLmhpZGUoKSA6IHRoaXMuc2hvdyhfcmVsYXRlZFRhcmdldClcclxuICB9XHJcblxyXG4gIE1vZGFsLnByb3RvdHlwZS5zaG93ID0gZnVuY3Rpb24gKF9yZWxhdGVkVGFyZ2V0KSB7XHJcbiAgICB2YXIgdGhhdCA9IHRoaXNcclxuICAgIHZhciBlICAgID0gJC5FdmVudCgnc2hvdy53cGJjLm1vZGFsJywgeyByZWxhdGVkVGFyZ2V0OiBfcmVsYXRlZFRhcmdldCB9KVxyXG5cclxuICAgIHRoaXMuJGVsZW1lbnQudHJpZ2dlcihlKVxyXG5cclxuICAgIGlmICh0aGlzLmlzU2hvd24gfHwgZS5pc0RlZmF1bHRQcmV2ZW50ZWQoKSkgcmV0dXJuXHJcblxyXG4gICAgdGhpcy5pc1Nob3duID0gdHJ1ZVxyXG5cclxuICAgIHRoaXMuY2hlY2tTY3JvbGxiYXIoKVxyXG4gICAgdGhpcy5zZXRTY3JvbGxiYXIoKVxyXG4gICAgdGhpcy4kYm9keS5hZGRDbGFzcygnbW9kYWwtb3BlbicpXHJcblxyXG4gICAgdGhpcy5lc2NhcGUoKVxyXG4gICAgdGhpcy5yZXNpemUoKVxyXG5cclxuICAgIHRoaXMuJGVsZW1lbnQub24oJ2NsaWNrLmRpc21pc3Mud3BiYy5tb2RhbCcsICdbZGF0YS1kaXNtaXNzPVwibW9kYWxcIl0nLCAkLnByb3h5KHRoaXMuaGlkZSwgdGhpcykpXHJcblxyXG4gICAgdGhpcy4kZGlhbG9nLm9uKCdtb3VzZWRvd24uZGlzbWlzcy53cGJjLm1vZGFsJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICB0aGF0LiRlbGVtZW50Lm9uZSgnbW91c2V1cC5kaXNtaXNzLndwYmMubW9kYWwnLCBmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIGlmICgkKGUudGFyZ2V0KS5pcyh0aGF0LiRlbGVtZW50KSkgdGhhdC5pZ25vcmVCYWNrZHJvcENsaWNrID0gdHJ1ZVxyXG4gICAgICB9KVxyXG4gICAgfSlcclxuXHJcbiAgICB0aGlzLmJhY2tkcm9wKGZ1bmN0aW9uICgpIHtcclxuICAgICAgdmFyIHRyYW5zaXRpb24gPSAkLnN1cHBvcnQudHJhbnNpdGlvbiAmJiB0aGF0LiRlbGVtZW50Lmhhc0NsYXNzKCdmYWRlJylcclxuXHJcbiAgICAgIGlmICghdGhhdC4kZWxlbWVudC5wYXJlbnQoKS5sZW5ndGgpIHtcclxuICAgICAgICB0aGF0LiRlbGVtZW50LmFwcGVuZFRvKHRoYXQuJGJvZHkpIC8vIGRvbid0IG1vdmUgbW9kYWxzIGRvbSBwb3NpdGlvblxyXG4gICAgICB9XHJcblxyXG4gICAgICB0aGF0LiRlbGVtZW50XHJcbiAgICAgICAgLnNob3coKVxyXG4gICAgICAgIC5zY3JvbGxUb3AoMClcclxuXHJcbiAgICAgIHRoYXQuYWRqdXN0RGlhbG9nKClcclxuXHJcbiAgICAgIGlmICh0cmFuc2l0aW9uKSB7XHJcbiAgICAgICAgdGhhdC4kZWxlbWVudFswXS5vZmZzZXRXaWR0aCAvLyBmb3JjZSByZWZsb3dcclxuICAgICAgfVxyXG5cclxuICAgICAgdGhhdC4kZWxlbWVudC5hZGRDbGFzcygnaW4nKVxyXG5cclxuICAgICAgdGhhdC5lbmZvcmNlRm9jdXMoKVxyXG5cclxuICAgICAgdmFyIGUgPSAkLkV2ZW50KCdzaG93bi53cGJjLm1vZGFsJywgeyByZWxhdGVkVGFyZ2V0OiBfcmVsYXRlZFRhcmdldCB9KVxyXG5cclxuICAgICAgdHJhbnNpdGlvbiA/XHJcbiAgICAgICAgdGhhdC4kZGlhbG9nIC8vIHdhaXQgZm9yIG1vZGFsIHRvIHNsaWRlIGluXHJcbiAgICAgICAgICAub25lKCdic1RyYW5zaXRpb25FbmQnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHRoYXQuJGVsZW1lbnQudHJpZ2dlcignZm9jdXMnKS50cmlnZ2VyKGUpXHJcbiAgICAgICAgICB9KVxyXG4gICAgICAgICAgLmVtdWxhdGVUcmFuc2l0aW9uRW5kKE1vZGFsLlRSQU5TSVRJT05fRFVSQVRJT04pIDpcclxuICAgICAgICB0aGF0LiRlbGVtZW50LnRyaWdnZXIoJ2ZvY3VzJykudHJpZ2dlcihlKVxyXG4gICAgfSlcclxuICB9XHJcblxyXG4gIE1vZGFsLnByb3RvdHlwZS5oaWRlID0gZnVuY3Rpb24gKGUpIHtcclxuICAgIGlmIChlKSBlLnByZXZlbnREZWZhdWx0KClcclxuXHJcbiAgICBlID0gJC5FdmVudCgnaGlkZS53cGJjLm1vZGFsJylcclxuXHJcbiAgICB0aGlzLiRlbGVtZW50LnRyaWdnZXIoZSlcclxuXHJcbiAgICBpZiAoIXRoaXMuaXNTaG93biB8fCBlLmlzRGVmYXVsdFByZXZlbnRlZCgpKSByZXR1cm5cclxuXHJcbiAgICB0aGlzLmlzU2hvd24gPSBmYWxzZVxyXG5cclxuICAgIHRoaXMuZXNjYXBlKClcclxuICAgIHRoaXMucmVzaXplKClcclxuXHJcbiAgICAkKGRvY3VtZW50KS5vZmYoJ2ZvY3VzaW4ud3BiYy5tb2RhbCcpXHJcblxyXG4gICAgdGhpcy4kZWxlbWVudFxyXG4gICAgICAucmVtb3ZlQ2xhc3MoJ2luJylcclxuICAgICAgLm9mZignY2xpY2suZGlzbWlzcy53cGJjLm1vZGFsJylcclxuICAgICAgLm9mZignbW91c2V1cC5kaXNtaXNzLndwYmMubW9kYWwnKVxyXG5cclxuICAgIHRoaXMuJGRpYWxvZy5vZmYoJ21vdXNlZG93bi5kaXNtaXNzLndwYmMubW9kYWwnKVxyXG5cclxuICAgICQuc3VwcG9ydC50cmFuc2l0aW9uICYmIHRoaXMuJGVsZW1lbnQuaGFzQ2xhc3MoJ2ZhZGUnKSA/XHJcbiAgICAgIHRoaXMuJGVsZW1lbnRcclxuICAgICAgICAub25lKCdic1RyYW5zaXRpb25FbmQnLCAkLnByb3h5KHRoaXMuaGlkZU1vZGFsLCB0aGlzKSlcclxuICAgICAgICAuZW11bGF0ZVRyYW5zaXRpb25FbmQoTW9kYWwuVFJBTlNJVElPTl9EVVJBVElPTikgOlxyXG4gICAgICB0aGlzLmhpZGVNb2RhbCgpXHJcbiAgfVxyXG5cclxuICBNb2RhbC5wcm90b3R5cGUuZW5mb3JjZUZvY3VzID0gZnVuY3Rpb24gKCkge1xyXG4gICAgJChkb2N1bWVudClcclxuICAgICAgLm9mZignZm9jdXNpbi53cGJjLm1vZGFsJykgLy8gZ3VhcmQgYWdhaW5zdCBpbmZpbml0ZSBmb2N1cyBsb29wXHJcbiAgICAgIC5vbignZm9jdXNpbi53cGJjLm1vZGFsJywgJC5wcm94eShmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIGlmICh0aGlzLiRlbGVtZW50WzBdICE9PSBlLnRhcmdldCAmJiAhdGhpcy4kZWxlbWVudC5oYXMoZS50YXJnZXQpLmxlbmd0aCkge1xyXG4gICAgICAgICAgdGhpcy4kZWxlbWVudC50cmlnZ2VyKCdmb2N1cycpXHJcbiAgICAgICAgfVxyXG4gICAgICB9LCB0aGlzKSlcclxuICB9XHJcblxyXG4gIE1vZGFsLnByb3RvdHlwZS5lc2NhcGUgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICBpZiAodGhpcy5pc1Nob3duICYmIHRoaXMub3B0aW9ucy5rZXlib2FyZCkge1xyXG4gICAgICB0aGlzLiRlbGVtZW50Lm9uKCdrZXlkb3duLmRpc21pc3Mud3BiYy5tb2RhbCcsICQucHJveHkoZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICBlLndoaWNoID09IDI3ICYmIHRoaXMuaGlkZSgpXHJcbiAgICAgIH0sIHRoaXMpKVxyXG4gICAgfSBlbHNlIGlmICghdGhpcy5pc1Nob3duKSB7XHJcbiAgICAgIHRoaXMuJGVsZW1lbnQub2ZmKCdrZXlkb3duLmRpc21pc3Mud3BiYy5tb2RhbCcpXHJcbiAgICB9XHJcbiAgfVxyXG5cclxuICBNb2RhbC5wcm90b3R5cGUucmVzaXplID0gZnVuY3Rpb24gKCkge1xyXG4gICAgaWYgKHRoaXMuaXNTaG93bikge1xyXG4gICAgICAkKHdpbmRvdykub24oJ3Jlc2l6ZS53cGJjLm1vZGFsJywgJC5wcm94eSh0aGlzLmhhbmRsZVVwZGF0ZSwgdGhpcykpXHJcbiAgICB9IGVsc2Uge1xyXG4gICAgICAkKHdpbmRvdykub2ZmKCdyZXNpemUud3BiYy5tb2RhbCcpXHJcbiAgICB9XHJcbiAgfVxyXG5cclxuICBNb2RhbC5wcm90b3R5cGUuaGlkZU1vZGFsID0gZnVuY3Rpb24gKCkge1xyXG4gICAgdmFyIHRoYXQgPSB0aGlzXHJcbiAgICB0aGlzLiRlbGVtZW50LmhpZGUoKVxyXG4gICAgdGhpcy5iYWNrZHJvcChmdW5jdGlvbiAoKSB7XHJcbiAgICAgIHRoYXQuJGJvZHkucmVtb3ZlQ2xhc3MoJ21vZGFsLW9wZW4nKVxyXG4gICAgICB0aGF0LnJlc2V0QWRqdXN0bWVudHMoKVxyXG4gICAgICB0aGF0LnJlc2V0U2Nyb2xsYmFyKClcclxuICAgICAgdGhhdC4kZWxlbWVudC50cmlnZ2VyKCdoaWRkZW4ud3BiYy5tb2RhbCcpXHJcbiAgICB9KVxyXG4gIH1cclxuXHJcbiAgTW9kYWwucHJvdG90eXBlLnJlbW92ZUJhY2tkcm9wID0gZnVuY3Rpb24gKCkge1xyXG4gICAgdGhpcy4kYmFja2Ryb3AgJiYgdGhpcy4kYmFja2Ryb3AucmVtb3ZlKClcclxuICAgIHRoaXMuJGJhY2tkcm9wID0gbnVsbFxyXG4gIH1cclxuXHJcbiAgTW9kYWwucHJvdG90eXBlLmJhY2tkcm9wID0gZnVuY3Rpb24gKGNhbGxiYWNrKSB7XHJcbiAgICB2YXIgdGhhdCA9IHRoaXNcclxuICAgIHZhciBhbmltYXRlID0gdGhpcy4kZWxlbWVudC5oYXNDbGFzcygnZmFkZScpID8gJ2ZhZGUnIDogJydcclxuXHJcbiAgICBpZiAodGhpcy5pc1Nob3duICYmIHRoaXMub3B0aW9ucy5iYWNrZHJvcCkge1xyXG4gICAgICB2YXIgZG9BbmltYXRlID0gJC5zdXBwb3J0LnRyYW5zaXRpb24gJiYgYW5pbWF0ZVxyXG5cclxuICAgICAgdGhpcy4kYmFja2Ryb3AgPSAkKGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpKVxyXG4gICAgICAgIC5hZGRDbGFzcygnbW9kYWwtYmFja2Ryb3AgJyArIGFuaW1hdGUpXHJcbiAgICAgICAgLmFwcGVuZFRvKHRoaXMuJGJvZHkpXHJcblxyXG4gICAgICB0aGlzLiRlbGVtZW50Lm9uKCdjbGljay5kaXNtaXNzLndwYmMubW9kYWwnLCAkLnByb3h5KGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgaWYgKHRoaXMuaWdub3JlQmFja2Ryb3BDbGljaykge1xyXG4gICAgICAgICAgdGhpcy5pZ25vcmVCYWNrZHJvcENsaWNrID0gZmFsc2VcclxuICAgICAgICAgIHJldHVyblxyXG4gICAgICAgIH1cclxuICAgICAgICBpZiAoZS50YXJnZXQgIT09IGUuY3VycmVudFRhcmdldCkgcmV0dXJuXHJcbiAgICAgICAgdGhpcy5vcHRpb25zLmJhY2tkcm9wID09ICdzdGF0aWMnXHJcbiAgICAgICAgICA/IHRoaXMuJGVsZW1lbnRbMF0uZm9jdXMoKVxyXG4gICAgICAgICAgOiB0aGlzLmhpZGUoKVxyXG4gICAgICB9LCB0aGlzKSlcclxuXHJcbiAgICAgIGlmIChkb0FuaW1hdGUpIHRoaXMuJGJhY2tkcm9wWzBdLm9mZnNldFdpZHRoIC8vIGZvcmNlIHJlZmxvd1xyXG5cclxuICAgICAgdGhpcy4kYmFja2Ryb3AuYWRkQ2xhc3MoJ2luJylcclxuXHJcbiAgICAgIGlmICghY2FsbGJhY2spIHJldHVyblxyXG5cclxuICAgICAgZG9BbmltYXRlID9cclxuICAgICAgICB0aGlzLiRiYWNrZHJvcFxyXG4gICAgICAgICAgLm9uZSgnYnNUcmFuc2l0aW9uRW5kJywgY2FsbGJhY2spXHJcbiAgICAgICAgICAuZW11bGF0ZVRyYW5zaXRpb25FbmQoTW9kYWwuQkFDS0RST1BfVFJBTlNJVElPTl9EVVJBVElPTikgOlxyXG4gICAgICAgIGNhbGxiYWNrKClcclxuXHJcbiAgICB9IGVsc2UgaWYgKCF0aGlzLmlzU2hvd24gJiYgdGhpcy4kYmFja2Ryb3ApIHtcclxuICAgICAgdGhpcy4kYmFja2Ryb3AucmVtb3ZlQ2xhc3MoJ2luJylcclxuXHJcbiAgICAgIHZhciBjYWxsYmFja1JlbW92ZSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB0aGF0LnJlbW92ZUJhY2tkcm9wKClcclxuICAgICAgICBjYWxsYmFjayAmJiBjYWxsYmFjaygpXHJcbiAgICAgIH1cclxuICAgICAgJC5zdXBwb3J0LnRyYW5zaXRpb24gJiYgdGhpcy4kZWxlbWVudC5oYXNDbGFzcygnZmFkZScpID9cclxuICAgICAgICB0aGlzLiRiYWNrZHJvcFxyXG4gICAgICAgICAgLm9uZSgnYnNUcmFuc2l0aW9uRW5kJywgY2FsbGJhY2tSZW1vdmUpXHJcbiAgICAgICAgICAuZW11bGF0ZVRyYW5zaXRpb25FbmQoTW9kYWwuQkFDS0RST1BfVFJBTlNJVElPTl9EVVJBVElPTikgOlxyXG4gICAgICAgIGNhbGxiYWNrUmVtb3ZlKClcclxuXHJcbiAgICB9IGVsc2UgaWYgKGNhbGxiYWNrKSB7XHJcbiAgICAgIGNhbGxiYWNrKClcclxuICAgIH1cclxuICB9XHJcblxyXG4gIC8vIHRoZXNlIGZvbGxvd2luZyBtZXRob2RzIGFyZSB1c2VkIHRvIGhhbmRsZSBvdmVyZmxvd2luZyBtb2RhbHNcclxuXHJcbiAgTW9kYWwucHJvdG90eXBlLmhhbmRsZVVwZGF0ZSA9IGZ1bmN0aW9uICgpIHtcclxuICAgIHRoaXMuYWRqdXN0RGlhbG9nKClcclxuICB9XHJcblxyXG4gIE1vZGFsLnByb3RvdHlwZS5hZGp1c3REaWFsb2cgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICB2YXIgbW9kYWxJc092ZXJmbG93aW5nID0gdGhpcy4kZWxlbWVudFswXS5zY3JvbGxIZWlnaHQgPiBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuY2xpZW50SGVpZ2h0XHJcblxyXG4gICAgdGhpcy4kZWxlbWVudC5jc3Moe1xyXG4gICAgICBwYWRkaW5nTGVmdDogICF0aGlzLmJvZHlJc092ZXJmbG93aW5nICYmIG1vZGFsSXNPdmVyZmxvd2luZyA/IHRoaXMuc2Nyb2xsYmFyV2lkdGggOiAnJyxcclxuICAgICAgcGFkZGluZ1JpZ2h0OiB0aGlzLmJvZHlJc092ZXJmbG93aW5nICYmICFtb2RhbElzT3ZlcmZsb3dpbmcgPyB0aGlzLnNjcm9sbGJhcldpZHRoIDogJydcclxuICAgIH0pXHJcbiAgfVxyXG5cclxuICBNb2RhbC5wcm90b3R5cGUucmVzZXRBZGp1c3RtZW50cyA9IGZ1bmN0aW9uICgpIHtcclxuICAgIHRoaXMuJGVsZW1lbnQuY3NzKHtcclxuICAgICAgcGFkZGluZ0xlZnQ6ICcnLFxyXG4gICAgICBwYWRkaW5nUmlnaHQ6ICcnXHJcbiAgICB9KVxyXG4gIH1cclxuXHJcbiAgTW9kYWwucHJvdG90eXBlLmNoZWNrU2Nyb2xsYmFyID0gZnVuY3Rpb24gKCkge1xyXG4gICAgdmFyIGZ1bGxXaW5kb3dXaWR0aCA9IHdpbmRvdy5pbm5lcldpZHRoXHJcbiAgICBpZiAoIWZ1bGxXaW5kb3dXaWR0aCkgeyAvLyB3b3JrYXJvdW5kIGZvciBtaXNzaW5nIHdpbmRvdy5pbm5lcldpZHRoIGluIElFOFxyXG4gICAgICB2YXIgZG9jdW1lbnRFbGVtZW50UmVjdCA9IGRvY3VtZW50LmRvY3VtZW50RWxlbWVudC5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKVxyXG4gICAgICBmdWxsV2luZG93V2lkdGggPSBkb2N1bWVudEVsZW1lbnRSZWN0LnJpZ2h0IC0gTWF0aC5hYnMoZG9jdW1lbnRFbGVtZW50UmVjdC5sZWZ0KVxyXG4gICAgfVxyXG4gICAgdGhpcy5ib2R5SXNPdmVyZmxvd2luZyA9IGRvY3VtZW50LmJvZHkuY2xpZW50V2lkdGggPCBmdWxsV2luZG93V2lkdGhcclxuICAgIHRoaXMuc2Nyb2xsYmFyV2lkdGggPSB0aGlzLm1lYXN1cmVTY3JvbGxiYXIoKVxyXG4gIH1cclxuXHJcbiAgTW9kYWwucHJvdG90eXBlLnNldFNjcm9sbGJhciA9IGZ1bmN0aW9uICgpIHtcclxuICAgIHZhciBib2R5UGFkID0gcGFyc2VJbnQoKHRoaXMuJGJvZHkuY3NzKCdwYWRkaW5nLXJpZ2h0JykgfHwgMCksIDEwKVxyXG4gICAgdGhpcy5vcmlnaW5hbEJvZHlQYWQgPSBkb2N1bWVudC5ib2R5LnN0eWxlLnBhZGRpbmdSaWdodCB8fCAnJ1xyXG4gICAgaWYgKHRoaXMuYm9keUlzT3ZlcmZsb3dpbmcpIHRoaXMuJGJvZHkuY3NzKCdwYWRkaW5nLXJpZ2h0JywgYm9keVBhZCArIHRoaXMuc2Nyb2xsYmFyV2lkdGgpXHJcbiAgfVxyXG5cclxuICBNb2RhbC5wcm90b3R5cGUucmVzZXRTY3JvbGxiYXIgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICB0aGlzLiRib2R5LmNzcygncGFkZGluZy1yaWdodCcsIHRoaXMub3JpZ2luYWxCb2R5UGFkKVxyXG4gIH1cclxuXHJcbiAgTW9kYWwucHJvdG90eXBlLm1lYXN1cmVTY3JvbGxiYXIgPSBmdW5jdGlvbiAoKSB7IC8vIHRoeCB3YWxzaFxyXG4gICAgdmFyIHNjcm9sbERpdiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpXHJcbiAgICBzY3JvbGxEaXYuY2xhc3NOYW1lID0gJ21vZGFsLXNjcm9sbGJhci1tZWFzdXJlJ1xyXG4gICAgdGhpcy4kYm9keS5hcHBlbmQoc2Nyb2xsRGl2KVxyXG4gICAgdmFyIHNjcm9sbGJhcldpZHRoID0gc2Nyb2xsRGl2Lm9mZnNldFdpZHRoIC0gc2Nyb2xsRGl2LmNsaWVudFdpZHRoXHJcbiAgICB0aGlzLiRib2R5WzBdLnJlbW92ZUNoaWxkKHNjcm9sbERpdilcclxuICAgIHJldHVybiBzY3JvbGxiYXJXaWR0aFxyXG4gIH1cclxuXHJcblxyXG4gIC8vIE1PREFMIFBMVUdJTiBERUZJTklUSU9OXHJcbiAgLy8gPT09PT09PT09PT09PT09PT09PT09PT1cclxuXHJcbiAgZnVuY3Rpb24gUGx1Z2luKG9wdGlvbiwgX3JlbGF0ZWRUYXJnZXQpIHtcclxuICAgIHJldHVybiB0aGlzLmVhY2goZnVuY3Rpb24gKCkge1xyXG4gICAgICB2YXIgJHRoaXMgICA9ICQodGhpcylcclxuICAgICAgdmFyIGRhdGEgICAgPSAkdGhpcy5kYXRhKCd3cGJjLm1vZGFsJylcclxuICAgICAgdmFyIG9wdGlvbnMgPSAkLmV4dGVuZCh7fSwgTW9kYWwuREVGQVVMVFMsICR0aGlzLmRhdGEoKSwgdHlwZW9mIG9wdGlvbiA9PSAnb2JqZWN0JyAmJiBvcHRpb24pXHJcblxyXG4gICAgICBpZiAoIWRhdGEpICR0aGlzLmRhdGEoJ3dwYmMubW9kYWwnLCAoZGF0YSA9IG5ldyBNb2RhbCh0aGlzLCBvcHRpb25zKSkpXHJcbiAgICAgIGlmICh0eXBlb2Ygb3B0aW9uID09ICdzdHJpbmcnKSBkYXRhW29wdGlvbl0oX3JlbGF0ZWRUYXJnZXQpXHJcbiAgICAgIGVsc2UgaWYgKG9wdGlvbnMuc2hvdykgZGF0YS5zaG93KF9yZWxhdGVkVGFyZ2V0KVxyXG4gICAgfSlcclxuICB9XHJcblxyXG4gIHZhciBvbGQgPSAkLmZuLndwYmNfbXlfbW9kYWxcclxuXHJcbiAgJC5mbi53cGJjX215X21vZGFsICAgICAgICAgICAgID0gUGx1Z2luXHJcbiAgJC5mbi53cGJjX215X21vZGFsLkNvbnN0cnVjdG9yID0gTW9kYWxcclxuXHJcblxyXG4gIC8vIE1PREFMIE5PIENPTkZMSUNUXHJcbiAgLy8gPT09PT09PT09PT09PT09PT1cclxuXHJcbiAgJC5mbi53cGJjX215X21vZGFsLm5vQ29uZmxpY3QgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAkLmZuLndwYmNfbXlfbW9kYWwgPSBvbGRcclxuICAgIHJldHVybiB0aGlzXHJcbiAgfVxyXG5cclxuXHJcbiAgLy8gTU9EQUwgREFUQS1BUElcclxuICAvLyA9PT09PT09PT09PT09PVxyXG5cclxuICAkKGRvY3VtZW50KS5vbignY2xpY2sud3BiYy5tb2RhbC5kYXRhLWFwaScsICdbZGF0YS10b2dnbGU9XCJ3cGJjX215X21vZGFsXCJdJywgZnVuY3Rpb24gKGUpIHtcclxuICAgIHZhciAkdGhpcyAgID0gJCh0aGlzKVxyXG4gICAgdmFyIGhyZWYgICAgPSAkdGhpcy5hdHRyKCdocmVmJylcclxuICAgIHZhciAkdGFyZ2V0ID0gJCgkdGhpcy5hdHRyKCdkYXRhLXRhcmdldCcpIHx8IChocmVmICYmIGhyZWYucmVwbGFjZSgvLiooPz0jW15cXHNdKyQpLywgJycpKSkgLy8gc3RyaXAgZm9yIGllN1xyXG4gICAgdmFyIG9wdGlvbiAgPSAkdGFyZ2V0LmRhdGEoJ3dwYmMubW9kYWwnKSA/ICd0b2dnbGUnIDogJC5leHRlbmQoeyByZW1vdGU6ICEvIy8udGVzdChocmVmKSAmJiBocmVmIH0sICR0YXJnZXQuZGF0YSgpLCAkdGhpcy5kYXRhKCkpXHJcblxyXG4gICAgaWYgKCR0aGlzLmlzKCdhJykpIGUucHJldmVudERlZmF1bHQoKVxyXG5cclxuICAgICR0YXJnZXQub25lKCdzaG93LndwYmMubW9kYWwnLCBmdW5jdGlvbiAoc2hvd0V2ZW50KSB7XHJcbiAgICAgIGlmIChzaG93RXZlbnQuaXNEZWZhdWx0UHJldmVudGVkKCkpIHJldHVybiAvLyBvbmx5IHJlZ2lzdGVyIGZvY3VzIHJlc3RvcmVyIGlmIG1vZGFsIHdpbGwgYWN0dWFsbHkgZ2V0IHNob3duXHJcbiAgICAgICR0YXJnZXQub25lKCdoaWRkZW4ud3BiYy5tb2RhbCcsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAkdGhpcy5pcygnOnZpc2libGUnKSAmJiAkdGhpcy50cmlnZ2VyKCdmb2N1cycpXHJcbiAgICAgIH0pXHJcbiAgICB9KVxyXG4gICAgUGx1Z2luLmNhbGwoJHRhcmdldCwgb3B0aW9uLCB0aGlzKVxyXG4gIH0pXHJcblxyXG59KGpRdWVyeSk7XHJcblxyXG5cclxuK2Z1bmN0aW9uICgkKSB7XHJcbiAgJ3VzZSBzdHJpY3QnO1xyXG5cclxuICAvLyBEUk9QRE9XTiBDTEFTUyBERUZJTklUSU9OXHJcbiAgLy8gPT09PT09PT09PT09PT09PT09PT09PT09PVxyXG5cclxuICB2YXIgYmFja2Ryb3AgPSAnLmRyb3Bkb3duLWJhY2tkcm9wJ1xyXG4gIHZhciB0b2dnbGUgICA9ICdbZGF0YS10b2dnbGU9XCJ3cGJjX2Ryb3Bkb3duXCJdJ1xyXG4gIHZhciBEcm9wZG93biA9IGZ1bmN0aW9uIChlbGVtZW50KSB7XHJcbiAgICAkKGVsZW1lbnQpLm9uKCdjbGljay53cGJjLmRyb3Bkb3duJywgdGhpcy50b2dnbGUpXHJcbiAgfVxyXG5cclxuICBEcm9wZG93bi5WRVJTSU9OID0gJzMuMy41J1xyXG5cclxuICBmdW5jdGlvbiBnZXRQYXJlbnQoJHRoaXMpIHtcclxuICAgIHZhciBzZWxlY3RvciA9ICR0aGlzLmF0dHIoJ2RhdGEtdGFyZ2V0JylcclxuXHJcbiAgICBpZiAoIXNlbGVjdG9yKSB7XHJcbiAgICAgIHNlbGVjdG9yID0gJHRoaXMuYXR0cignaHJlZicpXHJcbiAgICAgIHNlbGVjdG9yID0gc2VsZWN0b3IgJiYgLyNbQS1aYS16XS8udGVzdChzZWxlY3RvcikgJiYgc2VsZWN0b3IucmVwbGFjZSgvLiooPz0jW15cXHNdKiQpLywgJycpIC8vIHN0cmlwIGZvciBpZTdcclxuICAgIH1cclxuXHJcbiAgICB2YXIgJHBhcmVudCA9IHNlbGVjdG9yICYmICQoc2VsZWN0b3IpXHJcblxyXG4gICAgcmV0dXJuICRwYXJlbnQgJiYgJHBhcmVudC5sZW5ndGggPyAkcGFyZW50IDogJHRoaXMucGFyZW50KClcclxuICB9XHJcblxyXG4gIGZ1bmN0aW9uIGNsZWFyTWVudXMoZSkge1xyXG4gICAgaWYgKGUgJiYgZS53aGljaCA9PT0gMykgcmV0dXJuXHJcbiAgICAkKGJhY2tkcm9wKS5yZW1vdmUoKVxyXG4gICAgJCh0b2dnbGUpLmVhY2goZnVuY3Rpb24gKCkge1xyXG4gICAgICB2YXIgJHRoaXMgICAgICAgICA9ICQodGhpcylcclxuICAgICAgdmFyICRwYXJlbnQgICAgICAgPSBnZXRQYXJlbnQoJHRoaXMpXHJcbiAgICAgIHZhciByZWxhdGVkVGFyZ2V0ID0geyByZWxhdGVkVGFyZ2V0OiB0aGlzIH1cclxuXHJcbiAgICAgIGlmICghJHBhcmVudC5oYXNDbGFzcygnb3BlbicpKSByZXR1cm5cclxuXHJcbiAgICAgIGlmIChlICYmIGUudHlwZSA9PSAnY2xpY2snICYmIC9pbnB1dHx0ZXh0YXJlYS9pLnRlc3QoZS50YXJnZXQudGFnTmFtZSkgJiYgJC5jb250YWlucygkcGFyZW50WzBdLCBlLnRhcmdldCkpIHJldHVyblxyXG5cclxuICAgICAgJHBhcmVudC50cmlnZ2VyKGUgPSAkLkV2ZW50KCdoaWRlLndwYmMuZHJvcGRvd24nLCByZWxhdGVkVGFyZ2V0KSlcclxuXHJcbiAgICAgIGlmIChlLmlzRGVmYXVsdFByZXZlbnRlZCgpKSByZXR1cm5cclxuXHJcbiAgICAgICR0aGlzLmF0dHIoJ2FyaWEtZXhwYW5kZWQnLCAnZmFsc2UnKVxyXG4gICAgICAkcGFyZW50LnJlbW92ZUNsYXNzKCdvcGVuJykudHJpZ2dlcignaGlkZGVuLndwYmMuZHJvcGRvd24nLCByZWxhdGVkVGFyZ2V0KVxyXG4gICAgfSlcclxuICB9XHJcblxyXG4gIERyb3Bkb3duLnByb3RvdHlwZS50b2dnbGUgPSBmdW5jdGlvbiAoZSkge1xyXG4gICAgdmFyICR0aGlzID0gJCh0aGlzKVxyXG5cclxuICAgIGlmICgkdGhpcy5pcygnLmRpc2FibGVkLCA6ZGlzYWJsZWQnKSkgcmV0dXJuXHJcblxyXG4gICAgdmFyICRwYXJlbnQgID0gZ2V0UGFyZW50KCR0aGlzKVxyXG4gICAgdmFyIGlzQWN0aXZlID0gJHBhcmVudC5oYXNDbGFzcygnb3BlbicpXHJcblxyXG4gICAgY2xlYXJNZW51cygpXHJcblxyXG4gICAgaWYgKCFpc0FjdGl2ZSkge1xyXG4gICAgICBpZiAoJ29udG91Y2hzdGFydCcgaW4gZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50ICYmICEkcGFyZW50LmNsb3Nlc3QoJy5uYXZiYXItbmF2JykubGVuZ3RoKSB7XHJcbiAgICAgICAgLy8gaWYgbW9iaWxlIHdlIHVzZSBhIGJhY2tkcm9wIGJlY2F1c2UgY2xpY2sgZXZlbnRzIGRvbid0IGRlbGVnYXRlXHJcbiAgICAgICAgJChkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKSlcclxuICAgICAgICAgIC5hZGRDbGFzcygnZHJvcGRvd24tYmFja2Ryb3AnKVxyXG4gICAgICAgICAgLmluc2VydEFmdGVyKCQodGhpcykpXHJcbiAgICAgICAgICAub24oJ2NsaWNrJywgY2xlYXJNZW51cylcclxuICAgICAgfVxyXG5cclxuICAgICAgdmFyIHJlbGF0ZWRUYXJnZXQgPSB7IHJlbGF0ZWRUYXJnZXQ6IHRoaXMgfVxyXG4gICAgICAkcGFyZW50LnRyaWdnZXIoZSA9ICQuRXZlbnQoJ3Nob3cud3BiYy5kcm9wZG93bicsIHJlbGF0ZWRUYXJnZXQpKVxyXG5cclxuICAgICAgaWYgKGUuaXNEZWZhdWx0UHJldmVudGVkKCkpIHJldHVyblxyXG5cclxuICAgICAgJHRoaXNcclxuICAgICAgICAudHJpZ2dlcignZm9jdXMnKVxyXG4gICAgICAgIC5hdHRyKCdhcmlhLWV4cGFuZGVkJywgJ3RydWUnKVxyXG5cclxuICAgICAgJHBhcmVudFxyXG4gICAgICAgIC50b2dnbGVDbGFzcygnb3BlbicpXHJcbiAgICAgICAgLnRyaWdnZXIoJ3Nob3duLndwYmMuZHJvcGRvd24nLCByZWxhdGVkVGFyZ2V0KVxyXG4gICAgfVxyXG5cclxuICAgIHJldHVybiBmYWxzZVxyXG4gIH1cclxuXHJcbiAgRHJvcGRvd24ucHJvdG90eXBlLmtleWRvd24gPSBmdW5jdGlvbiAoZSkge1xyXG4gICAgaWYgKCEvKDM4fDQwfDI3fDMyKS8udGVzdChlLndoaWNoKSB8fCAvaW5wdXR8dGV4dGFyZWEvaS50ZXN0KGUudGFyZ2V0LnRhZ05hbWUpKSByZXR1cm5cclxuXHJcbiAgICB2YXIgJHRoaXMgPSAkKHRoaXMpXHJcblxyXG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpXHJcbiAgICBlLnN0b3BQcm9wYWdhdGlvbigpXHJcblxyXG4gICAgaWYgKCR0aGlzLmlzKCcuZGlzYWJsZWQsIDpkaXNhYmxlZCcpKSByZXR1cm5cclxuXHJcbiAgICB2YXIgJHBhcmVudCAgPSBnZXRQYXJlbnQoJHRoaXMpXHJcbiAgICB2YXIgaXNBY3RpdmUgPSAkcGFyZW50Lmhhc0NsYXNzKCdvcGVuJylcclxuXHJcbiAgICBpZiAoIWlzQWN0aXZlICYmIGUud2hpY2ggIT0gMjcgfHwgaXNBY3RpdmUgJiYgZS53aGljaCA9PSAyNykge1xyXG4gICAgICBpZiAoZS53aGljaCA9PSAyNykgJHBhcmVudC5maW5kKHRvZ2dsZSkudHJpZ2dlcignZm9jdXMnKVxyXG4gICAgICByZXR1cm4gJHRoaXMudHJpZ2dlcignY2xpY2snKVxyXG4gICAgfVxyXG5cclxuICAgIHZhciBkZXNjID0gJyBsaTpub3QoLmRpc2FibGVkKTp2aXNpYmxlIGEnXHJcbiAgICB2YXIgJGl0ZW1zID0gJHBhcmVudC5maW5kKCcuZHJvcGRvd24tbWVudScgKyBkZXNjICsgJywudWlfZHJvcGRvd25fbWVudScgKyBkZXNjKVxyXG5cclxuICAgIGlmICghJGl0ZW1zLmxlbmd0aCkgcmV0dXJuXHJcblxyXG4gICAgdmFyIGluZGV4ID0gJGl0ZW1zLmluZGV4KGUudGFyZ2V0KVxyXG5cclxuICAgIGlmIChlLndoaWNoID09IDM4ICYmIGluZGV4ID4gMCkgICAgICAgICAgICAgICAgIGluZGV4LS0gICAgICAgICAvLyB1cFxyXG4gICAgaWYgKGUud2hpY2ggPT0gNDAgJiYgaW5kZXggPCAkaXRlbXMubGVuZ3RoIC0gMSkgaW5kZXgrKyAgICAgICAgIC8vIGRvd25cclxuICAgIGlmICghfmluZGV4KSAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGluZGV4ID0gMFxyXG5cclxuICAgICRpdGVtcy5lcShpbmRleCkudHJpZ2dlcignZm9jdXMnKVxyXG4gIH1cclxuXHJcblxyXG4gIC8vIERST1BET1dOIFBMVUdJTiBERUZJTklUSU9OXHJcbiAgLy8gPT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuXHJcbiAgZnVuY3Rpb24gUGx1Z2luKG9wdGlvbikge1xyXG4gICAgcmV0dXJuIHRoaXMuZWFjaChmdW5jdGlvbiAoKSB7XHJcbiAgICAgIHZhciAkdGhpcyA9ICQodGhpcylcclxuICAgICAgdmFyIGRhdGEgID0gJHRoaXMuZGF0YSgnd3BiYy5kcm9wZG93bicpXHJcblxyXG4gICAgICBpZiAoIWRhdGEpICR0aGlzLmRhdGEoJ3dwYmMuZHJvcGRvd24nLCAoZGF0YSA9IG5ldyBEcm9wZG93bih0aGlzKSkpXHJcbiAgICAgIGlmICh0eXBlb2Ygb3B0aW9uID09ICdzdHJpbmcnKSBkYXRhW29wdGlvbl0uY2FsbCgkdGhpcylcclxuICAgIH0pXHJcbiAgfVxyXG5cclxuICB2YXIgb2xkID0gJC5mbi53cGJjX2Ryb3Bkb3duXHJcblxyXG4gICQuZm4ud3BiY19kcm9wZG93biAgICAgICAgICAgICA9IFBsdWdpblxyXG4gICQuZm4ud3BiY19kcm9wZG93bi5Db25zdHJ1Y3RvciA9IERyb3Bkb3duXHJcblxyXG5cclxuICAvLyBEUk9QRE9XTiBOTyBDT05GTElDVFxyXG4gIC8vID09PT09PT09PT09PT09PT09PT09XHJcblxyXG4gICQuZm4ud3BiY19kcm9wZG93bi5ub0NvbmZsaWN0ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgJC5mbi53cGJjX2Ryb3Bkb3duID0gb2xkXHJcbiAgICByZXR1cm4gdGhpc1xyXG4gIH1cclxuXHJcblxyXG4gIC8vIEFQUExZIFRPIFNUQU5EQVJEIERST1BET1dOIEVMRU1FTlRTXHJcbiAgLy8gPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxuXHJcbiAgJChkb2N1bWVudClcclxuICAgIC5vbignY2xpY2sud3BiYy5kcm9wZG93bi5kYXRhLWFwaScsIGNsZWFyTWVudXMpXHJcbiAgICAub24oJ2NsaWNrLndwYmMuZHJvcGRvd24uZGF0YS1hcGknLCAnLmRyb3Bkb3duIGZvcm0nLCBmdW5jdGlvbiAoZSkgeyBlLnN0b3BQcm9wYWdhdGlvbigpIH0pXHJcbiAgICAub24oJ2NsaWNrLndwYmMuZHJvcGRvd24uZGF0YS1hcGknLCB0b2dnbGUsIERyb3Bkb3duLnByb3RvdHlwZS50b2dnbGUpXHJcbiAgICAub24oJ2tleWRvd24ud3BiYy5kcm9wZG93bi5kYXRhLWFwaScsIHRvZ2dsZSwgRHJvcGRvd24ucHJvdG90eXBlLmtleWRvd24pXHJcbiAgICAub24oJ2tleWRvd24ud3BiYy5kcm9wZG93bi5kYXRhLWFwaScsICcuZHJvcGRvd24tbWVudScsIERyb3Bkb3duLnByb3RvdHlwZS5rZXlkb3duKVxyXG4gICAgLm9uKCdrZXlkb3duLndwYmMuZHJvcGRvd24uZGF0YS1hcGknLCAnLnVpX2Ryb3Bkb3duX21lbnUnLCBEcm9wZG93bi5wcm90b3R5cGUua2V5ZG93bilcclxuXHJcbn0oalF1ZXJ5KTtcclxuIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsSUFBSSxPQUFPQSxNQUFNLEtBQUssV0FBVyxFQUFFO0VBQ2pDLE1BQU0sSUFBSUMsS0FBSyxDQUFDLHlDQUF5QyxDQUFDO0FBQzVEO0FBQ0EsQ0FBQyxVQUFVQyxDQUFDLEVBQUU7RUFDWixZQUFZOztFQUNaLElBQUlDLE9BQU8sR0FBR0QsQ0FBQyxDQUFDRSxFQUFFLENBQUNDLE1BQU0sQ0FBQ0MsS0FBSyxDQUFDLEdBQUcsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDQSxLQUFLLENBQUMsR0FBRyxDQUFDO0VBQ2xELElBQUtILE9BQU8sQ0FBQyxDQUFDLENBQUMsR0FBRyxDQUFDLElBQUlBLE9BQU8sQ0FBQyxDQUFDLENBQUMsR0FBRyxDQUFDLElBQU1BLE9BQU8sQ0FBQyxDQUFDLENBQUMsSUFBSSxDQUFDLElBQUlBLE9BQU8sQ0FBQyxDQUFDLENBQUMsSUFBSSxDQUFDLElBQUlBLE9BQU8sQ0FBQyxDQUFDLENBQUMsR0FBRyxDQUFFLEVBQUU7SUFDaEcsTUFB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
