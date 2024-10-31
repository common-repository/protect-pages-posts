
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.buildwps.com/
 * @since      1.0.0
 *
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/js
 */

/**
 *
 * Javascript
 *
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/js
 * @author     Bwps <support@bwps.us>
 */

var templateurl = ajax_object.templateurl;



(function(window, $) {
    

    $(document).ready(function()
    {
        var bodyTag = $("body");
        bodyTag.append('<div class="modal"><!-- Place at bottom of page --></div>');

        //Check Event Post
        $(".check-protected-post").live("click", function()  {

            var $checkBox = $(this);
            var isPrevented = this.checked ? 1 : 0;

            // Get ID
            var postId = "#postId_" + this.id.split("checkProtected_")[1];
            postId = $(postId).val();


            bodyTag.addClass("loading");
            $.ajax({
                url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
                type: 'POST',
                data: {
                    action: 'protect_post',
                    id: postId, // this is the function in your functions.php that will be triggered
                    isPrevented: isPrevented
                },
                success: function(data) {
                    if (data.free_version)
                        {
                            alert(data.message);
                            $checkBox.prop('checked', false);
                            return;
                        }
                    var textProtectedUrl = "#protectedUrl_" + postId;
                    var regionProtect = "#regionProtect_" + postId;
                    if (isPrevented) {
                        $(textProtectedUrl).val(templateurl+"/" + data.message);
                        $(regionProtect).show();
                    } else
                    {
                        $(textProtectedUrl).val("");
                        $(regionProtect).hide();
                    }
                },
                error: function(error) {
                    console.log("Errors", error);
                    alert(error.responseText);
                },
                complete: function () {
                    bodyTag.removeClass("loading");
                }
            });
        });

        // Check Event Page
        $(".check-protected-page").live("click", function()  {
            var $checkBox = $(this);
            
            var bodyTag = $("body");
            bodyTag.append('<div class="modal"><!-- Place at bottom of page --></div>');

            var isPrevented = this.checked ? 1 : 0;

            // Get ID
            var pageId = "#pageId_" + this.id.split("checkProtected_")[1];
            pageId = $(pageId).val();

            bodyTag.addClass("loading");
            $.ajax({
                url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
                type: 'POST',
                data: {
                    action: 'protect_page',
                    id: pageId, // this is the function in your functions.php that will be triggered
                    isPrevented: isPrevented
                },
                success: function(data) {
                    
                    // Check prevent
                    if (data.free_version)
                        {
                            alert(data.message);
                            $checkBox.prop('checked', false);
                            return;
                        }
                    var textProtectedUrl = "#protectedUrl_" + pageId;
                    var regionProtect = "#regionProtect_" + pageId;
                    
                    if (isPrevented) {
                        $(textProtectedUrl).val(templateurl+"/" + data.message);
                        $(regionProtect).show();
                    } else
                    {
                        $(textProtectedUrl).val("");
                        $(regionProtect).hide();
                    }
                },
                error: function(error) {
                    console.log("Errors", error);
                    alert(error.responseText);
                },
                complete: function () {
                    bodyTag.removeClass("loading");
                }
            });
        });


    });



    // Copy to Clipboard Post
    $(".copy-to-clipboard-post").live('click', function(e) {
        // Get ID
        var postId = "#postId_" + this.id.split("copyToClipBoard_")[1];
        postId = $(postId).val();
        var textProtectedUrl = "protectedUrl_" + postId;

        var copyText = document.getElementById(textProtectedUrl);
        copyText.select();
        document.execCommand("Copy");

        event.preventDefault();

    });

        // Copy to Clipboard Post
    $(".copy-to-clipboard-page").live('click', function(e) {
        // Get ID
        var pageId = "#pageId_" + this.id.split("copyToClipBoard_")[1];
        pageId = $(pageId).val();
        var textProtectedUrl = "protectedUrl_" + pageId;

        var copyText = document.getElementById(textProtectedUrl);
        copyText.select();
        document.execCommand("Copy");

        event.preventDefault();

    });

})(window, jQuery);


