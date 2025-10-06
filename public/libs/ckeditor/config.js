/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */
var baseUrl = document.location.origin;
CKEDITOR.editorConfig = function(config) {
    config.filebrowserBrowseUrl =
        baseUrl + "/libs/ckeditor/plugins/ckfinder/ckfinder.html";

    config.filebrowserImageBrowseUrl =
        baseUrl + "/libs/ckeditor/plugins/ckfinder/ckfinder.html?type=Images";

    config.filebrowserFlashBrowseUrl =
        baseUrl + "/libs/ckeditor/plugins/ckfinder/ckfinder.html?type=Flash";

    config.filebrowserUploadUrl =
        baseUrl +
        "/libs/ckeditor/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files";

    config.filebrowserImageUploadUrl =
        baseUrl +
        "/libs/ckeditor/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images";

    config.filebrowserFlashUploadUrl =
        baseUrl +
        "/libs/ckeditor/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash";
};
