/* ==========================================================
 * editor.js
 * http://enviragallery.com/
 *
 * This file can be used by 3rd party plugins to integrate
 * with their custom field systems. It allows the selection
 * process to be standardized so that 3rd party plugins can
 * trigger modal selection windows and receive the corresponding
 * selected data objects.
 *
 * Using this file requires three actions for the 3rd party plugin.
 *
 * 1. The media modal HTML output needs to be inserted directly
 *    after the option/dropdown/button that is to be used to
 *    trigger the modal. This can be done by placing the following
 *    code after the output (first to return, latter to echo):
 *
 *    Envira_Gallery_Editor::get_instance()->gallery_selection_modal();
 *
 * 2. This file should be enqueued on the page where the field resides.
 *    You should add the class ".envira-gallery-modal-trigger" to the
 *    option/dropdown/button that will trigger the modal. This will
 *    be used as a reference point for showing, hiding and passing data
 *    between the modal and your plugin.
 *
 * 3. Attaching to a global event that is fired once the data for the
 *    selection has been retrieved. You should listen on the document
 *    object for the "enviraGalleryModalData" event, like this:
 *
 *    $(document).on("enviraGalleryModalData", function(e){
 *        console.log(e.gallery);
 *    });
 *
 *    This will give you access to the entire array of gallery data that
 *    the user has selected, including ID, title, slug and settings.
 * ==========================================================
 * Copyright 2013 Thomas Griffin.
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
        // Close the modal window on user action.
        var envira_trigger_target  = envira_editor_frame = false;
        var envira_append_and_hide = function(e){
            e.preventDefault();
            $('.envira-gallery-default-ui .selected').removeClass('details selected');
            $('.envira-gallery-default-ui').appendTo('.envira-gallery-default-ui-wrapper').hide();
            envira_trigger_target = envira_editor_frame = false;
        };

        $(document).on('click', '.envira-gallery-choose-gallery, .envira-gallery-modal-trigger', function(e){
            e.preventDefault();

            // Store the trigger target.
            envira_trigger_target = e.target;

            // Show the modal.
            envira_editor_frame = true;
            $('.envira-gallery-default-ui').appendTo('body').show();

            $(document).on('click', '.media-modal-close, .media-modal-backdrop, .envira-gallery-cancel-insertion', envira_append_and_hide);
            $(document).on('keydown', function(e){
                if ( 27 == e.keyCode && envira_editor_frame ) {
                    envira_append_and_hide(e);
                }
            });
        });

        $(document).on('click', '.envira-gallery-default-ui .thumbnail, .envira-gallery-default-ui .check, .envira-gallery-default-ui .media-modal-icon', function(e){
            e.preventDefault();
            if ( $(this).parent().parent().hasClass('selected') ) {
                $(this).parent().parent().removeClass('details selected');
                $('.envira-gallery-insert-gallery').attr('disabled', 'disabled');
            } else {
                $(this).parent().parent().parent().find('.selected').removeClass('details selected');
                $(this).parent().parent().addClass('details selected');
                $('.envira-gallery-insert-gallery').removeAttr('disabled');
            }
        });

        $(document).on('click', '.envira-gallery-default-ui .check', function(e){
            e.preventDefault();
            $(this).parent().parent().removeClass('details selected');
            $('.envira-gallery-insert-gallery').attr('disabled', 'disabled');
        });

        $(document).on('click', '.envira-gallery-default-ui .envira-gallery-insert-gallery', function(e){
            e.preventDefault();

            // Either insert into an editor or make an ajax request.
            if ( $(envira_trigger_target).hasClass('envira-gallery-choose-gallery') ) {
                wp.media.editor.insert('[envira-gallery id="' + $('.envira-gallery-default-ui .selected').data('envira-gallery-id') + '"]');
            } else {
                // Make the ajax request.
                var req_data = {
                    action: 'envira_gallery_load_gallery_data',
                    id:     $('.envira-gallery-default-ui:first .selected').data('envira-gallery-id')
                };
                $.post(ajaxurl, req_data, function(res){
                    // Trigger the event.
                    $(document).trigger({ type: 'enviraGalleryModalData', gallery: res });

                    // Close the modal.
                    envira_append_and_hide(e);
                }, 'json');
            }

            // Hide the modal.
            envira_append_and_hide(e);
        });
    });
}(jQuery));