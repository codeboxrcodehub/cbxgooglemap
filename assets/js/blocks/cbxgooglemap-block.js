'use strict';

(function (blocks, element, components, editor, ServerSideRender, blockEditor) {

    var el                = element.createElement,
        registerBlockType = blocks.registerBlockType,
        InspectorControls = blockEditor.InspectorControls,
        RangeControl      = components.RangeControl,
        Panel             = components.Panel,
        PanelBody         = components.PanelBody,
        PanelRow          = components.PanelRow,
        TextControl       = components.TextControl,
        TextareaControl   = components.TextareaControl,
        CheckboxControl   = components.CheckboxControl,
        RadioControl      = components.RadioControl,
        SelectControl     = components.SelectControl,
        ToggleControl     = components.ToggleControl,
        PanelColorPicker  = editor.PanelColorSettings,
        DateTimePicker    = components.DateTimePicker,
        HorizontalRule    = components.HorizontalRule,
        ExternalLink      = components.ExternalLink;

    var MediaUpload = wp.editor.MediaUpload;

    var iconEl = el('svg', {width: 20, height: 20},
        el('path', {d: "M8,0C4.687,0,2,2.687,2,6c0,3.854,4.321,8.663,5,9.398C7.281,15.703,7.516,16,8,16s0.719-0.297,1-0.602  C9.679,14.663,14,9.854,14,6C14,2.687,11.313,0,8,0z M8,10c-2.209,0-4-1.791-4-4s1.791-4,4-4s4,1.791,4,4S10.209,10,8,10z M8,4  C6.896,4,6,4.896,6,6s0.896,2,2,2s2-0.896,2-2S9.104,4,8,4z"})
    );

    registerBlockType('codeboxr/cbxgooglemap', {
        title: cbxgooglemap_block.block_title,
        icon: iconEl,
        category: cbxgooglemap_block.block_category,

        edit: function (props) {
            return [
                el(ServerSideRender, {
                    block: 'codeboxr/cbxgooglemap',
                    attributes: props.attributes,
                }),
                el(InspectorControls, {},
                    el(PanelBody, {title: cbxgooglemap_block.general_settings.title, initialOpen: true},

                        // Always show this field
                        el(SelectControl,
                            {
                                label: cbxgooglemap_block.general_settings.id,
                                options: cbxgooglemap_block.general_settings.id_options,
                                onChange: (value) => {
                                    props.setAttributes({id: value ? parseInt(value) : ''});
                                },
                                value: props.attributes.id
                            }
                        ),

                        // Conditionally show rest of fields only if id is not numeric
                        (!props.attributes.id || isNaN(props.attributes.id)) ? el('div', {},
                            [
                                el('p', {className: 'cbxgooglemap_block_note'}, cbxgooglemap_block.general_settings.id_note),
                                el('hr', {}),
                                el('h3', {className: 'cbxgooglemap_block_note_custom_attribute'}, cbxgooglemap_block.general_settings.custom_attribute_note),

                                el(SelectControl, {
                                    label: cbxgooglemap_block.general_settings.maptype,
                                    options: cbxgooglemap_block.general_settings.maptype_options,
                                    onChange: (value) => props.setAttributes({maptype: value}),
                                    value: props.attributes.maptype
                                }),

                                el(TextControl, {
                                    label: cbxgooglemap_block.general_settings.lat,
                                    onChange: (value) => props.setAttributes({lat: value}),
                                    value: props.attributes.lat
                                }),

                                el(TextControl, {
                                    label: cbxgooglemap_block.general_settings.lng,
                                    onChange: (value) => props.setAttributes({lng: value}),
                                    value: props.attributes.lng
                                }),

                                el(TextControl, {
                                    label: cbxgooglemap_block.general_settings.width,
                                    onChange: (value) => props.setAttributes({width: value}),
                                    value: props.attributes.width
                                }),

                                el(TextControl, {
                                    label: cbxgooglemap_block.general_settings.height,
                                    onChange: (value) => props.setAttributes({height: value}),
                                    value: props.attributes.height
                                }),

                                el(TextControl, {
                                    label: cbxgooglemap_block.general_settings.zoom,
                                    onChange: (value) => props.setAttributes({zoom: value}),
                                    value: props.attributes.zoom
                                }),

                                el(ToggleControl, {
                                    label: cbxgooglemap_block.general_settings.scrollwheel,
                                    onChange: (value) => props.setAttributes({scrollwheel: value}),
                                    checked: props.attributes.scrollwheel
                                }),

                                el(ToggleControl, {
                                    label: cbxgooglemap_block.general_settings.showinfo,
                                    onChange: (value) => props.setAttributes({showinfo: value}),
                                    checked: props.attributes.showinfo
                                }),

                                el(ToggleControl, {
                                    label: cbxgooglemap_block.general_settings.infow_open,
                                    onChange: (value) => props.setAttributes({infow_open: value}),
                                    checked: props.attributes.infow_open
                                }),

                                el(TextControl, {
                                    label: cbxgooglemap_block.general_settings.heading,
                                    onChange: (value) => props.setAttributes({heading: value}),
                                    value: props.attributes.heading
                                }),

                                el(TextControl, {
                                    label: cbxgooglemap_block.general_settings.address,
                                    onChange: (value) => props.setAttributes({address: value}),
                                    value: props.attributes.address
                                }),

                                el(TextControl, {
                                    label: cbxgooglemap_block.general_settings.website,
                                    onChange: (value) => props.setAttributes({website: value}),
                                    value: props.attributes.website
                                }),

                                el(MediaUpload, {
                                    label: cbxgooglemap_block.general_settings.mapicon,
                                    value: props.attributes.mapicon,
                                    onSelect: (media) => {
                                        props.setAttributes({mapicon: media.url});
                                    },
                                    type: 'image',
                                    render: function (obj) {
                                        return el(components.Button, {
                                                className: 'components-icon-button image-block-btn is-button is-default is-large',
                                                onClick: obj.open
                                            },
                                            el('svg', {className: 'dashicon dashicons-edit', width: '20', height: '20'},
                                                el('path', {d: "M2.25 1h15.5c.69 0 1.25.56 1.25 1.25v15.5c0 .69-.56 1.25-1.25 1.25H2.25C1.56 19 1 18.44 1 17.75V2.25C1 1.56 1.56 1 2.25 1zM17 17V3H3v14h14zM10 6c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2 2-.9 2-2zm3 5s0-6 3-6v10c0 .55-.45 1-1 1H5c-.55 0-1-.45-1-1V8c2 0 3 4 3 4s1-3 3-3 3 2 3 2z"})
                                            ),
                                            el('span', {}, cbxgooglemap_block.general_settings.mapicon_select)
                                        );
                                    }
                                })
                            ]
                        ) : null
                    )
                )
            ];
        },

        save: function () {
            return null;
        },
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.editor,
    window.wp.serverSideRender,
    window.wp.blockEditor
));