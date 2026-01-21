(function ($) {
	"use strict"

	$.fn.repeatable = function (userSettings) {

		/**
		 * Default settings
		 * @type {Object}
		 */
		var defaults = {
			addTrigger: ".add",
			deleteTrigger: ".delete",
			max: null,
      		min: 0,
			template: null,
			templateReplace: [],
			itemIndex: "new",
			itemContainer: ".field-group",
			countDefaultItem: true,
			beforeAdd: function () {},
			afterAdd: function (item) {},
			beforeDelete: function (item) {},
			afterDelete: function () {}
		};

		/**
		 * DOM element into which repeatable
		 * items will be added
		 * @type {jQuery object}
		 */
		var target = $(this);

		/**
		 * Blend passed user settings with defauly settings
		 * @type {array}
		 */
		var settings = $.extend({}, defaults, userSettings);

		/**
		 * Total templated items found on the page
		 * at load. These may be created by server-side
		 * scripts.
		 * @return null
		 */
		var total = function () {
			return $(target).find(settings.itemContainer).length;
		}();

		/**
		 * Iterator used to make each added
		 * repeatable element unique
		 * @type {Number}
		 */
		var i = (settings.countDefaultItem && total) ? total : 0;

		/**
		 * Add an element to the target
		 * and call the callback function
		 * @param  object e Event
		 * @return null
		 */

		var addOne = function (e) {
			e.preventDefault();
			if(settings.beforeAdd.call(this) !== false){
				var item = createOne();
				settings.afterAdd.call(this, item);
			}
		};

		/**
		 * Delete the parent element
		 * and call the callback function
		 * @param  object e Event
		 * @return null
		 */
		var deleteOne = function (e) {
			e.preventDefault();
			var item = $(this).parents(settings.itemContainer).first();
			if(settings.beforeDelete.call(this, item) !== false){
				if (total === settings.min) return;
				item.remove();
				total--;
				maintainAddBtn();
				settings.afterDelete.call(this);
			}
		};

		/**
		 * Add an element to the target
		 * @return null
		 */
		var createOne = function() {
			var item = getUniqueTemplate();
			item.appendTo(target);
			total++;
			maintainAddBtn();
			return item;
		};

		/**
		 * Escape regular expression characters
		 * @return string
		 */
		var escapeRegExp = function (str) {
			return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
		}

		/**
		 * Alter the given template to make
		 * each form field name unique
		 * @return {jQuery object}
		 */
		var getUniqueTemplate = function () {
			var template = $(settings.template).html();
			template = template.replace(/{\?}/g, (settings.itemIndex || '') + i++); 	// {?} => iterated placeholder
			template = template.replace(/\{[^\?\}]*\}/g, ""); 	// {valuePlaceholder} => ""
			if(Array.isArray(settings.templateReplace)){
				for(let item of settings.templateReplace){
					if(item.hasOwnProperty('key') && item.hasOwnProperty('value')){
						template = template.replace(new RegExp(escapeRegExp(item.key), 'g'), item.value);
					}
				}
			}

			return $(template);
		};

		/**
		 * Determines if the add trigger
		 * needs to be disabled
		 * @return null
		 */
		var maintainAddBtn = function () {
			if (!settings.max) {
				return;
			}

			if (total === settings.max) {
				$(settings.addTrigger).attr("disabled", "disabled");
			} else if (total < settings.max) {
				$(settings.addTrigger).removeAttr("disabled");
			}
		};

		/**
		 * Setup the repeater
		 * @return null
		 */
		(function () {
			$(document).on("click", settings.addTrigger, addOne);
			$("form").on("click", settings.deleteTrigger, deleteOne);

			if (!total) {
				var toCreate = settings.min - total;
				for (var j = 0; j < toCreate; j++) {
					createOne();
				}
			}

		})();
	};

})(jQuery);
