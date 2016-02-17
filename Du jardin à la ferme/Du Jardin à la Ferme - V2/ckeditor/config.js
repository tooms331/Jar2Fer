/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
    

    config.toolbar = [
        { name: 'styles', items: ['Styles'] },
        { name: 'colors', items: ['TextColor', 'BGColor'] },
		{ name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
		{ name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
		//{ name: 'insert', items: ['Smiley', 'Table'] },
		//{ name: 'links', items: ['Link', 'Unlink'] },
		{ name: 'editing', items: ['Find', 'Replace', '-', 'Scayt'] }
    ];

    config.scayt_autoStartup = true;
    config.language = 'fr';
    config.stylesSet = 'Main';

    //config.extraPlugins = 'stylesheetparser';
    //config.contentsCss = '../content.css';
};
