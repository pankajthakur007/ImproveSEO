/**
 * For posting module
 */
var max_posts = 0;
var determine = true;

function determineMaxPosts() {
	if (!determine) return;

	var locations = getUniqueLocations();
	
	var request_url = posting_ajax_vars.site_url+'/wp-admin/index.php?api=improveseo&action=count_posts';

	jQuery.post(request_url, {
		title: jQuery('#title').val(),
		content: jQuery.trim(tinymce.get('content') ? tinymce.get('content').getContent() : jQuery('#content').val()),
		custom_title: jQuery('#custom-title').val(),
		custom_description: jQuery('#custom-description').val(),
		custom_keywords: jQuery('#custom-keywords').val(),
		permalink: jQuery('input[name="permalink"]').val(),
		tags: jQuery('input[name="tags"]').val(),
		country: jQuery('#local-country').val(),
		locations: getUniqueLocations()
	}, function (response) {
		max_posts = response.posts;

		if (max_posts > 5000) {
			jQuery("#too-many-posts").show();
		} else {
			jQuery("#too-many-posts").hide();
		}
	}, 'json');
}

var geoRepeated = {};
var allowedLocalSEO = {country: 1, state: 0, stateshort: 0, city: 0, zip: 0};

function getUniqueLocations() {
	var locations = jQuery('#jstree').jstree(true).get_checked();
	var uniqueLocations = [];

	_.each(locations, function(location, i) {
		if (!geoRepeated[location]) uniqueLocations.push(location);
	});

	return uniqueLocations;
}

(function($) {

	// On Ready
	$(document).ready(function() {
		var $form = $('#poststuff').parent('form');
		ImproveSEOPopup.init('info');

		$('.notice-improveseo-dismiss').click(function(e){
			e.preventDefault();
			$(this).parent().slideUp(100, function() {	});
		});
		// Max Posts
		$form.find('#max-posts').change(function () {
			var value = parseInt($(this).val());
			if (value > 0) max_posts = value;
			determine = value == 0;
		});

		// Channel pages
		$('#enable_categorization').change(function () {
			if ($(this).is(':checked')) {
				$('#channel-howto').hide();
				$('#state-channel-page, #city-channel-page').attr('disabled', false);
			} else {
				if ($('#state-channel-page').is(':checked')) $('#state-channel-page').click();
				if ($('#city-channel-page').is(':checked')) $('#city-channel-page').click();

				$('#channel-howto').show();
				$('#state-channel-page, #city-channel-page').attr('disabled', true);
			}
		});

		$('#state-channel-page').change(function () {
			if ($(this).is(':checked')) $('#state-channel-page-wrap').show();
			else $('#state-channel-page-wrap').hide();
		});

		$('#city-channel-page').change(function () {
			if ($(this).is(':checked')) $('#city-channel-page-wrap').show();
			else $('#city-channel-page-wrap').hide();
		});

		// Permalink
		var is_permalink_saved = false;

		if ($('#permalink').text() != '@title') {
			is_permalink_saved = true;
		}

		$('#title').keyup(function() {
			if (is_permalink_saved) return;

			var title = $(this).val();
			if (!title) title = '@title';

			title = title.toLowerCase().replace(/\s/g, '-').replace(/[,]*/g, '');

			$('#permalink').text(title);
		});
		$('#edit-permalink').click(function () {
			$('#permalink, #edit-permalink').hide();
			$('#save-permalink, #cancel-permalink').show();

			$('<input/>').attr({
				type: 'text',
				'id': 'new-permalink'
			}).val($('#permalink').text()).insertAfter('#permalink');
		});
		$('#save-permalink').click(function () {
			is_permalink_saved = true;
			var permalink = $('#new-permalink').val();

			$('#permalink').text(permalink);
			$('input[name="permalink"]').val(permalink);

			$('#cancel-permalink').click();
		});
		$('#cancel-permalink').click(function () {
			$('#permalink, #edit-permalink').show();
			$('#new-permalink').remove();
			$('#save-permalink, #cancel-permalink').hide();
		});

		// Tags
		$('#add-tags').click(function () {
			var value = $('input[name="tags"]').val();
			var tags = value ? value.split(',') : [];
			var newTags = $('#tagsinput').val().split(',');

			if (!$('#tagsinput').val()) return;

			_.each(newTags, function (tag) {
				tag = $.trim(tag);

				if (_.indexOf(tags, tag) == -1) {
					tags.push(tag);

					var $tag = $('<span>\
						<a class="ntdelbutton" data-tag="'+ tag + '">X</a>\
						' + tag + '\
					</span>');

					$tag.find('a').click(function () {
						$tag.remove();

						tags = _.without(tags, $(this).data('tag'));
						$('input[name="tags"]').val(tags.join(','));
					});

					$tag.appendTo('#tags');
				}
			});

			$('input[name="tags"]').val(tags.join(','));
			$('#tagsinput').val('');
		});

		if ($('#tagsinput').val() != '') {
			$('#add-tags').click();
		}

		// On-Page SEO
		$('#on-page-seo').change(function() {
			if ($(this).is(':checked')) {
				$('#on-page-seo-wrap').show();
			} else {
				$('#on-page-seo-wrap').hide();
			}
		});

		// Schema
		$('#schema').change(function() {
			if ($(this).is(':checked')) {
				$('#schema-wrap').show();
			} else {
				$('#schema-wrap').hide();
			}
		});

		// Dripfeed
		$('#dripfeed-enabler').change(function() {
			if ($(this).is(':checked')) {
				$('#dripfeed-wrap').show();
			} else {
				$('#dripfeed-wrap').hide();
			}
		});

		// Image EXIF
		$('#exif-enabler').change(function() {
			if ($(this).is(':checked')) {
				$('#exif-wrap').show();
			} else {
				$('#exif-wrap').hide();
			}
		});

		// Local SEO
		$('#local-seo-enabler').change(function() {
			if ($(this).is(':checked')) {
				$('#local-seo-wrap').show();
			} else {
				$('#local-seo-wrap').hide();
			}
		});

		$('#local-country').change(function() {
			$('#jstree').jstree(true).refresh();
		});

		var geoCheckTimeout = null;

		$('#jstree')
			.on('select_node.jstree', function (e, data) {
				console.log("select: e:", e, " / data: ", data);
				var path = data.node.id.split('/');

				if (path.length == 1) {
					allowedLocalSEO['state']++;
					allowedLocalSEO['stateshort']++;
				}
				else if (path.length == 2) {
					allowedLocalSEO['state']++;
					allowedLocalSEO['stateshort']++;
					allowedLocalSEO['city']++;

					if (!geoRepeated[path[0]]) geoRepeated[path[0]] = 0;

					geoRepeated[ path[0] ]++;
				}
				else if (path.length == 3) {
					allowedLocalSEO['state']++;
					allowedLocalSEO['stateshort']++;
					allowedLocalSEO['city']++;
					allowedLocalSEO['zip']++;

					if (!geoRepeated[path[0]]) geoRepeated[path[0]] = 0;
					if (!geoRepeated[path[0] + '/' + path[1]]) geoRepeated[path[0] + '/' + path[1]] = 0;

					geoRepeated[ path[0] ]++;
					geoRepeated[ path[0] + '/' + path[1] ]++;
				}

				clearTimeout(geoCheckTimeout);
				geoCheckTimeout = setTimeout(function () {
					determineMaxPosts();
				}, 1200);
			})
			.on('deselect_node.jstree', function (e, data) {
				var path = data.node.id.split('/');

				if (path.length == 1) {
					allowedLocalSEO['state']--;
					allowedLocalSEO['stateshort']--;
				}
				else if (path.length == 2) {
					allowedLocalSEO['state']--;
					allowedLocalSEO['stateshort']--;
					allowedLocalSEO['city']--;

					geoRepeated[ path[0] ]--;
				}
				else if (path.length == 3) {
					allowedLocalSEO['state']--;
					allowedLocalSEO['stateshort']--;
					allowedLocalSEO['city']--;
					allowedLocalSEO['zip']--;

					geoRepeated[ path[0] ]--;
					geoRepeated[ path[0] + '/' + path[1] ]--;
				}

				clearTimeout(geoCheckTimeout);
				geoCheckTimeout = setTimeout(function () {
					determineMaxPosts();
				}, 1200);
			})
			.jstree({
				core: {
					'data': {
						url: function(node) {
							return posting_ajax_vars.site_url+'/wp-admin/index.php?api=improveseo&action=geo-tree&country='+ $('#local-country').val();
						},
						data: function(node) {
							return {id: node.id};
						}
					}
				},
				checkbox: {
					//three_state: false
				},
				plugins: ['checkbox', 'changed']
			}).bind("loaded.jstree", function (event, data) {
				// Local GEO Locations
				if (typeof local_geo_locations !== 'undefined') {
					_.each(local_geo_locations, function (location, index) {
						$('#jstree').jstree(true).select_node(location);
					});
				}
			});

		// Word AI
		if ($('#word-ai-pass').length && $('#word-ai-email').length) {
			$('#wp-content-media-buttons').append('\
				<button type="button" id="word-ai" class="button" data-editor="content">\
					Word AI\
				</button>\
			');
			$('#word-ai').click(function(e) {
				var text = $.trim(tinymce.get('content') ? tinymce.get('content').getContent() : $('#content').val());
				if (!text) return false;

				var email = $('#word-ai-email').val();
				var pass = $('#word-ai-pass').val();

				$(this).text('Processing..').attr('disabled', true);
				$.post('/index.php?api=improveseo&action=word-ai', {
					text: text,
					quality: 'Readable',
					email: email,
					pass: pass,
					output: 'json'
				}, 'json')
				.done(function(response) {
					if (response.status == 'Failure') {
						alert(response.error);
					} else {
						tinymce.get('content') ? tinymce.get('content').setContent(response.text) : $('#content').val(response.text);
					}
				})
				.always(function() {
					$(this).text('Word AI').attr('disabled', false);
				}.bind(this));
			});
		}

		// Form submission
		$form.submit(function(e) {
			// Check local seo tags
			var localSEOFields = ['title', 'content'];
			var localSEOHasError = false;
			var localSEOErrors = {};

			if ($form.find('#on-page-seo').is(':checked')) {
				localSEOFields = localSEOFields.concat(['custom_title', 'custom_description', 'custom_keywords']);
			}

			if ($form.find('#local-seo-enabler').is(':checked')) {
				var locations = $form.find('#jstree').jstree(true).get_checked();
				var local_country = $form.find('#local-country').val();

				if (parseInt($form.find('#max-posts').val()) > 0) {
					if (!confirm('There maybe more combinations in the local SEO area than you typed in max posts. Continue?')) {
						e.preventDefault();
					}
				}
			}

			$form.find('[local-seo-error]').remove();

			if (localSEOHasError) {
				$.each(localSEOErrors, function(field, errors) {
					var html = [];
					$.each(errors, function(i, error) {
						html.push('<span class="PostForm__error" local-seo-error>'+ error + '</span>');
					});

					$el = $('[name="'+ field + '"]');
					if (field == 'content') $el = $el.parent().parent();

					$el.after(html.join(''));
				});

				e.preventDefault();
				return;
			}

			$form.find('#local-geo-locations').remove();
			$form.find('#local-geo-data').remove();

			if ($form.find('#local-seo-enabler').is(':checked')) {
				$form.prepend('<input type="hidden" id="local-geo-locations" name="local_geo_locations" value=\''+ JSON.stringify(getUniqueLocations()) +'\'>');
			}

			if (max_posts > 5000) {
				if (!confirm('Your project contains more than 5,000 pages. While ImproveSEO can create hundreds of thousands of posts per project, it is recommended to split your project into multiple smaller projects if you are using shared hosting for maximum efficiency. VPS and dedicated server users can avoid this message.\n\nPress OK to continue or cancel to resume editing.')) {
					e.preventDefault();
				}
			}

			$form.find('#exif-locations').remove();

			if ($form.find('#exif-enabler').is(':checked')) {
				var exifLocations = [];

				_.each(ImageEXIF.selected, function(location) {
					exifLocations.push({
						address: location.address,
						location: location.location
					});
				});

				$form.prepend('<input type="hidden" id="exif-locations" name="exif_locations" value=\''+ JSON.stringify(exifLocations) + '\'>');
			}
		});

		if (location.search.match(/create_post/) || location.search.match(/edit_post/)) {
			// Detect changes
			// $form.find('#title, #custom-title, #custom-description, #custom-keywords, input[name="tags"], input[name="permalink"]').change(function () {
			// 	determineMaxPosts();
			// });

			$form.find('#title, #custom-title, #custom-description, #custom-keywords, input[name="tags"], input[name="permalink"]').blur(function () {
				determineMaxPosts();
			});

			// $('#content').change(function () {
			// 	determineMaxPosts();
			// });

			$('#content').blur(function () {
				determineMaxPosts();
			});

			$('#wpbody-content form').submit(function() {
				window.onbeforeunload = null;
			});

			$('#wpbody-content form').one('change', function() {
				window.onbeforeunload = function () {
					return 'The changes you made will be lost if you navigate away from this page.';
				}
			});
		}

	});

	
	
})(jQuery);
