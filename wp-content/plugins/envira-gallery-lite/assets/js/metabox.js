/* ==========================================================
 * metabox.js
 * http://enviragallery.com/
 * ==========================================================
 * Copyright 2014 Thomas Griffin.
 *
 * Licensed under the GPL License, Version 2.0 or later (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */
;(function($){
    $(function(){
        // Initialize the slider tabs.
        var envira_tabs           = $('#envira-tabs'),
            envira_tabs_nav       = $('#envira-tabs-nav'),
            envira_tabs_hash      = window.location.hash,
            envira_tabs_hash_sani = window.location.hash.replace('!', '');

        // If we have a hash and it begins with "envira-tab", set the proper tab to be opened.
        if ( envira_tabs_hash && envira_tabs_hash.indexOf('envira-tab-') >= 0 ) {
            $('.envira-active').removeClass('envira-active');
            envira_tabs_nav.find('li a[href="' + envira_tabs_hash_sani + '"]').parent().addClass('envira-active');
            envira_tabs.find(envira_tabs_hash_sani).addClass('envira-active').show();

            // Update the post action to contain our hash so the proper tab can be loaded on save.
            var post_action = $('#post').attr('action');
            if ( post_action ) {
                post_action = post_action.split('#')[0];
                $('#post').attr('action', post_action + envira_tabs_hash);
            }
        }

        // Change tabs on click.
        $(document).on('click', '#envira-tabs-nav li a', function(e){
            e.preventDefault();
            var $this = $(this);
            if ( $this.parent().hasClass('envira-active') ) {
                return;
            } else {
                window.location.hash = envira_tabs_hash = this.hash.split('#').join('#!');
                var current = envira_tabs_nav.find('.envira-active').removeClass('envira-active').find('a').attr('href');
                $this.parent().addClass('envira-active');
                envira_tabs.find(current).removeClass('envira-active').hide();
                envira_tabs.find($this.attr('href')).addClass('envira-active').show();

                // Update the post action to contain our hash so the proper tab can be loaded on save.
                var post_action = $('#post').attr('action');
                if ( post_action ) {
                    post_action = post_action.split('#')[0];
                    $('#post').attr('action', post_action + envira_tabs_hash);
                }
            }
        });

        // Load plupload.
        var envira_uploader;
        enviraPlupload();

        // Conditionally show necessary fields.
        enviraConditionals();

        // Handle the meta icon helper.
        if ( 0 !== $('.envira-helper-needed').length ) {
            $('<div class="envira-meta-helper-overlay" />').prependTo('#envira-gallery');
        }

        $(document).on('click', '.envira-meta-icon', function(e){
            e.preventDefault();
            var $this     = $(this),
                container = $this.parent(),
                helper    = $this.next();
            if ( helper.is(':visible') ) {
                $('.envira-meta-helper-overlay').remove();
                container.removeClass('envira-helper-active');
            } else {
                if ( 0 === $('.envira-meta-helper-overlay').length ) {
                    $('<div class="envira-meta-helper-overlay" />').prependTo('#envira-gallery');
                }
                container.addClass('envira-helper-active');
            }
        });

        // Open up the media manager modal.
        $(document).on('click', '.envira-media-library', function(e){
            e.preventDefault();

            // Show the modal.
            envira_main_frame = true;
            $('#envira-gallery-upload-ui').appendTo('body').show();
        });

        // Add the selected state to images when selected from the library view.
        $('.envira-gallery-gallery').on('click', '.thumbnail, .check, .media-modal-icon', function(e){
            e.preventDefault();
            if ( $(this).parent().parent().hasClass('envira-gallery-in-gallery') )
                return;
            if ( $(this).parent().parent().hasClass('selected') )
                $(this).parent().parent().removeClass('details selected');
            else
                $(this).parent().parent().addClass('details selected');
        });

        // Load more images into the library view when the 'Load More Images from Library'
        // button is pressed
        $(document).on('click', 'a.envira-gallery-load-library', function(e){
            enviraLoadLibraryImages( $('a.envira-gallery-load-library').attr('data-envira-gallery-offset') );
        });

        // Load more images into the library view when the user scrolls to the bottom of the view
        // Honours any search term(s) specified
        $('.envira-gallery-gallery').bind('scroll', function() {
            if( $(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight ) {
                enviraLoadLibraryImages( $('a.envira-gallery-load-library').attr('data-envira-gallery-offset') );
            }
        });

        // Load images when the search term changes
        $(document).on('keyup keydown', '#envira-gallery-gallery-search', function() {
            delay(function() {
                enviraLoadLibraryImages( 0 );
            }); 
        });

        /**
        * Makes an AJAX call to get the next batch of images
        */
        function enviraLoadLibraryImages( offset ) {
            // Show spinner
            $('.media-toolbar-secondary span.envira-gallery-spinner').css('visibility','visible');

            // AJAX call to get next batch of images
            $.post(
                envira_gallery_metabox.ajax,
                {
                    action:  'envira_gallery_load_library',
                    offset:  offset,
                    post_id: envira_gallery_metabox.id,
                    search:  $('input#envira-gallery-gallery-search').val(),
                    nonce:   envira_gallery_metabox.load_gallery
                },
                function(response) {
                    // Update offset
                    $('a.envira-gallery-load-library').attr('data-envira-gallery-offset', ( Number(offset) + 20 ) );

                    // Hide spinner
                    $('.media-toolbar-secondary span.envira-gallery-spinner').css('visibility','hidden');

                    // Append the response data.
                    if ( offset === 0 ) {
                        // New search, so replace results
                        $('.envira-gallery-gallery').html( response.html );    
                    } else {
                        // Append to end of results
                        $('.envira-gallery-gallery').append( response.html );
                    }
                    
                },
                'json'
            );
        }

        // Process inserting slides into slider when the Insert button is pressed.
        $(document).on('click', '.envira-gallery-media-insert', function(e){
            e.preventDefault();
            var $this = $(this),
                text  = $(this).text(),
                data  = {
                    action: 'envira_gallery_insert_images',
                    nonce:   envira_gallery_metabox.insert_nonce,
                    post_id: envira_gallery_metabox.id,
                    images:  {}
                },
                selected = false,
                insert_e = e;
            $this.text(envira_gallery_metabox.inserting);

            // Loop through potential data to send when inserting images.
            // First, we loop through the selected items and add them to the data var.
            $('.envira-gallery-media-frame').find('.attachment.selected:not(.envira-gallery-in-gallery)').each(function(i, el){
                data.images[i] = $(el).attr('data-attachment-id');
                selected       = true;
            });

            // Send the ajax request with our data to be processed.
            $.post(
                envira_gallery_metabox.ajax,
                data,
                function(response){
                    // Set small delay before closing modal.
                    setTimeout(function(){
                        // Re-append modal to correct spot and revert text back to default.
                        append_and_hide(insert_e);
                        $this.text(text);

                        // If we have selected items, be sure to properly load first images back into view.
                        if ( selected )
                            $('.envira-gallery-load-library').attr('data-envira-gallery-offset', 0).addClass('has-search').trigger('click');
                    }, 500);
                },
                'json'
            );

        });

        // Make gallery items sortable.
        var gallery = $('#envira-gallery-output');

        // Use ajax to make the images sortable.
        gallery.sortable({
            containment: '#envira-gallery-output',
            items: 'li',
            cursor: 'move',
            forcePlaceholderSize: true,
            placeholder: 'dropzone',
            update: function(event, ui) {
                // Make ajax request to sort out items.
                var opts = {
                    url:      envira_gallery_metabox.ajax,
                    type:     'post',
                    async:    true,
                    cache:    false,
                    dataType: 'json',
                    data: {
                        action:  'envira_gallery_sort_images',
                        order:   gallery.sortable('toArray').toString(),
                        post_id: envira_gallery_metabox.id,
                        nonce:   envira_gallery_metabox.sort
                    },
                    success: function(response) {
                        return;
                    },
                    error: function(xhr, textStatus ,e) {
                        return;
                    }
                };
                $.ajax(opts);
            }
        });

        // Process image removal from a gallery.
        $('#envira-gallery').on('click', '.envira-gallery-remove-image', function(e){
            e.preventDefault();

            // Bail out if the user does not actually want to remove the image.
            var confirm_delete = confirm(envira_gallery_metabox.remove);
            if ( ! confirm_delete )
                return;

            // Prepare our data to be sent via Ajax.
            var attach_id = $(this).parent().attr('id'),
                remove = {
                    action:        'envira_gallery_remove_image',
                    attachment_id: attach_id,
                    post_id:       envira_gallery_metabox.id,
                    nonce:         envira_gallery_metabox.remove_nonce
                };

            // Process the Ajax response and output all the necessary data.
            $.post(
                envira_gallery_metabox.ajax,
                remove,
                function(response) {
                    $('#' + attach_id).fadeOut('normal', function() {
                        $(this).remove();

                        // Refresh the modal view to ensure no items are still checked if they have been removed.
                        $('.envira-gallery-load-library').attr('data-envira-gallery-offset', 0).addClass('has-search').trigger('click');
                    });
                },
                'json'
            );
        });

        // Open up the media modal area for modifying gallery metadata when clicking the info icon
        $('#envira-gallery').on('click', '.envira-gallery-modify-image', function(e){
            e.preventDefault();
            var attach_id = $(this).parent().data('envira-gallery-image'),
                formfield = 'envira-gallery-meta-' + attach_id;
            
            // Open modal
            openModal(attach_id, formfield);    
        });
        
        // Open modal
        var modal;
        var openModal = function(attach_id, formfield) {
	        
            // Show the modal.
            modal = $('#' + formfield).appendTo('body');
            $(modal).show();
            
	        // Close modal on close button or background click
	        $(document).on('click', '.media-modal-close, .media-modal-backdrop', function(e) {
	            e.preventDefault();
	            closeModal();
	        });
	        
	        // Close modal on esc keypress
	        $(document).on('keydown', function(e) {
	            if ( 27 == e.keyCode ) {
		        	closeModal();    
	            }
	        });
        }
        
        // Close modal
        var closeModal = function() {
	        // Get modal
			var formfield = $(modal).attr('id');
			var formfieldArr = formfield.split('-');
			var attach_id = formfieldArr[(formfieldArr.length-1)];
            	
            // Close modal
	        $('#' + formfield).appendTo('#' + attach_id).hide();
        }
        
        // Save the gallery metadata.
        $(document).on('click', '.envira-gallery-meta-submit', function(e){
            e.preventDefault();
            var $this     = $(this),
                default_t = $this.text(),
                attach_id = $this.data('envira-gallery-item'),
                formfield = 'envira-gallery-meta-' + attach_id,
                meta      = {};

            // Output saving text...
            $this.text(envira_gallery_metabox.saving);

            // Add the title since it is a special field.
            meta.title = $('#envira-gallery-meta-table-' + attach_id).find('textarea[name="_envira_gallery[meta_title]"]').val();

            // Get all meta fields and values.
            $('#envira-gallery-meta-table-' + attach_id).find(':input').not('.ed_button').each(function(i, el){
                if ( $(this).data('envira-meta') )
                    meta[$(this).data('envira-meta')] = $(this).val();
            });

            // Prepare the data to be sent.
            var data = {
                action:    'envira_gallery_save_meta',
                nonce:     envira_gallery_metabox.save_nonce,
                attach_id: attach_id,
                post_id:   envira_gallery_metabox.id,
                meta:      meta
            };

            $.post(
                envira_gallery_metabox.ajax,
                data,
                function(res){
                    setTimeout(function(){
                        $('#' + formfield).appendTo('#' + attach_id).hide();
                        $this.text(default_t);
                    }, 500);
                },
                'json'
            );
        });

        // Append spinner when importing a gallery.
        $('#envira-gallery-import-submit').on('click', function(e){
            $(this).next().css('display', 'inline-block');
            if ( $('#envira-config-import-gallery').val().length === 0 ) {
                e.preventDefault();
                $(this).next().hide();
                alert(envira_gallery_metabox.import);
            }
        });

        // Polling function for typing and other user centric items.
        var delay = (function() {
            var timer = 0;
            return function(callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();

        // Close the modal window on user action.
        var envira_main_frame = false;
        var append_and_hide = function(e){
            e.preventDefault();
            $('#envira-gallery-upload-ui').appendTo('#envira-gallery-upload-ui-wrapper').hide();
            enviraRefresh();
            envira_main_frame = false;
        };
        $(document).on('click', '#envira-gallery-upload-ui .media-modal-close, #envira-gallery-upload-ui .media-modal-backdrop', append_and_hide);
        $(document).on('keydown', function(e){
            if ( 27 == e.keyCode && envira_main_frame )
                append_and_hide(e);
        });

        // Function to refresh images in the gallery.
        function enviraRefresh(){
            var data = {
                action:  'envira_gallery_refresh',
                post_id: envira_gallery_metabox.id,
                nonce:   envira_gallery_metabox.refresh_nonce
            };

            $('.envira-media-library').after('<span class="spinner envira-gallery-spinner envira-gallery-spinner-refresh"></span>');
            $('.envira-gallery-spinner-refresh').css({'display' : 'inline-block', 'margin-top' : '-3px'});

            $.post(
                envira_gallery_metabox.ajax,
                data,
                function(res){
                    if ( res && res.success ) {
                        $('#envira-gallery-output').html(res.success);
                        $('#envira-gallery-output').find('.wp-editor-wrap').each(function(i, el){
                            var qt = $(el).find('.quicktags-toolbar');
                            if ( qt.length > 0 ) {
                                return;
                            }

                            var arr = $(el).attr('id').split('-'),
                                id  = arr.slice(4, -1).join('-');
                            quicktags({id: 'envira-gallery-caption-' + id, buttons: 'strong,em,link,ul,ol,li,close'});
                            QTags._buttonsInit(); // Force buttons to initialize.
                        });

                        // Trigger a custom event for 3rd party scripts.
                        $('#envira-gallery-output').trigger({ type: 'enviraRefreshed', html: res.success, id: envira_gallery_metabox.id });
                    }

                    // Remove the spinner.
                    $('.envira-gallery-spinner-refresh').fadeOut(300, function(){
                        $(this).remove();
                    });
                },
                'json'
            );
        }

        // Function to show conditional fields.
        function enviraConditionals() {
            var envira_crop_option    = $('#envira-config-crop'),
                envira_mobile_option  = $('#envira-config-mobile'),
                envira_toolbar_option = $('#envira-config-lightbox-toolbar');
            if ( envira_crop_option.is(':checked') )
                $('#envira-config-crop-size-box').fadeIn(300);
            envira_crop_option.on('change', function(){
                if ( $(this).is(':checked') )
                    $('#envira-config-crop-size-box').fadeIn(300);
                else
                    $('#envira-config-crop-size-box').fadeOut(300);
            });
            if ( envira_mobile_option.is(':checked') )
                $('#envira-config-mobile-size-box').fadeIn(300);
            envira_mobile_option.on('change', function(){
                if ( $(this).is(':checked') )
                    $('#envira-config-mobile-size-box').fadeIn(300);
                else
                    $('#envira-config-mobile-size-box').fadeOut(300);
            });
            if ( envira_toolbar_option.is(':checked') )
                $('#envira-config-lightbox-toolbar-position-box').fadeIn(300);
            envira_toolbar_option.on('change', function(){
                if ( $(this).is(':checked') )
                    $('#envira-config-lightbox-toolbar-position-box').fadeIn(300);
                else
                    $('#envira-config-lightbox-toolbar-position-box').fadeOut(300);
            });
        }

        // Function to initialize plupload.
        function enviraPlupload() {
            // Append the custom loading progress bar.
            $('#envira-gallery .drag-drop-inside').append('<div class="envira-progress-bar"><div></div></div>');

            // Prepare variables.
            envira_uploader     = new plupload.Uploader(envira_gallery_metabox.plupload);
            var envira_bar      = $('#envira-gallery .envira-progress-bar'),
                envira_progress = $('#envira-gallery .envira-progress-bar div'),
                envira_output   = $('#envira-gallery-output');

            // Only move forward if the uploader is present.
            if ( envira_uploader ) {
                // Append a link to use images from the user's media library.
                $('#envira-gallery .max-upload-size').append(' <a class="envira-media-library button button-primary" href="#" title="' + envira_gallery_metabox.gallery + '" style="vertical-align: baseline;">' + envira_gallery_metabox.gallery + '</a>');

                envira_uploader.bind('Init', function(up) {
                    var uploaddiv = $('#envira-gallery-plupload-upload-ui');

                    // If drag and drop, make that happen.
                    if ( up.features.dragdrop && ! $(document.body).hasClass('mobile') ) {
                        uploaddiv.addClass('drag-drop');
                        $('#envira-gallery-drag-drop-area').bind('dragover.wp-uploader', function(){
                            uploaddiv.addClass('drag-over');
                        }).bind('dragleave.wp-uploader, drop.wp-uploader', function(){
                            uploaddiv.removeClass('drag-over');
                        });
                    } else {
                        uploaddiv.removeClass('drag-drop');
                        $('#envira-gallery-drag-drop-area').unbind('.wp-uploader');
                    }

                    // If we have an HTML4 runtime, hide the flash bypass.
                    if ( up.runtime == 'html4' )
                        $('.upload-flash-bypass').hide();
                });

                // Initialize the uploader.
                envira_uploader.init();

                // Bind to the FilesAdded event to show the progess bar.
                envira_uploader.bind('FilesAdded', function(up, files){
                    var hundredmb = 100 * 1024 * 1024,
                        max       = parseInt(up.settings.max_file_size, 10);

                    // Remove any errors.
                    $('#envira-gallery-upload-error').html('');

                    // Show the progress bar.
                    $(envira_bar).show().css('display', 'block');

                    // Upload the files.
                    plupload.each(files, function(file){
                        if ( max > hundredmb && file.size > hundredmb && up.runtime != 'html5' ) {
                            enviraUploadError( up, file, true );
                        }
                    });

                    // Refresh and start.
                    up.refresh();
                    up.start();
                });

                // Bind to the UploadProgress event to manipulate the progress bar.
                envira_uploader.bind('UploadProgress', function(up, file){
                    $(envira_progress).css('width', up.total.percent + '%');
                });

                // Bind to the FileUploaded event to set proper UI display for slider.
                envira_uploader.bind('FileUploaded', function(up, file, info){
                    // Make an ajax request to generate and output the image in the slider UI.
                    $.post(
                        envira_gallery_metabox.ajax,
                        {
                            action:  'envira_gallery_load_image',
                            nonce:   envira_gallery_metabox.load_image,
                            id:      info.response,
                            post_id: envira_gallery_metabox.id
                        },
                        function(res){
                            $(envira_output).append(res);
                            $(res).find('.wp-editor-container').each(function(i, el){
                                var id = $(el).attr('id').split('-')[4];
                                quicktags({id: 'envira-gallery-caption-' + id, buttons: 'strong,em,link,ul,ol,li,close'});
                                QTags._buttonsInit(); // Force buttons to initialize.
                            });
                        },
                        'json'
                    );
                });

                // Bind to the UploadComplete event to hide and reset the progress bar.
                envira_uploader.bind('UploadComplete', function(){
                    $(envira_bar).hide().css('display', 'none');
                    $(envira_progress).removeAttr('style');
                });

                // Bind to any errors and output them on the screen.
                envira_uploader.bind('Error', function(up, error) {
                    var hundredmb = 100 * 1024 * 1024,
                        error_el  = $('#envira-gallery-upload-error'),
                        max;
                    switch (error) {
                        case plupload.FAILED:
                        case plupload.FILE_EXTENSION_ERROR:
                            error_el.html('<p class="error">' + pluploadL10n.upload_failed + '</p>');
                            break;
                        case plupload.FILE_SIZE_ERROR:
                            enviraUploadError(up, error.file);
                            break;
                        case plupload.IMAGE_FORMAT_ERROR:
                            wpFileError(fileObj, pluploadL10n.not_an_image);
                            break;
                        case plupload.IMAGE_MEMORY_ERROR:
                            wpFileError(fileObj, pluploadL10n.image_memory_exceeded);
                            break;
                        case plupload.IMAGE_DIMENSIONS_ERROR:
                            wpFileError(fileObj, pluploadL10n.image_dimensions_exceeded);
                            break;
                        case plupload.GENERIC_ERROR:
                            wpQueueError(pluploadL10n.upload_failed);
                            break;
                        case plupload.IO_ERROR:
                            max = parseInt(uploader.settings.max_file_size, 10);

                            if ( max > hundredmb && fileObj.size > hundredmb )
                                wpFileError(fileObj, pluploadL10n.big_upload_failed.replace('%1$s', '<a class="uploader-html" href="#">').replace('%2$s', '</a>'));
                            else
                                wpQueueError(pluploadL10n.io_error);
                            break;
                        case plupload.HTTP_ERROR:
                            wpQueueError(pluploadL10n.http_error);
                            break;
                        case plupload.INIT_ERROR:
                            $('.media-upload-form').addClass('html-uploader');
                            break;
                        case plupload.SECURITY_ERROR:
                            wpQueueError(pluploadL10n.security_error);
                            break;
                        default:
                            enviraUploadError(up, error.file);
                            break;
                    }
                    up.refresh();
                });
            }
        }

        // Function for displaying file upload errors.
        function enviraUploadError( up, file, over100mb ) {
            var message;

            if ( over100mb ) {
                message = pluploadL10n.big_upload_queued.replace('%s', file.name) + ' ' + pluploadL10n.big_upload_failed.replace('%1$s', '<a class="uploader-html" href="#">').replace('%2$s', '</a>');
            } else {
                message = pluploadL10n.file_exceeds_size_limit.replace('%s', file.name);
            }

            $('#envira-gallery-upload-error').html('<div class="error fade"><p>' + message + '</p></div>');
            up.removeFile(file);
        }
    });
}(jQuery));