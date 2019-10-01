/**
 * @file js/BlogFormHandler.js
 *
 * @package plugins.generic.blog
 * @class BlogFormHandler
 *
 * @brief blog form handler.
 */
(function($) {

	/** @type {Object} */
	$.pkp.controllers.form.blog =
			$.pkp.controllers.form.blog || { };



	/**
	 * @constructor
	 *
	 * @extends $.pkp.controllers.form.AjaxFormHandler
	 *
	 * @param {jQueryObject} $formElement A wrapped HTML element that
	 *  represents the approved proof form interface element.
	 * @param {Object} options Tabbed modal options.
	 */
	$.pkp.controllers.form.blog.BlogFormHandler =
			function($formElement, options) {
		this.parent($formElement, options);
	};
	$.pkp.classes.Helper.inherits(
			$.pkp.controllers.form.blog.BlogFormHandler,
			$.pkp.controllers.form.AjaxFormHandler
	);


	//
	// Private methods.
	//
	/**
	 * Callback triggered on clicking the "preview" button to open a preview window.
	 *
	 * @param {HTMLElement} submitButton The submit button.
	 * @param {Event} event The event that triggered the
	 *  submit button.
	 * @return {boolean} true.
	 * @private
	 */
	$.pkp.controllers.form.blog.BlogFormHandler.
			prototype.showPreview_ = function(submitButton, event) {

		var $formElement = this.getHtmlElement();
		$.post(this.previewUrl_,
				$formElement.serialize(),
				function(data) {
					var win = window.open('about:blank');
					with(win.document) {
						open();
						write(data);
						close();
					}
				});
		return true;
	};
/** @param {jQuery} $ jQuery closure. */
}(jQuery));
